<?php

use App\Http\Requests\Api\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

describe('UpdateUserRequest', function () {
    describe('authorization', function () {
        test('admin user can authorize', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);

            expect($request->authorize())->toBeTrue();
        });

        test('non-admin user cannot authorize', function () {
            $patient = User::factory()->create(['role' => 'pacijent']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $patient);

            expect($request->authorize())->toBeFalse();
        });

        test('doctor user cannot authorize', function () {
            $doctor = User::factory()->create(['role' => 'doktor']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $doctor);

            expect($request->authorize())->toBeFalse();
        });
    });

    describe('validation rules', function () {
        test('name is optional but must be string and max 255 if provided', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['name' => 'Valid Name']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->passes())->toBeTrue();
        });

        test('name fails if too long', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['name' => str_repeat('a', 256)]);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('name'))->toBeTrue();
        });

        test('email is optional but must be valid email if provided', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['email' => 'valid@example.com']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->passes())->toBeTrue();
        });

        test('email fails if invalid format', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['email' => 'invalid-email']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('email'))->toBeTrue();
        });

        test('password is optional but must be string and min 8 if provided', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['password' => 'validpassword123']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->passes())->toBeTrue();
        });

        test('password fails if less than 8 characters', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['password' => 'short']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('password'))->toBeTrue();
        });

        test('role is optional but must be one of admin, doktor, pacijent if provided', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['role' => 'doktor']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->passes())->toBeTrue();
        });

        test('role fails if not in allowed values', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge(['role' => 'invalid_role']);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('role'))->toBeTrue();
        });

        test('all fields are optional together', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $request = new UpdateUserRequest;
            $request->setUserResolver(fn () => $admin);
            $request->merge([]);

            $validator = Validator::make(
                $request->all(),
                $request->rules()
            );

            expect($validator->passes())->toBeTrue();
        });
    });
});
