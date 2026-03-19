<?php

use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('RegisterRequest authorizes public access', function () {
    $request = new RegisterRequest;
    expect($request->authorize())->toBeTrue();
});

test('name is required', function () {
    $request = new RegisterRequest;
    $request->merge([
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('name must be a string', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 123,
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('name max length is 255 characters', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('email is required', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('email must be a valid email format', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('email must be unique in users table', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('password is required', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('password must be a string', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 123,
        'password_confirmation' => '123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('password minimum length is 8 characters', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('password must be confirmed', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('role is optional', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeTrue();
});

test('role must be one of admin, doktor, pacijent', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'invalid_role',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('role'))->toBeTrue();
});

test('valid registration data passes validation', function () {
    $request = new RegisterRequest;
    $request->merge([
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'pacijent',
    ]);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeTrue();
});
