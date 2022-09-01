<div class="py-12">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between">
            <label>
                <x-jet-input
                    wire:model="searh"
                    type="search"
                    placeholder="buscar..."
                />
            </label>
            <a href="{{ route('articles.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5 mr-2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                </svg>
                {{ __('New Article') }}

            </a>
        </div>


        <div class="overflow-x-auto relative shadow-md sm:rounded-lg mt-10">

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">
                        Title
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Created at
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($articles as $article)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                    <th scope="row" class="flex items-center py-4 px-6 text-gray-900 whitespace-nowrap dark:text-white">
                        <img class="w-10 h-10 rounded-full" src="{{ $article->imageUrl() }}"
                             alt="{{ $article->$article }}">
                        <div class="pl-3">
                            <div class="text-base font-semibold">
                                <a href="{{ route('articles.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </div>
                        </div>
                    </th>
                    <td class="py-4 px-6">
                        <div class="text-base font-semibold">{{ $article->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <a href="{{ route('articles.edit', $article) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>


        <ul>
            @foreach($articles as $article)
                <li>
                    <a href="{{ route('articles.show', $article) }}">
                        {{ $article->title }}
                    </a>
                    <a href="{{ route('articles.edit', $article) }}" style="color: cornflowerblue">
                        Editar
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
