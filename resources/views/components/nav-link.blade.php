@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-4 py-2 text-sm font-medium text-primary-700 bg-primary-100 border border-primary-200 rounded-lg hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200'
            : 'inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
