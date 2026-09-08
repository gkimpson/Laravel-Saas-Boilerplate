<?php

use App\Enums\TeamRole;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the products index page can be rendered', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    Product::factory()->for($team)->create(['name' => 'Existing Product']);

    $response = $this
        ->actingAs($user)
        ->get(route('products.index', $team));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('products/Index')
        ->has('products.data', 1)
        ->where('products.data.0.name', 'Existing Product'));
});

test('products can be created', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->post(route('products.store', $team), [
            'name' => 'Widget',
            'description' => 'A useful widget.',
            'price' => 1999,
            'stock' => 10,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'team_id' => $team->id,
        'name' => 'Widget',
        'price' => 1999,
        'stock' => 10,
    ]);
});

test('product creation requires name price and stock', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->post(route('products.store', $team), []);

    $response->assertSessionHasErrors(['name', 'price', 'stock']);
});

test('products can be updated', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $product = Product::factory()->for($team)->create(['name' => 'Old Name']);

    $response = $this
        ->actingAs($user)
        ->put(route('products.update', [$team, $product]), [
            'name' => 'New Name',
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New Name',
    ]);
});

test('products can be deleted', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $product = Product::factory()->for($team)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('products.destroy', [$team, $product]));

    $response->assertRedirect();
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('a member of one team cannot update or delete another teams product', function () {
    $userA = User::factory()->create();
    $teamA = Team::factory()->create();
    $teamA->members()->attach($userA, ['role' => TeamRole::Owner->value]);

    $teamB = Team::factory()->create();
    $productB = Product::factory()->for($teamB)->create(['name' => 'Untouched']);

    $this->actingAs($userA);

    $this->put(route('products.update', [$teamA, $productB]), [
        'name' => 'Hacked',
        'price' => 100,
        'stock' => 1,
    ])->assertNotFound();

    $this->delete(route('products.destroy', [$teamA, $productB]))
        ->assertNotFound();

    expect($productB->fresh()->name)->toBe('Untouched');
    $this->assertDatabaseHas('products', ['id' => $productB->id, 'deleted_at' => null]);
});

test('a non member cannot access another teams products page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('products.index', $team));

    $response->assertForbidden();
});
