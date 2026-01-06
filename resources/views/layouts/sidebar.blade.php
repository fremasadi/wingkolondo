<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- BRAND -->
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <i class="bx bxs-store text-primary" style="font-size: 30px;"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">
                Wingko Londo
            </span>
        </a>

        <a href="#" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
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

        <li class="menu-item {{ request()->is('tokos*') ? 'active' : '' }}">
            <a href="{{ route('tokos.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>Data Toko</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('bahan-bakus*') ? 'active' : '' }}">
            <a href="{{ route('bahan-bakus.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Bahan Baku</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('produks*') ? 'active' : '' }}">
            <a href="{{ route('produks.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cookie"></i>
                <div>Produk Wingko</div>
            </a>
        </li>

        <!-- TRANSAKSI -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Transaksi</span>
        </li>

        <li class="menu-item {{ request()->is('pesanans*') ? 'active' : '' }}">
            <a href="{{ route('pesanans.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div>Pesanan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('produksis*') ? 'active' : '' }}">
            <a href="{{ route('produksis.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Produksi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('distribusis*') ? 'active' : '' }}">
            <a href="{{ route('distribusis.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-send"></i>
                <div>Distribusi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('returs*') ? 'active' : '' }}">
            <a href="{{ route('returs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-undo"></i>
                <div>Retur</div>
            </a>
        </li>

        <!-- KEUANGAN -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Keuangan</span>
        </li>

        <li class="menu-item {{ request()->is('piutangs*') ? 'active' : '' }}">
            <a href="{{ route('piutangs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Piutang</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('omzet*') ? 'active' : '' }}">
            <a href="{{ route('omzet.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div>Omzet</div>
            </a>
        </li>

        {{-- <!-- LAPORAN -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Laporan</span>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Laporan Produksi</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file-find"></i>
                <div>Laporan Distribusi</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file-blank"></i>
                <div>Laporan Keuangan</div>
            </a>
        </li> --}}

        <!-- PENGATURAN -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>

        <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Manajemen User</div>
            </a>
        </li>

        {{-- <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div>Logout</div>
            </a>
        </li> --}}

    </ul>

</aside>