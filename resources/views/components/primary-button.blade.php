<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-semibold text-base shadow-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
