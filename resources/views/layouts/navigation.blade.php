<nav x-data="{ open: false }" class="bg-white dark:bg-slate-800/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    @php
                        $homeRoute = route('repair_requests.index'); // Default for User
                        $logoLinkText = __('Btu Repair'); // Default App Name
                        if (Auth::check()) {
                            if (Auth::user()->is_admin) {
                                $homeRoute = route('dashboard');
                            } elseif (Auth::user()->is_technician) {
                                $homeRoute = route('repair_requests.manage');
                            }
                        } else {
                            $homeRoute = route('welcome'); // Guest ไปหน้า welcome
                        }
                    @endphp
                    <a href="{{ $homeRoute }}" class="flex items-center">
                        {{-- โลโก้ SVG Btu Repair --}}
                        <svg class="h-9 w-auto text-sky-600 dark:text-sky-500 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 7L12 12L21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.5 9.75L12 12L7.5 9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <ellipse cx="12" cy="6.5" rx="1.5" ry="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="font-semibold text-xl text-slate-700 dark:text-slate-200">{{ $logoLinkText }}</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth {{-- แสดงเมนูเหล่านี้เมื่อผู้ใช้ล็อกอินแล้วเท่านั้น --}}
                        {{-- ลิงก์ Dashboard (เฉพาะ Admin) --}}
                        @if (Auth::user()->is_admin)
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif

                        {{-- ลิงก์รายการแจ้งซ่อม --}}
                        {{-- User: รายการของฉัน, Technician: รายการที่เกี่ยวข้อง, Admin: อาจจะไม่จำเป็นถ้าดูจาก Dashboard/Manage --}}
                        @if (Auth::user()->is_technician || !Auth::user()->is_admin) {{-- แสดงสำหรับ User และ Technician (Admin อาจจะเข้าจากที่อื่น) --}}
                            <x-nav-link :href="route('repair_requests.index')" :active="request()->routeIs('repair_requests.index')">
                                {{ Auth::user()->is_admin ? __('รายการแจ้งซ่อมทั้งหมด') : (Auth::user()->is_technician ? __('งานของฉัน/ทั้งหมด') : __('รายการของฉัน')) }}
                            </x-nav-link>
                        @endif

                        {{-- ลิงก์แจ้งซ่อมใหม่ (ทุกคนที่ล็อกอิน) --}}
                        <x-nav-link :href="route('repair_requests.create')" :active="request()->routeIs('repair_requests.create')">
                            {{ __('แจ้งซ่อมใหม่') }}
                        </x-nav-link>

                        {{-- ลิงก์สำหรับ Admin และ Technician: จัดการแจ้งซ่อม --}}
                        @if (Auth::user()->is_admin || Auth::user()->is_technician)
                            <x-nav-link :href="route('repair_requests.manage')" :active="request()->routeIs('repair_requests.manage')">
                                {{ __('จัดการงานซ่อม') }}
                            </x-nav-link>
                        @endif

                        {{-- ลิงก์สำหรับ Admin เท่านั้น: การจัดการผู้ใช้ --}}
                        @if (Auth::user()->is_admin)
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                                {{ __('จัดการผู้ใช้งาน') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- User Dropdown (แสดงเมื่อล็อกอินแล้ว) --}}
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('โปรไฟล์ของฉัน') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('ออกจากระบบ') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            {{-- Hamburger Menu (แสดงเมื่อล็อกอินแล้ว) --}}
            @auth
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 dark:text-slate-500 hover:text-slate-500 dark:hover:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-slate-100 dark:focus:bg-slate-700 focus:text-slate-500 dark:focus:text-slate-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endauth
        </div>
    </div>

    {{-- Responsive Navigation Menu (แสดงเมื่อล็อกอินแล้ว) --}}
    @auth
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->is_technician || !Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('repair_requests.index')" :active="request()->routeIs('repair_requests.index')">
                    {{ Auth::user()->is_admin ? __('รายการแจ้งซ่อมทั้งหมด') : (Auth::user()->is_technician ? __('งานของฉัน/ทั้งหมด') : __('รายการของฉัน')) }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('repair_requests.create')" :active="request()->routeIs('repair_requests.create')">
                {{ __('แจ้งซ่อมใหม่') }}
            </x-responsive-nav-link>

            @if (Auth::user()->is_admin || Auth::user()->is_technician)
                <x-responsive-nav-link :href="route('repair_requests.manage')" :active="request()->routeIs('repair_requests.manage')">
                    {{ __('จัดการงานซ่อม') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                    {{ __('จัดการผู้ใช้งาน') }}
                </x-responsive-nav-link>
            @endif
        </div>

        {{-- Responsive Settings Options --}}
        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-600">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('โปรไฟล์ของฉัน') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('ออกจากระบบ') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    @endauth
</nav>