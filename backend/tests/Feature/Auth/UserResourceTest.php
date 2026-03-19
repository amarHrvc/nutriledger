<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('UserResource returns JSON:API shape', function () {
    $user = User::factory()->create([
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'admin',
    ]);

    $resource = new UserResource($user);
    $data = $resource->resolve();

    expect($data)->toHaveKeys(['type', 'id', 'attributes'])
        ->and($data['type'])->toBe('users')
        ->and($data['id'])->toBe($user->id)
        ->and($data['attributes'])->toHaveKeys(['name', 'email', 'role'])
        ->and($data['attributes']['name'])->toBe('John Doe')
        ->and($data['attributes']['email'])->toBe('john@example.com')
        ->and($data['attributes']['role'])->toBe('admin');
});

test('UserResource collection returns JSON:API array', function () {
    $users = User::factory()->count(3)->create();

    $resources = UserResource::collection($users);
    $data = $resources->resolve();

    expect($data)->toBeArray()
        ->and(count($data))->toBe(3);

    foreach ($data as $item) {
        expect($item)->toHaveKeys(['type', 'id', 'attributes'])
            ->and($item['type'])->toBe('users')
            ->and($item['attributes'])->toHaveKeys(['name', 'email', 'role']);
    }
});
