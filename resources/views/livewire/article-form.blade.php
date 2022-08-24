<div>
    <h1> Crear articulo</h1>
    <form wire:submit.prevent="save">
        <label>
            <input wire:model="article.title" type="text" placeholder="Titulo">
            @error('article.title')
                {{ $message }}
            @enderror
        </label>

        <label>
            <input wire:model="article.slug" type="text" placeholder="Url amigable">
            @error('article.slug')
            {{ $message }}
            @enderror
        </label>

        <label>
            <textarea wire:model="article.content" placeholder="Contenio" cols="20" rows="10"></textarea>
            @error('article.content')
            {{ $message }}
            @enderror
        </label>

        <input type="submit" value="Guardar" >
    </form>
    <a href="{{ route('articles.index') }}">Regresar</a>
</div>

