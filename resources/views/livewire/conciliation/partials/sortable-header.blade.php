@props(['field', 'label'])

<th
    wire:click="sortBy('{{ $field }}')"
    class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none"
>
    <div class="flex items-center gap-1">
        {{ $label }}
        @if($sortField === $field)
            @if($sortDirection === 'asc')
                <i class="fas fa-sort-up text-purple-600"></i>
            @else
                <i class="fas fa-sort-down text-purple-600"></i>
            @endif
        @else
            <i class="fas fa-sort text-gray-300"></i>
        @endif
    </div>
</th>
