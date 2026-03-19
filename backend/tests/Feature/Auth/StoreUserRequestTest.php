<?php

use App\Http\Requests\Api\StoreUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('StoreUserRequest Authorization', function () {
    test('authorize returns true for admin user', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = new StoreUserRequest;
        $request->setUserResolver(fn () => $admin);

        expect($request->authorize())->toBeTrue();
    });

    test('authorize returns false for non-admin user', function () {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $request = new StoreUserRequest;
        $request->setUserResolver(fn () => $doctor);

        expect($request->authorize())->toBeFalse();
    });

    test('authorize returns false for patient user', function () {
        $patient = User::factory()->create(['role' => 'pacijent']);
        $request = new StoreUserRequest;
        $request->setUserResolver(fn () => $patient);

        expect($request->authorize())->toBeFalse();
    });
});

describe('StoreUserRequest Validation', function () {
    test('name is required', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('name'))->toBeTrue();
    });

    test('name must be string', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 123,
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('name'))->toBeTrue();
    });

    test('name must not exceed 255 characters', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('name'))->toBeTrue();
    });

    test('email is required', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    test('email must be valid email format', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    test('email must be unique in users table', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    test('password is required', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    test('password must be string', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 123,
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    test('password must be at least 8 characters', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'role' => 'pacijent',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    test('role is required', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('role'))->toBeTrue();
    });

    test('role must be one of admin, doktor, pacijent', function () {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'invalid_role',
        ]);

        $validator = validator($request->all(), $request->rules());
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('role'))->toBeTrue();
    });

    test('accepts all valid roles', function () {
        collect(['admin', 'doktor', 'pacijent'])->each(function ($role) {
            $request = new StoreUserRequest;
            $request->merge([
                'name' => "Test User {$role}",
                'email' => "test{$role}@example.com",
                'password' => 'password123',
                'role' => $role,
            ]);

            $validator = validator($request->all(), $request->rules());
            expect($validator->errors()->has('role'))->toBeFalse();
        });
    });
});
