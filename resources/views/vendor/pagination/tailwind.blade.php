{{-- resources/views/vendor/pagination/tailwind.blade.php --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center">
        <ul class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true" aria-label="Previous">
                    <span class="px-3 py-1 rounded text-orange-xamanen bg-white border border-orange-xamanen font-bold opacity-50 cursor-not-allowed">&lt;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1 rounded text-orange-xamanen bg-white border border-orange-xamanen font-bold hover:bg-orange-xamanen hover:text-white transition">&lt;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li aria-disabled="true"><span class="px-3 py-1 rounded text-gray-400 bg-white border font-bold">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="px-3 py-1 rounded bg-orange-xamanen text-white font-bold border border-orange-xamanen">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-3 py-1 rounded text-orange-xamanen bg-white border border-orange-xamanen font-bold hover:bg-orange-xamanen hover:text-white transition">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1 rounded text-orange-xamanen bg-white border border-orange-xamanen font-bold hover:bg-orange-xamanen hover:text-white transition">&gt;</a>
                </li>
            @else
                <li aria-disabled="true" aria-label="Next">
                    <span class="px-3 py-1 rounded text-orange-xamanen bg-white border border-orange-xamanen font-bold opacity-50 cursor-not-allowed">&gt;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
