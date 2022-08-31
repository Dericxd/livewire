<?php

namespace App\Http\Livewire;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArticleForm extends Component
{
    use WithFileUploads;

    public Article $article; //$article->title

    public $image;

    public $newCategory;

    public $showCategoryModal = false;

    public function openCategoryForm()
    {
        $this->newCategory = new Category;
        $this->showCategoryModal = true;
    }

    public function closeCategoryForm()
    {
        $this->showCategoryModal = false;
        $this->newCategory = null;
        $this->clearValidation('newCategory.*');
    }

    public function saveNewCategory()
    {
        $this->validateOnly('newCategory.name');
        $this->validateOnly('newCategory.slug');

        $this->newCategory->save();
        $this->article->category_id = $this->newCategory->id;
        $this->closeCategoryForm();
    }

    protected function rules()
    {
        return [
            'image' => [
                Rule::requiredIf(! $this->article->image),
//                $this->article->image ? '' : 'required',
                Rule::when($this->image, ['image', 'max:2048'])
//                $this->image ? ['image','max:2048'] : '',
            ],
            'article.title' => ['required','min:4'],
            'article.slug' => [
                'required',
                'alpha_dash',
                Rule::unique('articles','slug')->ignore($this->article),
//                'unique:articles,slug,'.$this->article->id
            ],
            'article.content' => ['required'],
            'article.category_id' => [
                'required',
                Rule::exists('categories', 'id')
            ],
            'newCategory.name' => [
                Rule::requiredIf($this->newCategory instanceof Category),
                Rule::unique('categories','name')
            ],
            'newCategory.slug' => [
                Rule::requiredIf($this->newCategory instanceof Category),
                Rule::unique('categories','slug')
            ]
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

    public function updatedNewCategoryName($name)
    {
        $this->newCategory->slug = Str::slug($name);
    }

    public function save()
    {
        $this->validate();

        if ($this->image) {// para validar si tiene una imagen
            $this->article->image = $this->uploadImage();
        }

        // Relacion del modelo usuario con articulos
        Auth::user()->articles()->save($this->article);

//        $this->article->user_id = auth()->id();
//        $this->article->save();

        session()->flash('status', __('Article saved.'));

        $this->redirectRoute('articles.index');
    }

    /**
     * @return mixed
     */
    public function uploadImage()
    {
        //al actualizar la imagen borre la anterior
        if ($oldImg = $this->article->image) {
            Storage::disk('public')->delete($oldImg);
        }

        //esto retorna la direccion de la imagen guardada
        return $this->image->store('/', 'public');
    }

    public function render()
    {
        return view('livewire.article-form',[
            'categories' => Category::pluck('name', 'id')
        ]);
    }
}
