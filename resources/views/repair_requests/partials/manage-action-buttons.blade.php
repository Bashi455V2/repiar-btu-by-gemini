<div class="flex items-center {{ isset($isMobile) && $isMobile ? 'justify-end space-x-4' : 'justify-center space-x-2 sm:space-x-3' }}">
    <a href="{{ route('repair_requests.show', $item) }}" class="{{ isset($isMobile) ? 'text-xs' : '' }} font-medium text-sky-600 dark:text-sky-400 hover:underline inline-flex items-center p-1 {{ isset($isMobile) ? '' : 'rounded-full hover:bg-sky-100 dark:hover:bg-slate-700' }}" title="ดูรายละเอียด">
        <i class="fas fa-eye fa-fw {{ isset($isMobile) ? 'mr-1' : '' }}"></i><span class="{{ isset($isMobile) ? '' : 'sm:hidden md:inline' }}">{{ isset($isMobile) ? 'ดู' : '' }}</span>
    </a>
    @can('update', $item)
    <a href="{{ route('repair_requests.edit', $item) }}" class="{{ isset($isMobile) ? 'text-xs' : '' }} font-medium text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center p-1 {{ isset($isMobile) ? '' : 'rounded-full hover:bg-amber-100 dark:hover:bg-slate-700' }}" title="แก้ไข">
        <i class="fas fa-pencil-alt fa-fw {{ isset($isMobile) ? 'mr-1' : '' }}"></i><span class="{{ isset($isMobile) ? '' : 'sm:hidden md:inline' }}">{{ isset($isMobile) ? 'แก้ไข' : '' }}</span>
    </a>
    @endcan
    @can('delete', $item)
    <form action="{{ route('repair_requests.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ isset($isMobile) ? 'text-xs' : '' }} font-medium text-red-600 dark:text-red-400 hover:underline inline-flex items-center p-1 {{ isset($isMobile) ? '' : 'rounded-full hover:bg-red-100 dark:hover:bg-slate-700' }}" title="ลบ">
            <i class="fas fa-trash-alt fa-fw {{ isset($isMobile) ? 'mr-1' : '' }}"></i><span class="{{ isset($isMobile) ? '' : 'sm:hidden md:inline' }}">{{ isset($isMobile) ? 'ลบ' : '' }}</span>
        </button>
    </form>
    @endcan
</div>