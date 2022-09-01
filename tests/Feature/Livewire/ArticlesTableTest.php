<?php

namespace Tests\Feature\livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticlesTableTest extends TestCase
{
    function articles_component_renders_properly()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('articles.index'))
            ->assertSeeLivewire('articles');
    }
}
