<?php

namespace Tests\Feature\Livewire;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ArticlesFormTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    function can_create_new_articles()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.content', 'New content from test file')
            ->call('save')
            ->assertStatus('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles',[
            'title' => 'New Article',
            'content' => 'New content from test file'
        ]);
    }

    function can_ipdate_articles()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form',['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.content', $article->content)
            ->set('article.title', 'Update Title')
            ->call('save')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseCount('articles', 1);

        $this->assertDatabaseHas('articles',[
            'title' => 'Update Title'
        ]);
    }

    function title_is_required()
    {
        Livewire::test('article-form')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['articles.title' => 'required']);
    }

    function title_must_be_characters_min()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Art')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['articles.title' => 'min']);
    }

    function content_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article new title')
            ->call('save')
            ->assertHasErrors(['articles.content' => 'required']);
    }

    /** @tets **/
    function real_time_validation_works_for_title()
    {
        Livewire::test('article-form')
            ->set('article.title', '')
            ->assertHasErrors(['article.title' => 'required'])
            ->set('article.title', 'New')
            ->assertHasErrors(['article.title' => 'min'])
            ->set('article.title', 'New Article Title')
            ->assertHasNoErrors('article.title');
    }

    function real_time_validation_works_for_content()
    {
        Livewire::test('article-form')
            ->set('article.content', '')
            ->assertHasErrors(['article.content' => 'required'])
            ->set('article.content', 'New Article with content')
            ->assertHasNoErrors('article.content');
    }
}
