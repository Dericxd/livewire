@php($img = $attributes->wire('model')->value) {{--  $id => $img  --}}
<div x-data="{ focused: false }" class="relative">
    @if($image)
        <x-jet-danger-button wire:click="$set('{{ $img }}')" class="absolute bottom-2 right-2">
            {{ __('Change Image') }}
        </x-jet-danger-button>
        {{--    Solo pra php 8    --}}
        <img src="{{ $image?->temporaryUrl() }}" class="border-2 rounded" alt="">
        {{--    Php 7 para abajo    --}}
        {{--    <img src="{{ $image->temporaryUrl() }}" class="border-2 rounded" alt="">--}}
    @elseif($existing)
        <label for="{{ $img }}"
                     class="absolute bottom-2 right-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 disabled:opacity-25 transition"
                     :class="{'outline-none border-gray-900 ring ring-gray-300': focused }"
        >{{ __('Change Image') }}</label>
        <img src="{{ Storage::disk('public')->url($existing) }}" alt="">
    @else
        <div class="h-32 bg-gray-50 border-2 border-dashed rounded flex items-center justify-center">
            <label for="{{ $img }}"
                   class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 disabled:opacity-25 transition"
                   :class="{'outline-none border-gray-900 ring ring-gray-300' : focused }"
            >{{ __('Select Image') }}</label>
        </div>
    @endif

    @unless($image)
        <x-jet-input
            x-on:focus="focused = true"
            x-on:blur="focused = false"
            wire:model="{{ $img }}"
            :id="$img"
            class="sr-only"
            type="file" />
    @endunless
</div>
