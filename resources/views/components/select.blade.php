@props([
    'disabled' => false,
    'placeholder' => 'Select',
    'options' => []
])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
    <option value="">{{ $placeholder }}</option>
    @foreach($options as $opt => $label)
        <option value="{{ $opt }}"> {{ $label }} </option>
    @endforeach
</select>
