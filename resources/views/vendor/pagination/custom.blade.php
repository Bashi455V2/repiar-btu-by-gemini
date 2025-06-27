@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- ส่วนนี้จะแสดงผลบนจอขนาดเล็ก (sm) --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 cursor-default leading-5 rounded-md dark:bg-slate-800 dark:border-slate-600 dark:text-slate-400">
                    &laquo; ก่อนหน้า
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-500 focus:outline-none focus:ring ring-slate-300 focus:border-blue-300 active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                    &laquo; ก่อนหน้า
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-500 focus:outline-none focus:ring ring-slate-300 focus:border-blue-300 active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                    ถัดไป &raquo;
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-500 bg-white border border-slate-300 cursor-default leading-5 rounded-md dark:bg-slate-800 dark:border-slate-600 dark:text-slate-400">
                    ถัดไป &raquo;
                </span>
            @endif
        </div>

        {{-- ส่วนนี้จะแสดงผลบนจอขนาดใหญ่ (sm ขึ้นไป) --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end">
            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-default rounded-l-md leading-5 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-l-md leading-5 hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring ring-slate-300 focus:border-blue-300 active:bg-slate-100 active:text-slate-500 transition ease-in-out duration-150 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-300 cursor-default leading-5 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-sky-500 border border-sky-500 cursor-default leading-5 dark:bg-sky-600 dark:border-sky-600">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring ring-slate-300 focus:border-blue-300 active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-r-md leading-5 hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring ring-slate-300 focus:border-blue-300 active:bg-slate-100 active:text-slate-500 transition ease-in-out duration-150 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-400 bg-white border border-slate-300 cursor-default rounded-r-md leading-5 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif