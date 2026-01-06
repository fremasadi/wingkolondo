<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <!-- Sneat Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> <!-- WAJIB SETELAH THEME -->
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

            {{-- CONTENT WRAPPER - JANGAN TAMBAH CONTAINER LAGI --}}
            <div class="content-wrapper">
                @yield('content')

                {{-- FOOTER --}}
                @include('layouts.footer')
            </div>

        </div>
    </div>
</div>

<!-- Sneat JS -->
<script src="{{ asset('sneat-1.0.0/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('sneat-1.0.0/assets/js/main.js') }}"></script>

</body>
</html>