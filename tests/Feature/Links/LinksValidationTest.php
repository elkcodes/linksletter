<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

test('validates link url', function () {

    $user = User::factory()->create();

    $link = Link::factory()->for($user)->create();

    actingAs($user);

    // Check for URL validation

    post(route('links.store'), [
        'url' => 'invalid-url',
        'title' => 'Link Title',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field must be a valid URL.']);

    put(route('links.update', ['link' => $link]), [
        'url' => 'invalid-url',
        'title' => 'Link Title',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field must be a valid URL.']);

    // Check for URL required validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field is required.']);

    put(route('links.update', ['link' => $link]), [
        'title' => 'Link Title',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field is required.']);

    // Check for URL string validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'url' => 123,
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field must be a string.']);

    put(route('links.update', ['link' => $link]), [
        'title' => 'Link Title',
        'url' => 123,
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['url' => 'The url field must be a string.']);

});

test('validates link title', function () {

    $user = User::factory()->create();

    $link = Link::factory()->for($user)->create();

    actingAs($user);

    // Check for title required validation

    post(route('links.store'), [
        'description' => 'Link Description',
        'position' => 1,
        'url' => 'https://www.gavelyvicaje.org.au',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['title' => 'The title field is required.']);

    put(route('links.update', ['link' => $link]), [
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['title' => 'The title field is required.']);

    // Check for title string validation

    post(route('links.store'), [
        'title' => 123,
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['title' => 'The title field must be a string.']);

    put(route('links.update', ['link' => $link]), [
        'title' => 123,
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 'Link Description',
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['title' => 'The title field must be a string.']);

});

test('validates link description', function () {

    $user = User::factory()->create();

    $link = Link::factory()->for($user)->create();

    actingAs($user);

    // Check for description nullable validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'position' => 1,
        'url' => 'https://www.gavelyvicaje.org.au',
    ])
        ->assertRedirect(route('links.index'))
        ->assertSessionHasNoErrors();

    put(route('links.update', ['link' => $link]), [
        'url' => 'https://www.gavelyvicaje.org.au',
        'title' => 'Link Title',
        'position' => 1,
    ])
        ->assertRedirect(route('links.index'))
        ->assertSessionHasNoErrors();

    // Check for description string validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 123,
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['description' => 'The description field must be a string.']);

    put(route('links.update', ['link' => $link]), [
        'title' => 'Link Title',
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 123,
        'position' => 1,
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['description' => 'The description field must be a string.']);

});

test('validates link position', function () {

    $user = User::factory()->create();

    $link = Link::factory()->for($user)->create();

    actingAs($user);

    // Check for position nullable validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'description' => 'Link Description',
        'url' => 'https://www.gavelyvicaje.org.au',
    ])
        ->assertRedirect(route('links.index'))
        ->assertSessionHasNoErrors();

    put(route('links.update', ['link' => $link]), [
        'url' => 'https://www.gavelyvicaje.org.au',
        'title' => 'Link Title',
        'description' => 'Link Description',
    ])
        ->assertRedirect(route('links.index'))
        ->assertSessionHasNoErrors();

    // Check for position interger validation

    post(route('links.store'), [
        'title' => 'Link Title',
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 'Link Description',
        'position' => 'invalid',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['position' => 'The position field must be an integer.']);

    put(route('links.update', ['link' => $link]), [
        'title' => 'Link Title',
        'url' => 'https://www.gavelyvicaje.org.au',
        'description' => 'Link Description',
        'position' => 'invalid',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['position' => 'The position field must be an integer.']);

});
