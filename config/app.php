<?php

use Illuminate\Support\Facades\Facade; // ตรวจสอบว่ามี use Facade
use Illuminate\Support\ServiceProvider; // ตรวจสอบว่ามี use ServiceProvider

return [
    'name' => env('APP_NAME', 'Laravel'),
    // ... (ส่วนอื่นๆ เช่น env, debug, url, timezone, locale, key, maintenance) ...
    'locale' => 'th',
'fallback_locale' => 'th',
'faker_locale' => 'th_TH', // สำหรับ Faker
// config/app.php
// config/app.php
'timezone' => env('APP_TIMEZONE', 'Asia/Bangkok'), // Fallback เป็น Asia/Bangkok ถ้าใน .env ไม่มี
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        // ... (รายชื่อ Service Provider ของ Laravel ทั้งหมด) ...
        Illuminate\View\ViewServiceProvider::class, // <--- สำคัญสำหรับ View Facade

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class, // ที่คุณต้องการเพิ่ม
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),
];