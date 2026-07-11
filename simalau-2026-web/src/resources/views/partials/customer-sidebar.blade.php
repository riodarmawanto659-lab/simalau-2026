<aside class="sidebar">
    <a class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">Dashboard</a>
    <a class="{{ request()->routeIs('customer.orders.create') ? 'active' : '' }}" href="{{ route('customer.orders.create') }}">Buat Pesanan</a>
    <a class="{{ request()->routeIs('customer.orders.index') || request()->routeIs('customer.orders.show') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Pesanan Saya</a>
    <a class="{{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">Profil Saya</a>
    <a href="{{ route('status.form') }}">Cek Status Publik</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</aside>
