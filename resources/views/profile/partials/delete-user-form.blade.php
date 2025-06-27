<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-red-600 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-triangle fa-fw mr-3"></i>
            {{ __('ลบบัญชีผู้ใช้') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('เมื่อบัญชีของคุณถูกลบ ทรัพยากรและข้อมูลทั้งหมดจะถูกลบอย่างถาวร ก่อนลบบัญชี กรุณาดาวน์โหลดข้อมูลหรือข้อมูลใดๆ ที่คุณต้องการเก็บไว้') }}
        </p>
    </header>

    {{-- ปุ่มสำหรับเปิด Modal --}}
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('ลบบัญชี') }}</x-danger-button>

    {{-- Modal ยืนยันการลบ --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="p-6 dark:bg-slate-800"
            x-data="{ showPassword: false, processing: false }"
            @submit="processing = true"
        >
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                {{ __('คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีของคุณ?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                {{ __('เมื่อบัญชีของคุณถูกลบ ทรัพยากรและข้อมูลทั้งหมดจะถูกลบอย่างถาวร กรุณากรอกรหัสผ่านของคุณเพื่อยืนยันว่าคุณต้องการลบบัญชีของคุณอย่างถาวร') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password_delete" value="{{ __('รหัสผ่าน') }}" class="sr-only" />

                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                         <i class="fas fa-key fa-fw"></i>
                    </div>
                    {{-- ใช้ input tag ตรงๆ เพื่อให้ Alpine.js ทำงานกับ type ได้ --}}
                    <input
                        id="password_delete"
                        name="password"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        class="block w-full ps-10 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm"
                        placeholder="{{ __('รหัสผ่าน') }}"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        <i class="fas fa-fw" x-bind:class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('ยกเลิก') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" x-bind:disabled="processing">
                    <div x-show="processing" class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span>{{ __('กำลังลบ...') }}</span>
                    </div>
                    <span x-show="!processing">{{ __('ลบบัญชี') }}</span>
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
