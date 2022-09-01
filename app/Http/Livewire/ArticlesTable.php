<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlesTable extends Component
{
    use WithPagination;

    public $searh = '';

    public $sortFiled = 'created_at';

    public $sortDirection = 'desc';

    public function sortBy($field)
    {
        $this->sortFiled === $field
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : $this->sortDirection = 'asc';

        $this->sortFiled = $field;
    }

    public function render()
    {
        return view('livewire.articles-table', [
            'articles' => Article::query()
                ->where('title', 'like',"%{$this->searh}%")
                ->orderBy($this->sortFiled,$this->sortDirection)
                ->paginate(5)
        ]);
    }
}
