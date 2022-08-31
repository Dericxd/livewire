<?php

namespace Tests\Feature\Livewire;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('articles.create'))
            ->assertSeeLivewire('article-form');

        $article = Article::factory()->create();

        $this->actingAs($user)->get(route('articles.update',$article))
            ->assertSeeLivewire('article-form')
            ->assertDontSeeText(__('Delete'));
    }

    /** @test **/
    function blade_template_is_wired_properly()
    {
        Livewire::test('article-form')
            ->assertSeeHtml('wire:submit.prevent="save"')
            ->assertSeeHtml('wire:model="article.title"')
            ->assertSeeHtml('wire:model="article.slug"');
        ;
    }

    /** @tets **/
    function can_create_new_articles()
    {
        Storage::fake('public');

        $img = UploadedFile::fake()->image('post-img.png');

        $user = User::factory()->create();

        $category = Category::factory()->create();

        Livewire::actingAs($user)->test('article-form')
            ->set('image', $img)
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'New content from test file')
            ->set('article.category_id', $category)
            ->call('save')
            ->assertStatus('status')
            ->assertRedirect(route('articles.index'));

//        dd(Storage::disk('public')->files()[0]);

        $this->assertDatabaseHas('articles',[
            'image' => $imgPath = Storage::disk('public')->files()[0],
            'title' => 'New Article',
            'slug' => 'new-article',
            'content' => 'New content from test file',
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        Storage::disk('public')->assertExists($imgPath);
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
            ->assertSet('article.category_id', $article->category->id)
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
    function can_delete_articles()
    {
        Storage::fake();

        $imagePath = UploadedFile::fake()
            ->image('image.png')
            ->store('/', 'public')
        ;

        $article = Article::factory()->create([
            'image' => $imagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
            ->call('delete')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'))
        ;

        Storage::disk('public')->assertMissing($imagePath);

        $this->assertDatabaseCount('articles', 0);
    }

    /** @tets **/
    function can_update_articles_image()
    {
        Storage::fake('public');

        $oldImg = UploadedFile::fake()->image('oldImg.png');
        $oldImgPath = $oldImg->store('/','public');
        $newImg = UploadedFile::fake()->image('newImg.png');
//        $newImgPath = $newImg->store('/','public');

        $article = Article::factory()->create([
            'image' => $oldImgPath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
            ->set('image', $newImg)
            ->call('save')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        Storage::disk('public')
            ->assertExists($article->fresh()->image)
            ->assertMissing($oldImgPath);

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
    function image_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article title')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['image' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'image']))
        ;
    }

    /** @tets **/
    function image_field_must_be_of_type_image()
    {
        Livewire::test('article-form')
            ->set('image', 'string-not-allowed')
            ->call('save')
            ->assertHasErrors(['image' => 'image'])
            ->assertSeeHtml(__('validation.image', ['attribute' => 'image']))
        ;
    }

    /** @tets **/
    function image_must_be_2mb_max()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('post-image.png')->size(3000);

        Livewire::test('article-form')
            ->set('image', $image)
            ->call('save')
            ->assertHasErrors(['image' => 'max'])
            ->assertSeeHtml(__('validation.max.file', [
                'attribute' => 'image',
                'max' => '2048'
            ]))
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

    /** @tets **/
    function category_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'Article content')
            ->set('article.category_id', null)
            ->call('save')
            ->assertHasErrors(['article.category_id' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'category id']))
        ;
    }

    /** @tets **/
    function category_must_exist_in_database()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'Article content')
            ->set('article.category_id', 1)
            ->call('save')
            ->assertHasErrors(['article.category_id' => 'exists'])
            ->assertSeeHtml(__('validation.exists', ['attribute' => 'category id']))
        ;
    }

    /** @test */
    function can_create_new_category()
    {
        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Nueva categoria')
            ->assertSet('newCategory.slug', 'Nueva categoria')
            ->call('saveNewCategory')
            ->assertSet('article.category_id', Category::first()->id)
            ->assertSet('showCategoryModal', false)
        ;

        $this->assertDatabaseCount('categories', 1);
    }

    /** @test */
    function new_category_name_is_required()
    {
        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.slug', 'Laravel')
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.name' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'name']))
        ;
    }

    /** @test */
    function new_category_name_must_be_unique()
    {
        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Laravel')
            ->set('newCategory.slug', $category->slug)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug']))
        ;
    }

    /** @test */
    function new_category_slug_must_be_unique()
    {
        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', $category->name)
            ->set('newCategory.slug', 'Laravel')
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.name' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'name']))
        ;
    }

    /** @test */
    function new_category_slug_is_required()
    {
        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Extras')
            ->set('newCategory.slug', null)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'required'])
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
