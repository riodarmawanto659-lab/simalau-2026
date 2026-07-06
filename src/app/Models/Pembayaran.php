<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $fillable = [
        'pesanan_id',
        'nomor_pembayaran',
        'metode_pembayaran',
        'total_tagihan',
        'nominal_dibayar',
        'kembalian',
        'status_pembayaran',
        'tanggal_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'tanggal_pembayaran' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pembayaran $pembayaran): void {
            $pembayaran->nomor_pembayaran ??= static::generateNomorPembayaran();
        });

        static::saving(function (Pembayaran $pembayaran): void {
            $pembayaran->kembalian = max((float) $pembayaran->nominal_dibayar - (float) $pembayaran->total_tagihan, 0);

            if ((float) $pembayaran->nominal_dibayar >= (float) $pembayaran->total_tagihan && (float) $pembayaran->total_tagihan > 0) {
                $pembayaran->status_pembayaran = 'lunas';
                $pembayaran->tanggal_pembayaran ??= now();
            }
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
                        'keterangan' => 'Kas masuk otomatis dari pembayaran pesanan.',
                    ]
                );
            }
        });
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public static function generateNomorPembayaran(): string
    {
        $prefix = 'PAY-' . now()->format('Ymd') . '-';
        $lastNumber = static::where('nomor_pembayaran', 'like', $prefix . '%')
            ->orderByDesc('nomor_pembayaran')
            ->value('nomor_pembayaran');

        $sequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getNamaMetodePembayaranAttribute(): string
    {
        return match ($this->metode_pembayaran) {
            'tunai' => 'Tunai',
            'transfer_bank' => 'Transfer Bank',
            'qris' => 'QRIS',
            default => '-',
        };
    }
}
