@php use Illuminate\Support\Facades\Storage; @endphp
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{--            {{ __('New article') }}--}}
        </h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

            <x-jet-form-section submit="save">
                <x-slot name="title">
                    {{ __('New article') }}
                </x-slot>
                <x-slot name="description">
                    {{ __('Some description') }}
                </x-slot>

                <x-slot name="form">

                    {{--    Imagen    --}}
                    <div class="col-span-6 sm:col-span-4 relative">
                        <x-select-image wire:model="image" :image="$image" :existing="$article->image" />
                        <x-jet-input-error for="image" class="mt-2"/>
                    </div>

                    {{--    Titulo    --}}
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="title" :value="__('Title')" />
                        <x-jet-input wire:model="article.title" id="title" class="mt-1 block w-full" type="text" />
                        <x-jet-input-error for="article.title" class="mt-2"/>
                    </div>

                    {{--    Slug    --}}
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="slug" :value="__('Slug')" />
                        <x-jet-input wire:model="article.slug" id="slug" class="mt-1 block w-full" type="text" />
                        <x-jet-input-error for="article.slug" class="mt-2"/>
                    </div>

                    {{--    Categoria    --}}
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="category_id" :value="__('Category')" />
                        {{--    agrupar select y boton de crear    --}}
                        <div class="flex space-x-2 mt-1">
                            <x-select wire:model="article.category_id" id="category_id" class="block w-full" :options="$categories" :placeholder="__('Select category')" />
                            {{--    Boton del modal de categoria    --}}
                            <x-jet-secondary-button wire:click="$set('showCategoryModal', true)" class="!p-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-jet-secondary-button>
                        </div>
                        <x-jet-input-error for="article.category_id" class="mt-2"/>
                    </div>

                    {{--    Contenido    --}}
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="content" :value="__('Content')" />
                        <x-html-editor wire:model="article.content" id="content" class="mt-1 block w-full"></x-html-editor>
{{--                        <x-textarea wire:model="article.content" id="content" class="mt-1 block w-full" />--}}
                        <x-jet-input-error for="article.content" class="mt-2"/>
                    </div>

                    {{--    Boton    --}}
                    <x-slot name="actions">
                        <x-jet-button>
                            {{ __('Save') }}
                        </x-jet-button>
                    </x-slot>

                </x-slot>
        <a href="{{ route('articles.index') }}">Regresar</a>
            </x-jet-form-section>
        </div>
    </div>
    <x-jet-dialog-modal wire:model="showCategoryModal">
        <x-slot name="title">Modal Title</x-slot>
        <x-slot name="content">Category Form</x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('showCategoryModal', false)">
                Cancel
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>

