<a
    {{ $attributes->merge(['class' => 'flex items-center w-full px-4 py-3 text-sm text-neutral-700 hover:bg-neutral-50 hover:text-primary-700 focus:outline-none focus:bg-neutral-50 focus:text-primary-700 transition-all duration-200']) }}>
    {{ $slot }}
</a>
