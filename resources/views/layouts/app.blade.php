<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <!-- Sneat Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/css/demo.css') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/fonts/boxicons.css') }}">
</head>

<body>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        <div class="layout-page">

            {{-- NAVBAR --}}
            @include('layouts.navbar')

            {{-- CONTENT WRAPPER --}}
            <div class="content-wrapper">
                @yield('content')

                {{-- FOOTER --}}
                @include('layouts.footer')
            </div>

        </div>
    </div>

    <!-- Layout Overlay untuk Mobile -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>

<!-- Sneat JS - URUTAN PENTING! -->
<script src="{{ asset('sneat-1.0.0/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/js/main.js') }}"></script>

<!-- Script tambahan untuk memastikan menu toggle berfungsi -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan menu toggle berfungsi
        const menuToggles = document.querySelectorAll('.layout-menu-toggle');

        menuToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('.layout-wrapper').classList.toggle('layout-menu-expanded');
            });
        });
    });
</script>

</body>
</html>