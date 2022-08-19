<?php

namespace Tests\Feature\Livewire;

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
}
