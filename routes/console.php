<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan otomatis setiap hari pukul 00:05 untuk buang stok kadaluarsa
Schedule::command('stok:buang-kadaluarsa')->dailyAt('00:05');
