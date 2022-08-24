<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ArticleForm extends Component
{
    public Article $article; //$article->title

    protected function rules()
    {
        return [
            'article.title' => ['required','min:4'],
            'article.slug' => [
                'required',
                'alpha_dash',
//                Rule::unique('articles','slug')->ignore($this->article),
                'unique:articles,slug,'.$this->article->id
            ],
            'article.content' => ['required']
        ];
    }

    public function mount(Article $article)
    {
        $this->article = $article;
    }

    // Validacion en tiempo real
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedArticleTitle($title)
    {
        $this->article->slug = Str::slug($title);
    }

    public function save()
    {
        $this->validate();

        // Relacion del modelo usuario con articulos
        Auth::user()->articles()->save($this->article);

//        $this->article->user_id = auth()->id();
//        $this->article->save();

        session()->flash('status', __('Article saved.'));

        $this->redirectRoute('articles.index');
    }

    public function render()
    {
        return view('livewire.article-form');
    }
}
