<nav x-data="{ open: false }" class="bg-white dark:bg-slate-800/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    @php
                        $homeRoute = route('welcome'); // Default for guests
                        if (Auth::check()) {
                            if (Auth::user()->is_admin) {
                                $homeRoute = route('admin.dashboard');
                            } elseif (Auth::user()->is_technician) {
                                // หน้าแรกของช่างคือ "งานของฉัน"
                                $homeRoute = route('technician.tasks.index');
                            } else { // User ทั่วไป
                                $homeRoute = route('repair_requests.index');
                            }
                        }
                    @endphp
                    <a href="{{ $homeRoute }}" class="flex items-center">
                        <svg class="h-9 w-auto text-sky-600 dark:text-sky-500 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 7L12 12L21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.5 9.75L12 12L7.5 9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <ellipse cx="12" cy="6.5" rx="1.5" ry="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold text-xl text-slate-700 dark:text-slate-200">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                {{-- Navigation Links (Desktop) --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (Auth::user()->is_admin)
                            {{-- Admin Links --}}
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <i class="fas fa-tachometer-alt fa-fw mr-2"></i>{{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.manage')" :active="request()->routeIs('admin.manage')">
                                <i class="fas fa-tasks fa-fw mr-2"></i>{{ __('จัดการงานซ่อม') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <i class="fas fa-users-cog fa-fw mr-2"></i>{{ __('จัดการผู้ใช้งาน') }}
                            </x-nav-link>
                            <div class="hidden sm:flex sm:items-center sm:ms-3">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none transition ease-in-out duration-150">
                                            <div><i class="fas fa-cogs fa-fw mr-2"></i>{{ __('ตั้งค่าระบบ') }}</div>
                                            <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('admin.locations.index')" :active="request()->routeIs('admin.locations.*')">{{ __('จัดการสถานที่') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">{{ __('จัดการหมวดหมู่') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.statuses.index')" :active="request()->routeIs('admin.statuses.*')">{{ __('จัดการสถานะ') }}</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @elseif (Auth::user()->is_technician)
                            {{-- Technician Links --}}
                            <x-nav-link :href="route('technician.tasks.index')" :active="request()->routeIs('technician.tasks.index')">
                                <i class="fas fa-tools fa-fw mr-2"></i>{{ __('งานของฉัน') }}
                            </x-nav-link>
                            {{-- **แก้ไขตรงนี้** --}}
                            <x-nav-link :href="route('technician.queue.index')" :active="request()->routeIs('technician.queue.index')">
                                <i class="fas fa-inbox fa-fw mr-2"></i>{{ __('คิวงานซ่อม') }}
                            </x-nav-link>
                        @else
                            {{-- General User Links --}}
                            <x-nav-link :href="route('repair_requests.index')" :active="request()->routeIs('repair_requests.index')">
                                <i class="fas fa-clipboard-list fa-fw mr-2"></i>{{ __('รายการของฉัน') }}
                            </x-nav-link>
                        @endif

                        @can('create', App\Models\RepairRequest::class)
                            <x-nav-link :href="route('repair_requests.create')" :active="request()->routeIs('repair_requests.create')">
                                <i class="fas fa-plus-circle fa-fw mr-2"></i>{{ __('แจ้งซ่อมใหม่') }}
                            </x-nav-link>
                        @endcan
                    @endauth
                </div>
            </div>

            {{-- User Dropdown & Hamburger --}}
            @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')"><i class="fas fa-user-edit fa-fw mr-2"></i>{{ __('โปรไฟล์ของฉัน') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-sign-out-alt fa-fw mr-2"></i>{{ __('ออกจากระบบ') }}</x-dropdown-link></form>
                        </x-slot>
                    </x-dropdown>
                </div>
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 dark:text-slate-500 hover:text-slate-500 dark:hover:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-slate-100 dark:focus:bg-slate-700 focus:text-slate-500 dark:focus:text-slate-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endauth
        </div>
    </div>

    {{-- Responsive Navigation Menu --}}
    @auth
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"><i class="fas fa-tachometer-alt fa-fw mr-2"></i>{{ __('Dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.manage')" :active="request()->routeIs('admin.manage')"><i class="fas fa-tasks fa-fw mr-2"></i>{{ __('จัดการงานซ่อม') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"><i class="fas fa-users-cog fa-fw mr-2"></i>{{ __('จัดการผู้ใช้งาน') }}</x-responsive-nav-link>
                <div class="pt-2 pb-1 border-t border-slate-200 dark:border-slate-700"><div class="px-4"><div class="font-medium text-sm text-slate-600 dark:text-slate-400">{{ __('ตั้งค่าข้อมูลหลัก') }}</div></div><x-responsive-nav-link :href="route('admin.locations.index')" :active="request()->routeIs('admin.locations.*')">{{ __('จัดการสถานที่') }}</x-responsive-nav-link><x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">{{ __('จัดการหมวดหมู่') }}</x-responsive-nav-link><x-responsive-nav-link :href="route('admin.statuses.index')" :active="request()->routeIs('admin.statuses.*')">{{ __('จัดการสถานะ') }}</x-responsive-nav-link></div>
            @elseif (Auth::user()->is_technician)
                {{-- Technician Responsive Links --}}
                <x-responsive-nav-link :href="route('technician.tasks.index')" :active="request()->routeIs('technician.tasks.index')"><i class="fas fa-tools fa-fw mr-2"></i>{{ __('งานของฉัน') }}</x-responsive-nav-link>
                {{-- **แก้ไขตรงนี้** --}}
                <x-responsive-nav-link :href="route('technician.queue.index')" :active="request()->routeIs('technician.queue.index')"><i class="fas fa-inbox fa-fw mr-2"></i>{{ __('คิวงานซ่อม') }}</x-responsive-nav-link>
            @else
                {{-- General User Responsive Links --}}
                <x-responsive-nav-link :href="route('repair_requests.index')" :active="request()->routeIs('repair_requests.index')"><i class="fas fa-clipboard-list fa-fw mr-2"></i>{{ __('รายการของฉัน') }}</x-responsive-nav-link>
            @endif
            @can('create', App\Models\RepairRequest::class)
                <x-responsive-nav-link :href="route('repair_requests.create')" :active="request()->routeIs('repair_requests.create')"><i class="fas fa-plus-circle fa-fw mr-2"></i>{{ __('แจ้งซ่อมใหม่') }}</x-responsive-nav-link>
            @endcan
        </div>
        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-600">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')"><i class="fas fa-user-edit fa-fw mr-2"></i>{{ __('โปรไฟล์ของฉัน') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">@csrf<x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();this.closest('form').submit();"><i class="fas fa-sign-out-alt fa-fw mr-2"></i>{{ __('ออกจากระบบ') }}</x-responsive-nav-link></form>
            </div>
        </div>
    </div>
    @endauth
</nav>