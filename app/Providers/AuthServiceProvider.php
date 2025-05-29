<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Uncomment ถ้าจะใช้ Gate โดยตรง
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// Import Models และ Policies ที่คุณต้องการลงทะเบียน
use App\Models\RepairRequest;
use App\Policies\RepairRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // รูปแบบทั่วไป
        RepairRequest::class => RepairRequestPolicy::class, // <--- เพิ่ม Policy ของคุณที่นี่
        // เพิ่ม Model และ Policy อื่นๆ ที่นี่
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ถ้าคุณต้องการใช้ Gates ก็สามารถ define ที่นี่ได้
        // Gate::define('view-admin-dashboard', function ($user) {
        //     return $user->is_admin;
        // });
    }
}