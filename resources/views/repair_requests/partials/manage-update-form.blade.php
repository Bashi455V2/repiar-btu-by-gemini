<form method="POST" action="{{ route('repair_requests.update_status_assign', $item->id) }}">
    @csrf
    @method('PUT')
    <div class="flex flex-col space-y-2 {{ $formIdSuffix === '_desktop' ? 'xl:flex-row xl:space-y-0 xl:space-x-2 xl:items-center' : '' }}">
        <div class="flex-1 min-w-0">
            <label for="status_id{{ $formIdSuffix }}_{{ $item->id }}" class="{{ $formIdSuffix === '_mobile' ? 'block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1' : 'sr-only' }}">สถานะ</label>
            <select name="status_id" id="status_id{{ $formIdSuffix }}_{{ $item->id }}" class="block w-full rounded-md border-slate-300 dark:border-slate-600 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500 sm:text-xs bg-white dark:bg-slate-700">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ $item->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            </select>
            @error('status_id', "updateForm{$formIdSuffix}_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex-1 min-w-0">
            <label for="assigned_to_user_id{{ $formIdSuffix }}_{{ $item->id }}" class="{{ $formIdSuffix === '_mobile' ? 'block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1' : 'sr-only' }}">มอบหมายให้</label>
            <select name="assigned_to_user_id" id="assigned_to_user_id{{ $formIdSuffix }}_{{ $item->id }}" class="block w-full rounded-md border-slate-300 dark:border-slate-600 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm focus:ring-1 focus:ring-sky-500 focus:border-sky-500 sm:text-xs bg-white dark:bg-slate-700">
                <option value="">-- ไม่มอบหมาย --</option>
                @foreach ($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ $item->assigned_to_user_id == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                @endforeach
            </select>
            @error('assigned_to_user_id', "updateForm{$formIdSuffix}_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="whitespace-nowrap w-full {{ $formIdSuffix === '_desktop' ? 'xl:w-auto' : '' }} inline-flex items-center justify-center px-3 py-2 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
            <i class="fas fa-save mr-1.5"></i>บันทึก
        </button>
    </div>
</form>