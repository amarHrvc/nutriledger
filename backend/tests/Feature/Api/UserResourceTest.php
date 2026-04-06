<?php

use App\Http\Resources\Api\UserResource;
use App\Models\Patient;
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

    //    dd($user);

    $resource = new UserResource($user);
    $data = $resource->resolve();

    //    dd($data);
    expect($data)->toHaveKeys(['type', 'id', 'attributes'])
        ->and($data['type'])->toBe('users')
        ->and($data['id'])->toBe($user->id)
        ->and($data['attributes'])->toHaveKeys(['name', 'email', 'role', 'createdAt', 'updatedAt', 'deletedAt'])
        ->and($data['attributes']['name'])->toBe('John Doe')
        ->and($data['attributes']['email'])->toBe('john@example.com')
        ->and($data['attributes']['role'])->toBe('admin')
        ->and($data['attributes']['createdAt'])->toBe($user->created_at->toIso8601String())
        ->and($data['attributes']['updatedAt'])->toBe($user->updated_at->toIso8601String())
        ->and($data['attributes'])->not()->toHaveKey('password')
        ->and($data['attributes'])->not()->toHaveKey('remember_token')
        ->and($data['attributes'])->not()->toHaveKey('email_verified_at')
        ->and($data)->toHaveKey('links')
        ->and($data['links'])->toHaveKey('self');
});

test('UserResource collection returns JSON:API array', function () {
    $users = User::factory()->count(3)->create();

    $resources = UserResource::collection($users);
    $data = $resources->resolve();

    dump($data);

    expect($data)->toBeArray()
        ->and(count($data))->toBe(3);

    foreach ($data as $item) {
        expect($item)->toHaveKeys(['type', 'id', 'attributes'])
            ->and($item['type'])->toBe('users')
            ->and($item['attributes'])->toHaveKeys(['name', 'email', 'role',  'createdAt', 'updatedAt', 'deletedAt']);
    }
});

test('UserResource has related data', function () {
    $patient = Patient::factory()->create();
    /** @var User $user */
    $user = $patient->user;

    $data = (new UserResource($user->load('patient')))->resolve();

    expect($data)->toHaveKeys(['type', 'id', 'attributes'])
        ->and($data['type'])->toBe('users')
        ->and($data['id'])->toBe($user->id)
        ->and($data)->toHaveKey('relationships')
        ->and($data['relationships'])->toHaveKey('patient');

});
test('UserResource excludees patient when relation is not laoded', function () {
    $patient = Patient::factory()->create();
    /** @var User $user */
    $user = $patient->user;

    $data = (new UserResource($user))->resolve();

    expect($data)->toHaveKeys(['type', 'id', 'attributes'])
        ->and($data['type'])->toBe('users')
        ->and($data['id'])->toBe($user->id)
        ->and($data)->toHaveKey('relationships')
        ->and($data['relationships'])->not()->toHaveKey('patient')
        ->and($data['relationships']['patient'] ?? null)->toBeNull();

});
