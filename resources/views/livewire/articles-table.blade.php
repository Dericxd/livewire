<div>
    <h1> Lista de articulos </h1>

    <a href="{{ route('articles.create') }}"> Crear </a>
    <label>
        <input
            wire:model="searh"
            type="search"
            placeholder="buscar..."
        >
    </label>

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
