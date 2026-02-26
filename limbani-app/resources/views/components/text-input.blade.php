@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
