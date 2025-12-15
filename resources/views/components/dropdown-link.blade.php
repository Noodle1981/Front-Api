{{-- resources/views/components/dropdown-link.blade.php --}}
<a {{ $attributes->merge([
    'class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-white/80 hover:text-white hover:bg-white/10 backdrop-blur-sm focus:outline-none focus:bg-white/20 transition duration-150 ease-in-out'
]) }}>
    {{ $slot }}
</a>