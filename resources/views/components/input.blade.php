@props(['id', 'name', 'label', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => '', 'readonly' => false, 'disabled' => false])

<div>
    <label for="{{ $id }}"
        class="block mb-2 text-tiny font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
        @if ($readonly) readonly @endif
        @if ($disabled) disabled class="text-gray-400 bg-gray-50 border border-gray-300 text-tiny rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5" @endif
        class="bg-gray-50 border border-gray-300 text-gray-900 text-tiny rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5"
        placeholder="{{ $placeholder }}" @if ($required) required @endif value="{{ $value }}">

    @error($name)
        <p class="mt-1 text-tiny text-red-500">{{ $message }}</p>
    @enderror
</div>
