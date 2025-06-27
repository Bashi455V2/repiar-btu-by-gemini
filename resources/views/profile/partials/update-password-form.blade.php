<section x-data="{ show: false, processing: false }">
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 flex items-center">
            <i class="fas fa-key fa-fw mr-3 text-sky-600 dark:text-sky-500"></i>
            {{ __('เปลี่ยนรหัสผ่าน') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('ตรวจสอบให้แน่ใจว่าบัญชีของคุณใช้รหัสผ่านที่ยาวและคาดเดายากเพื่อความปลอดภัย') }}
        </p>
    </header>

    {{-- Success Message --}}
    @if (session('status') === 'password-updated')
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            x-init="setTimeout(() => show = false, 5000)"
            class="mt-6 p-4 flex items-center bg-green-50 dark:bg-green-700/30 border-l-4 border-green-500 rounded-r-lg"
            role="alert"
        >
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle fa-lg text-green-600 dark:text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">
                    {{ __('บันทึกรหัสผ่านใหม่เรียบร้อยแล้ว') }}
                </p>
            </div>
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8 dark:bg-transparent dark:text-green-400 dark:hover:bg-green-700/40" aria-label="Dismiss">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    @endif

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
        x-data="{
            showCurrent: false,
            showNew: false,
            showConfirm: false
        }"
        @submit="processing = true"
    >
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('รหัสผ่านปัจจุบัน')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                    <i class="fas fa-shield-alt fa-fw"></i>
                </div>
                {{-- ** แก้ไข: ใช้ input tag ตรงๆ แทน x-text-input ** --}}
                <input
                    id="update_password_current_password"
                    name="current_password"
                    x-bind:type="showCurrent ? 'text' : 'password'"
                    class="block w-full ps-10 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm"
                    autocomplete="current-password"
                />
                <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    <i class="fas fa-fw" x-bind:class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div>
            <x-input-label for="update_password_password" :value="__('รหัสผ่านใหม่')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                    <i class="fas fa-key fa-fw"></i>
                </div>
                {{-- ** แก้ไข: ใช้ input tag ตรงๆ แทน x-text-input ** --}}
                <input
                    id="update_password_password"
                    name="password"
                    x-bind:type="showNew ? 'text' : 'password'"
                    class="block w-full ps-10 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm"
                    autocomplete="new-password"
                />
                 <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    <i class="fas fa-fw" x-bind:class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('ยืนยันรหัสผ่านใหม่')" />
            <div class="relative mt-1">
                 <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                    <i class="fas fa-key fa-fw"></i>
                </div>
                {{-- ** แก้ไข: ใช้ input tag ตรงๆ แทน x-text-input ** --}}
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    x-bind:type="showConfirm ? 'text' : 'password'"
                    class="block w-full ps-10 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm"
                    autocomplete="new-password"
                />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    <i class="fas fa-fw" x-bind:class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button x-bind:disabled="processing">
                <div x-show="processing" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <span>{{ __('กำลังบันทึก...') }}</span>
                </div>
                <span x-show="!processing">{{ __('บันทึก') }}</span>
            </x-primary-button>
        </div>
    </form>
</section>
