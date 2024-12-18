<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

it('can create a link', function () {

    $user = User::factory()->create();

    actingAs($user);

    // Make sure that the form laods for our user

    get(route('links.create'))
        ->assertStatus(200)
        ->assertSeeText('Title')
        ->assertSeeText('URL')
        ->assertSeeText('Description')
        ->assertSeeText('Position')
        ->assertSeeText('Create Link');

    // Assume form was submitted

    post(route('links.store'), [
        'title' => 'Test Link',
        'url' => 'https://test.com',
        'description' => 'Test Description',
        'position' => 1,
    ])

        ->assertRedirect(route('links.index'))

      // Ensure the message is flashed

        ->assertSessionHas('message', 'Link created successfully.');

    // Ensure the link was created in the database for the user

    assertDatabaseHas('links', [
        'title' => 'Test Link',
        'url' => 'https://test.com',
        'description' => 'Test Description',
        'position' => 1,
        'user_id' => $user->id,
    ]);

});

it('Can update link', function () {

    $user = User::factory()->create();
    $link = Link::factory()->for($user)->create();

    actingAs($user);

    // Make sure that the form laods for our user

    get(route('links.edit', ['link' => $link]))
        ->assertStatus(200)
        ->assertSeeText('Title')
        ->assertSeeText('URL')
        ->assertSeeText('Description')
        ->assertSeeText('Position')
        ->assertSeeText('Save');

    put(route('links.update', ['link' => $link]), [
        'title' => 'Updated Link',
        'url' => 'https://updated.com',
        'description' => 'Updated Description',
        'position' => 2,
    ])
        ->assertSessionHas('message', 'Link updated successfully.')
        ->assertRedirect(route('links.index'));

    assertDatabaseHas('links', [
        'title' => 'Updated Link',
        'url' => 'https://updated.com',
        'description' => 'Updated Description',
        'position' => 2,
        'user_id' => $user->id,
        'id' => $link->id,
    ]);

});

it('Can delete a link', function () {

    $user = User::factory()->create();
    $link = Link::factory()->for($user)->create();

    actingAs($user);

    get(route('links.index'))
        ->assertStatus(200)
        ->assertSeeText('Links List')
        ->assertSeeText('Title')
        ->assertSeeText('URL')
        ->assertSeeText('Add New Link')
        ->assertSeeText('Actions')
        ->assertSeeText('Edit')
        ->assertSeeText('Delete');

    // Assume form was submitted

    delete(route('links.destroy', ['link' => $link]), [
        '_method' => 'DELETE',
    ])
        ->assertRedirect(route('links.index'))
        ->assertSessionHas('message', 'Link deleted successfully.');

    // Ensure the link was deleted from the database for the User

    assertDatabaseMissing('links', [
        'id' => $link->id,
    ]);

});

it('Can list links', function () {

    $user = User::factory()->create();
    $links = Link::factory(2)->for($user)->create();

    actingAs($user);

    // Make sure the links are listed for our User

    get(route('links.index'))
        ->assertStatus(200)
        ->assertSeeText('Links List')
        ->assertSeeText('Title')
        ->assertSeeText('URL')
        ->assertSeeText('Add New Link')
        ->assertSeeText('Actions')
        ->assertSeeText('Edit')
        ->assertSeeText('Delete')

        // Ensure the links are displayed in DESC order (reverse of creation)

        ->assertSeeTextInOrder($links->pluck('title')->reverse()->toArray());

});

it('Can create link with no position but still generate one', function () {

    $user = User::factory()->create();

    actingAs($user);

    // Assume form was submitted

    post(route('links.store'), [
        'title' => 'Test Link',
        'url' => 'https://test.com',
        'description' => 'Test Description',
    ])
        ->assertRedirect(route('links.index'))

        // Ensure the message is flashed

        ->assertSessionHas('message', 'Link created successfully.');

    // Ensure the link was created in the database for the User

    assertDatabaseHas('links', [
        'title' => 'Test Link',
        'url' => 'https://test.com',
        'description' => 'Test Description',
        'position' => 1,
        'user_id' => $user->id,
    ]);

});
