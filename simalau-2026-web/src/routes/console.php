<?php

use App\Services\LaundryOrderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('laundry:generate-pengingat-pengambilan', function (LaundryOrderService $service) {
    $service->syncDueReminders();

    $this->info('Pengingat pengambilan cucian berhasil disinkronkan.');
})->purpose('Generate pengingat cucian siap diambil yang belum diambil selama 3 hari');

Schedule::command('laundry:generate-pengingat-pengambilan')->hourly();
