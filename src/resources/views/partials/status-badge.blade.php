@php
    $class = match ($status ?? '') {
        'menunggu_konfirmasi' => 'gray',
        'menunggu_proses' => 'warning',
        'sedang_dicuci', 'sedang_dikeringkan', 'sedang_disetrika' => 'info',
        'siap_diambil', 'selesai', 'lunas', 'aktif', 'sudah_dihubungi' => 'success',
        'dibatalkan', 'nonaktif' => 'danger',
        'belum_dibayar' => 'warning',
        default => 'gray',
    };
@endphp
<span class="badge {{ $class }}">{{ $label ?? $status }}</span>
