<?php

namespace Tests\Feature\Livewire;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ArticlesFormTest extends TestCase
{
    use RefreshDatabase;

    /** @tets **/
    function guests_cannot_create_or_update_articles()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('articles.create'))
            ->assertRedirect('login');

        $article = Article::factory()->create();
        $this->actingAs($user)->get(route('articles.update',$article))
            ->assertRedirect('login');
    }

    /** @tets **/
    function article_form_renders_properly()
    {
        $this->get(route('articles.create'))
            ->assertSeeLivewire('article-form');

        $article = Article::factory()->create();
        $this->get(route('articles.update',$article))
            ->assertSeeLivewire('article-form');
    }

    /** @test **/
    function blade_template_is_wired_properly()
    {
        Livewire::test('article-form')
            ->assertSeeHtml('wire:submit.prevent="save"')
            ->assertSeeHtml('wire:model="article.title"')
            ->assertSeeHtml('wire:model="article.slug"')
            ->assertSeeHtml('wire:model="article.content"')
        ;
    }

    /** @tets **/
    function can_create_new_articles()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'New content from test file')
            ->call('save')
            ->assertStatus('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles',[
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'New content from test file',
            'user_id' => $user->id
        ]);
    }

    /** @tets **/
    function can_update_articles()
    {
        $article = Article::factory()->create();

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.slug', $article->slug)
            ->assertSet('article.content', $article->content)
            ->set('article.title', 'Update Title')
            ->set('article.slug', 'Update-slug')
            ->call('save')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseCount('articles', 1);

        $this->assertDatabaseHas('articles',[
            'title' => 'Update Title',
            'slug' => 'update-slug',
            'user_id' => $user->id,
        ]);
    }

    /** @tets **/
    function title_is_required()
    {
        Livewire::test('article-form')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['articles.title' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'title']))
        ;
    }

    /** @tets **/
    function slug_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', null)
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['articles.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug']))
        ;
    }

    /** @test */
    function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug']))
        ;
    }

    /** @test */
    function slug_must_only_contain_letters_numbers_dashes_and_underscores()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article$%^')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'alpha_dash'])
            ->assertSeeHtml(__('validation.alpha_dash', ['attribute' => 'slug']))
        ;
    }

    /** @tets **/
    function slug_is_generated_automatically()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Nuevo articulo')
            ->assertSet('article.slug', 'nuevo-articulo')
        ;
    }

    /** @test */
    function unique_rule_should_be_ignored_when_updating_the_same_slug()
    {
        $article = Article::factory()->create();

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
            ->set('article.title', 'New Article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasNoErrors(['article.slug' => 'unique'])
        ;
    }

    /** @tets **/
    function title_must_be_characters_min()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Art')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['articles.title' => 'min'])
            ->assertSeeHtml(__('validation.min.string', [
                'attribute' => 'title',
                'min' => 4
            ]))
        ;
    }

    /** @tets **/
    function content_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article new title')
            ->call('save')
            ->assertHasErrors(['articles.content' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'content']))
        ;
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

    /** @tets **/
    function real_time_validation_works_for_content()
    {
        Livewire::test('article-form')
            ->set('article.content', '')
            ->assertHasErrors(['article.content' => 'required'])
            ->set('article.content', 'New Article with content')
            ->assertHasNoErrors('article.content');
    }

}
