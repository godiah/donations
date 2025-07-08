@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center w-full px-3 py-3 text-base font-medium text-primary-700 bg-primary-100 border-l-4 border-primary-600 hover:bg-primary-200 focus:outline-none focus:bg-primary-200 focus:text-primary-800 transition-all duration-200'
            : 'flex items-center w-full px-3 py-3 text-base font-medium text-neutral-600 hover:text-primary-700 hover:bg-primary-50 border-l-4 border-transparent hover:border-primary-300 focus:outline-none focus:text-primary-700 focus:bg-primary-50 focus:border-primary-300 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
