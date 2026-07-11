<?php

namespace App\Models;

use App\Enums\PembayaranStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pembayaran extends Model
{
    protected $fillable = [
        'pesanan_id',
        'nomor_pembayaran',
        'midtrans_order_id',
        'snap_token',
        'snap_redirect_url',
        'transaction_status',
        'payment_type',
        'fraud_status',
        'midtrans_response',
        'metode_pembayaran',
        'total_tagihan',
        'nominal_dibayar',
        'kembalian',
        'status_pembayaran',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'tanggal_pembayaran' => 'datetime',
        'midtrans_response' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pembayaran $pembayaran): void {
            $pembayaran->nomor_pembayaran ??= static::generateNomorPembayaran();
            $pembayaran->metode_pembayaran ??= 'qris';
            $pembayaran->nominal_dibayar ??= 0;
            $pembayaran->kembalian ??= 0;
            $pembayaran->status_pembayaran ??= 'belum_dibayar';
        });

        static::saving(function (Pembayaran $pembayaran): void {
            $pembayaran->nomor_pembayaran ??= static::generateNomorPembayaran();
            $pembayaran->metode_pembayaran ??= 'qris';
            $pembayaran->nominal_dibayar ??= 0;

            $pembayaran->kembalian = max(
                (float) $pembayaran->nominal_dibayar - (float) $pembayaran->total_tagihan,
                0
            );

            if ($pembayaran->status_pembayaran === 'menunggu_konfirmasi') {
                $pembayaran->tanggal_pembayaran = null;
                return;
            }

            if ($pembayaran->status_pembayaran === 'lunas') {
                $pembayaran->nominal_dibayar = max(
                    (float) $pembayaran->nominal_dibayar,
                    (float) $pembayaran->total_tagihan
                );

                $pembayaran->tanggal_pembayaran ??= now();
                return;
            }

            $pembayaran->status_pembayaran = 'belum_dibayar';
            $pembayaran->tanggal_pembayaran = null;
        });

        static::saved(function (Pembayaran $pembayaran): void {
            $pembayaran->pesanan?->forceFill([
                'status_pembayaran' => $pembayaran->status_pembayaran,
            ])->saveQuietly();

            if ($pembayaran->status_pembayaran === 'lunas') {
                ArusKas::updateOrCreate(
                    ['pembayaran_id' => $pembayaran->id],
                    [
                        'pesanan_id' => $pembayaran->pesanan_id,
                        'user_id' => auth()->id(),
                        'jenis' => 'masuk',
                        'kategori' => 'Pembayaran Laundry',
                        'judul' => 'Pembayaran ' . $pembayaran->nomor_pembayaran,
                        'nominal' => $pembayaran->total_tagihan,
                        'tanggal' => ($pembayaran->tanggal_pembayaran ?: now())->toDateString(),
                        'keterangan' => $pembayaran->midtrans_order_id
                            ? 'Kas masuk otomatis dari pembayaran Midtrans Sandbox.'
                            : 'Kas masuk otomatis dari pembayaran pesanan.',
                    ]
                );

                $pesanan = $pembayaran->pesanan;

                if ($pesanan && $pesanan->status_pesanan === 'menunggu_konfirmasi') {
                    $pesanan->updateStatus(
                        'menunggu_proses',
                        catatan: 'Pembayaran sudah lunas. Pesanan otomatis diproses.'
                    );
                }

                return;
            }

            ArusKas::query()
                ->where('pembayaran_id', $pembayaran->id)
                ->where('jenis', 'masuk')
                ->delete();
        });
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function arusKas(): HasOne
    {
        return $this->hasOne(ArusKas::class);
    }

    public static function generateNomorPembayaran(): string
    {
        $prefix = 'PAY-' . now()->format('Ymd') . '-';

        $lastNumber = static::where('nomor_pembayaran', 'like', $prefix . '%')
            ->orderByDesc('nomor_pembayaran')
            ->value('nomor_pembayaran');

        $sequence = $lastNumber ? ((int) substr((string) $lastNumber, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getNamaMetodePembayaranAttribute(): string
    {
        return match ($this->metode_pembayaran) {
            'qris' => $this->midtrans_order_id ? 'Midtrans Sandbox / QRIS' : 'QRIS',
            default => '-',
        };
    }

    public function getBuktiPembayaranUrlAttribute(): ?string
    {
        if (! $this->bukti_pembayaran) {
            return null;
        }

        return asset('storage/' . $this->bukti_pembayaran);
    }

    public function getNamaStatusPembayaranAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'belum_dibayar' => 'Belum Dibayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'lunas' => 'Lunas',
            default => '-',
        };
    }
}
