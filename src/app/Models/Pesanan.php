<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Pesanan extends Model
{
    public const STATUS_FLOW = [
        'menunggu_konfirmasi',
        'menunggu_proses',
        'sedang_dicuci',
        'sedang_dikeringkan',
        'sedang_disetrika',
        'siap_diambil',
        'selesai',
    ];

    public const STATUS_LABELS = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'menunggu_proses' => 'Menunggu Proses',
        'sedang_dicuci' => 'Sedang Dicuci',
        'sedang_dikeringkan' => 'Sedang Dikeringkan',
        'sedang_disetrika' => 'Sedang Disetrika',
        'siap_diambil' => 'Siap Diambil',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'pelanggan_id',
        'nomor_pesanan',
        'tanggal_masuk',
        'estimasi_selesai',
        'tanggal_siap_diambil',
        'tanggal_selesai',
        'metode_penyerahan',
        'alamat_penjemputan',
        'catatan_pelanggan',
        'catatan_admin',
        'status_pesanan',
        'status_pembayaran',
        'subtotal',
        'diskon',
        'total_biaya',
        'urutan_antrian',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pesanan $pesanan): void {
            $pesanan->nomor_pesanan ??= static::generateNomorPesanan();
            $pesanan->tanggal_masuk ??= now();
            $pesanan->status_pesanan ??= 'menunggu_konfirmasi';
            $pesanan->status_pembayaran ??= 'belum_dibayar';

            if ($pesanan->status_pesanan !== 'menunggu_konfirmasi' && ! $pesanan->urutan_antrian) {
                $pesanan->urutan_antrian = static::nextQueueNumber();
            }
        });

        static::updated(function (Pesanan $pesanan): void {
            if ($pesanan->wasChanged('status_pesanan')) {
                $pesanan->recordStatusHistory(
                    $pesanan->getOriginal('status_pesanan'),
                    $pesanan->status_pesanan
                );

                if ($pesanan->status_pesanan === 'siap_diambil' && ! $pesanan->tanggal_siap_diambil) {
                    $pesanan->forceFill(['tanggal_siap_diambil' => now()])->saveQuietly();
                }

                if ($pesanan->status_pesanan === 'selesai' && ! $pesanan->tanggal_selesai) {
                    $pesanan->forceFill(['tanggal_selesai' => now()])->saveQuietly();
                }

                $pesanan->syncPengingatPengambilan();
            }
        });
    }

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'estimasi_selesai' => 'datetime',
        'tanggal_siap_diambil' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'urutan_antrian' => 'integer',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    public function pengingatPengambilan(): HasOne
    {
        return $this->hasOne(PengingatPengambilan::class);
    }

    public function arusKas(): HasMany
    {
        return $this->hasMany(ArusKas::class);
    }

    public static function statusOptions(): array
    {
        return self::STATUS_LABELS;
    }

    public static function generateNomorPesanan(?Carbon $date = null): string
    {
        $date ??= now();
        $prefix = 'LDR-' . $date->format('Ymd') . '-';

        $lastNumber = static::where('nomor_pesanan', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('nomor_pesanan')
            ->value('nomor_pesanan');

        $sequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function nextQueueNumber(): int
    {
        return ((int) static::max('urutan_antrian')) + 1;
    }

    public function nextStatus(): ?string
    {
        if ($this->status_pesanan === 'dibatalkan') {
            return null;
        }

        $index = array_search($this->status_pesanan, self::STATUS_FLOW, true);

        if ($index === false || $index === count(self::STATUS_FLOW) - 1) {
            return null;
        }

        return self::STATUS_FLOW[$index + 1];
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        if ($targetStatus === 'dibatalkan') {
            return in_array($this->status_pesanan, ['menunggu_konfirmasi', 'menunggu_proses'], true);
        }

        return $this->nextStatus() === $targetStatus;
    }

    public function updateStatus(string $targetStatus, ?User $user = null, ?string $catatan = null): bool
    {
        if (! $this->canTransitionTo($targetStatus)) {
            return false;
        }

        $this->forceFill([
            'status_pesanan' => $targetStatus,
            'urutan_antrian' => $this->urutan_antrian ?: static::nextQueueNumber(),
            'tanggal_siap_diambil' => $targetStatus === 'siap_diambil'
                ? ($this->tanggal_siap_diambil ?: now())
                : $this->tanggal_siap_diambil,
            'tanggal_selesai' => $targetStatus === 'selesai'
                ? ($this->tanggal_selesai ?: now())
                : $this->tanggal_selesai,
            'catatan_admin' => $catatan ?: $this->catatan_admin,
        ])->save();

        return true;
    }

    public function refreshTotals(): void
    {
        $subtotal = (float) $this->detailPesanans()->sum('subtotal');
        $diskon = (float) $this->diskon;

        $this->forceFill([
            'subtotal' => $subtotal,
            'total_biaya' => max($subtotal - $diskon, 0),
        ])->saveQuietly();
    }

    public function recordStatusHistory(?string $previousStatus, string $newStatus, ?User $user = null, ?string $catatan = null): void
    {
        $this->riwayatStatuses()->create([
            'user_id' => $user?->id ?? Auth::id(),
            'status_sebelumnya' => $previousStatus,
            'status_baru' => $newStatus,
            'tanggal_perubahan' => now(),
            'catatan' => $catatan ?: 'Status pesanan diperbarui menjadi ' . (self::STATUS_LABELS[$newStatus] ?? $newStatus) . '.',
        ]);
    }

    public function syncPengingatPengambilan(): void
    {
        if ($this->status_pesanan === 'siap_diambil' && $this->tanggal_siap_diambil) {
            $tanggalMasukPengingat = $this->tanggal_siap_diambil->copy()->addDays(3);

            if (now()->greaterThanOrEqualTo($tanggalMasukPengingat)) {
                $this->pengingatPengambilan()->updateOrCreate(
                    ['pesanan_id' => $this->id],
                    [
                        'pelanggan_id' => $this->pelanggan_id,
                        'tanggal_siap_diambil' => $this->tanggal_siap_diambil,
                        'tanggal_masuk_pengingat' => $tanggalMasukPengingat,
                        'jumlah_hari_tertahan' => max($this->tanggal_siap_diambil->diffInDays(now()), 3),
                        'status_pengingat' => 'aktif',
                    ]
                );
            }

            return;
        }

        if ($this->status_pesanan === 'selesai' && $this->pengingatPengambilan) {
            $this->pengingatPengambilan
                ->forceFill(['status_pengingat' => 'selesai'])
                ->save();
        }
    }

    public function getTotalBiayaRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_biaya, 0, ',', '.');
    }

    public function getNamaStatusPesananAttribute(): string
    {
        return self::STATUS_LABELS[$this->status_pesanan] ?? '-';
    }

    public function getNamaStatusPembayaranAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'belum_dibayar' => 'Belum Dibayar',
            'lunas' => 'Lunas',
            default => '-',
        };
    }

    public function getNamaMetodePenyerahanAttribute(): string
    {
        return match ($this->metode_penyerahan) {
            'antar_sendiri' => 'Antar Sendiri ke Outlet',
            'jemput' => 'Minta Dijemput',
            default => '-',
        };
    }
}
