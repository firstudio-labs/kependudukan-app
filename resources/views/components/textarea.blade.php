@props([
    'id' => '',
    'label' => 'Label',
    'name' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'row' => 3,
    'value' => '',
])

<div>
    <label for="{{ $id }}"
        class="block mb-2 text-tiny font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    <textarea id="{{ $id }}" rows="{{ $row }}" name="{{ $name }}"
        class="block p-2.5 w-full text-tiny text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-purple-500 focus:border-purple-500"
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }}>
@if (isset($value))
{{ $value }}
@endif
</textarea>

    @error($name)
        <p class="mt-1 text-tiny text-red-500">{{ $message }}</p>
    @enderror
</div>
