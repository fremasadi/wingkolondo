<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- BRAND -->
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <i class="bx bxs-store text-primary" style="font-size: 30px;"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">
                WingkoLondo
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- DASHBOARD -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- MASTER DATA -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>

        <li class="menu-item {{ request()->routeIs('tokos.*') ? 'active' : '' }}">
            <a href="{{ route('tokos.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>Data Toko</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('bahan-bakus.*') ? 'active' : '' }}">
            <a href="{{ route('bahan-bakus.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Bahan Baku</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('produks.*') ? 'active' : '' }}">
            <a href="{{ route('produks.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cookie"></i>
                <div>Produk Wingko</div>
            </a>
        </li>

        <!-- TRANSAKSI -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Transaksi</span>
        </li>

        <li class="menu-item {{ request()->routeIs('pesanans.*') ? 'active' : '' }}">
            <a href="{{ route('pesanans.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div>Pesanan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('produksis.*') ? 'active' : '' }}">
            <a href="{{ route('produksis.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Produksi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('distribusis.*') ? 'active' : '' }}">
            <a href="{{ route('distribusis.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-send"></i>
                <div>Distribusi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('returs.*') ? 'active' : '' }}">
            <a href="{{ route('returs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-undo"></i>
                <div>Retur</div>
            </a>
        </li>

        <!-- KEUANGAN -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Keuangan</span>
        </li>

        <li class="menu-item {{ request()->routeIs('piutangs.*') ? 'active' : '' }}">
            <a href="{{ route('piutangs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Piutang</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('omzet.*') ? 'active' : '' }}">
            <a href="{{ route('omzet.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div>Omzet</div>
            </a>
        </li>

        <!-- PENGATURAN -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Manajemen User</div>
            </a>
        </li>

    </ul>

</aside>