@extends('layouts.public')

@section('content')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-12 lg:px-8">
        <aside class="lg:col-span-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="px-4 py-2 text-lg font-black text-slate-800">Dashboard</p>
                <nav class="mt-3 grid gap-2 text-base font-black text-slate-700">
                    <a href="{{ route('customer.orders.create') }}" class="rounded-xl px-4 py-4 {{ request()->routeIs('customer.orders.create') ? 'bg-blue-50 text-blue-700' : 'hover:bg-slate-50' }}">Buat Pesanan</a>
                    <a href="{{ route('customer.orders.index') }}" class="rounded-xl px-4 py-4 {{ request()->routeIs('customer.orders.index') || request()->routeIs('customer.orders.show') ? 'bg-blue-50 text-blue-700' : 'hover:bg-slate-50' }}">Pesanan Saya</a>
                    <a href="{{ route('customer.profile') }}" class="rounded-xl px-4 py-4 {{ request()->routeIs('customer.profile') ? 'bg-blue-50 text-blue-700' : 'hover:bg-slate-50' }}">Profil Saya</a>
                    <a href="{{ route('status.form') }}" class="rounded-xl px-4 py-4 hover:bg-slate-50">Cek Status Publik</a>

                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
                            @csrf
                            <button type="submit" class="font-black text-slate-700 hover:text-blue-700">Logout</button>
                        </form>
                    @endauth
                </nav>
            </div>
        </aside>

        <div class="lg:col-span-9">
            @if(isset($hariLiburAktif) && $hariLiburAktif)
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 font-bold text-amber-800">
                    Informasi: Laundry sedang libur {{ $hariLiburAktif->periode_libur }} — {{ $hariLiburAktif->nama_hari_libur }}. Pesanan tetap bisa dibuat, estimasi dapat disesuaikan admin.
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-bold text-emerald-700">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-bold text-red-700">{{ $errors->first() }}</div>
            @endif

            @yield('customer-content')
        </div>
    </section>
@endsection
