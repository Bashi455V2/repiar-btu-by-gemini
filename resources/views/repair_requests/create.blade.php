<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- <h2 class="font-semibold text-2xl md:text-3xl text-slate-800 dark:text-slate-200 leading-tight text-center sm:text-left"> --}}
            {{-- ลดขนาด H2 เล็กน้อย และปรับ leadig-tight เป็น leading-normal เพื่อความโปร่ง --}}
            <h2 class="font-semibold text-xl md:text-2xl text-slate-800 dark:text-slate-200 leading-normal text-center sm:text-left">
                {{ __('แจ้งซ่อมใหม่') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12 lg:py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-2xl sm:rounded-xl">
                <div class="p-6 sm:p-8 lg:p-10">

                    {{-- Section Header - เน้นความสำคัญของหัวข้อ --}}
                    <div class="text-center mb-8 lg:mb-10">
                        {{-- <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-slate-100 mb-3"> --}}
                        {{-- ลดขนาด H3 เล็กน้อย และปรับ font-extrabold เป็น font-bold เพื่อลดความทึบ --}}
                        <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-slate-100 mb-3">
                            ส่งเรื่องแจ้งซ่อม
                        </h3>
                        {{-- <p class="text-base md:text-lg text-slate-600 dark:text-slate-400 max-w-prose mx-auto"> --}}
                        {{-- ปรับ text-lg เป็น text-base, เพิ่ม leading-relaxed เพื่อระยะห่างบรรทัดที่โปร่งขึ้น, และปรับสีเล็กน้อย --}}
                        <p class="text-base text-slate-600 dark:text-slate-400 max-w-prose mx-auto leading-relaxed">
                            กรุณากรอกรายละเอียดปัญหาให้ครบถ้วนเพื่อการดำเนินการที่รวดเร็วและแม่นยำ
                        </p>
                    </div>

                    <form method="POST" action="{{ route('repair_requests.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Section: รายละเอียดปัญหา --}}
                        <div class="mb-8">
                            {{-- <h4 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ข้อมูลปัญหา') }}</h4> --}}
                            {{-- ปรับขนาดและน้ำหนักเล็กน้อย --}}
                            <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ข้อมูลปัญหา') }}</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="title" :value="__('เรื่อง/อาการเบื้องต้น')" />
                                    {{-- ปรับสี placeholder ให้กลมกลืนขึ้น --}}
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด, แอร์เสีย" />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="description" :value="__('รายละเอียดปัญหา')" />
                                    {{-- ปรับสี placeholder และ text-slate-700 เป็น text-slate-600 เพื่อลดความเข้ม --}}
                                    <textarea id="description" name="description" rows="6" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-lg shadow-sm bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500" required placeholder="อธิบายปัญหาที่คุณพบอย่างละเอียด เช่น สถานที่ที่พบอาการ, ช่วงเวลา, หรือสิ่งที่ได้ลองแก้ไขเบื้องต้น">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: สถานที่และหมวดหมู่ --}}
                        <div class="mb-8">
                            {{-- <h4 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ตำแหน่งและประเภท') }}</h4> --}}
                            {{-- ปรับขนาดและน้ำหนักเล็กน้อย --}}
                            <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ตำแหน่งและประเภท') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="location_id" :value="__('สถานที่/อาคาร')" />
                                    <select id="location_id" name="location_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-lg shadow-sm bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-200" required>
                                        <option value="">-- เลือกสถานที่ --</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }} {{ $location->building ? '('.$location->building.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="category_id" :value="__('หมวดหมู่ปัญหา')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-600 rounded-lg shadow-sm bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-200" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: ข้อมูลติดต่อและรูปภาพ --}}
                        <div class="mb-10">
                            {{-- <h4 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ข้อมูลติดต่อและหลักฐาน') }}</h4> --}}
                            {{-- ปรับขนาดและน้ำหนักเล็กน้อย --}}
                            <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4">{{ __('ข้อมูลติดต่อและหลักฐาน') }}</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="requester_phone" :value="__('เบอร์โทรศัพท์ติดต่อ (ถ้ามี)')" />
                                    <x-text-input id="requester_phone" class="block mt-1 w-full" type="text" name="requester_phone" :value="old('requester_phone', Auth::user()->phone_number ?? '')" placeholder="เช่น 081-XXX-XXXX" />
                                    <x-input-error :messages="$errors->get('requester_phone')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="image" :value="__('รูปภาพประกอบ (ถ้ามี)')" />
                                    <input id="image" name="image" type="file" class="block w-full text-sm text-slate-500 dark:text-slate-400
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-lg file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-sky-100 dark:file:bg-sky-700/50 file:text-sky-700 dark:file:text-sky-200
                                        hover:file:bg-sky-200 dark:hover:file:bg-sky-600/50
                                        mt-1 border border-slate-300 dark:border-slate-600 rounded-lg cursor-pointer
                                        focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" accept="image/*"/>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB (เลือกได้ 1 ภาพ)</p>
                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />

                                    {{-- Image Preview Container with clear button --}}
                                    <div id="image-preview-container" class="mt-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-700/30 hidden">
                                        <div class="flex justify-between items-center mb-3">
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">รูปภาพที่เลือก:</p>
                                            <button type="button" id="clear-image-button" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 rounded-md p-1 -m-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span class="sr-only">{{ __('ลบรูปภาพ') }}</span>
                                            </button>
                                        </div>
                                        <div class="relative overflow-hidden rounded-md aspect-w-16 aspect-h-9 group"> {{-- Aspect ratio container --}}
                                            <img id="image-preview" src="#" alt="Image Preview" class="object-cover w-full h-full transform transition-transform duration-300 ease-in-out group-hover:scale-105"/>
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-md"></div> {{-- Overlay effect --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 sm:gap-6">
                            <a href="{{ route('repair_requests.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 text-base text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-sky-600 border border-transparent rounded-lg font-semibold text-base text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900 dark:bg-sky-500 dark:hover:bg-sky-400 dark:focus:bg-sky-400 dark:active:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                {{ __('ส่งเรื่องแจ้งซ่อม') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreviewContainer = document.getElementById('image-preview-container');
            const imagePreview = document.getElementById('image-preview');
            const clearImageButton = document.getElementById('clear-image-button'); // Get the new clear button

            function displayImagePreview(file) {
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.classList.remove('hidden'); // Show container
                    };

                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '#';
                    imagePreviewContainer.classList.add('hidden'); // Hide container if no file
                }
            }

            // Event listener for when a file is selected
            imageInput.addEventListener('change', function(event) {
                displayImagePreview(event.target.files[0]);
            });

            // Event listener for the clear button
            clearImageButton.addEventListener('click', function() {
                imageInput.value = ''; // Clear the input file value
                displayImagePreview(null); // Hide and clear the preview
            });
        });
    </script>
    @endpush
</x-app-layout>