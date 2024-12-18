<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

test('user tenancy is applied to list', function () {

    $authUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $authUserLink = Link::factory()->for($authUser)->create();
    $otherUserLinks = Link::factory(2)->for($otherUser)->create();

    actingAs($authUser);

    get(route('links.index'))
        ->assertSee($authUserLink->title)
        ->assertDontSee($otherUserLinks->first()->title)
        ->assertDontSee($otherUserLinks->last()->title);

});

test('user tenancy prevents access to other user data', function () {

    $authUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $authUserLink = Link::factory()->for($authUser)->create();
    $otherUserLink = Link::factory()->for($otherUser)->create();

    actingAs($authUser);

    get(route('links.edit', ['link' => $otherUserLink]))
        ->assertStatus(404);

    put(route('links.update', ['link' => $otherUserLink]), [
        'title' => 'Updated Title',
        'url' => 'https://updated.com',
    ])
        ->assertStatus(404);

    delete(route('links.destroy', ['link' => $otherUserLink]))
        ->assertStatus(404);

});
