<?php

namespace Tests\Unit\Traits;

use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class ApiResponsesTester
{
    use ApiResponses;

    public function testOk(string $message, ?array $data = null): JsonResponse
    {
        return $this->ok($message, $data);
    }

    public function testCreated(string $message, ?array $data = null): JsonResponse
    {
        return $this->created($message, $data);
    }

    public function testSuccess(string $message, int $statusCode = 200, ?array $data = null): JsonResponse
    {
        return $this->success($message, $statusCode, $data);
    }
}

describe('ApiResponses Trait', function () {
    beforeEach(function () {
        $this->tester = new ApiResponsesTester;
    });

    describe('ok() method', function () {
        test('returns 200 status code', function () {
            $response = $this->tester->testOk('Success');

            expect($response)->toBeInstanceOf(JsonResponse::class)
                ->and($response->status())->toBe(200);
        });

        test('includes message in response', function () {
            $response = $this->tester->testOk('Test message');
            $data = json_decode($response->getContent(), true);

            expect($data['message'])->toBe('Test message');
        });

        test('accepts optional data parameter', function () {
            $testData = ['id' => 1, 'name' => 'Test'];
            $response = $this->tester->testOk('Success', $testData);
            $data = json_decode($response->getContent(), true);

            expect($data['data'])->toBe($testData);
        });

        test('includes status code in response', function () {
            $response = $this->tester->testOk('Success');
            $data = json_decode($response->getContent(), true);

            expect($data['status'])->toBe(200);
        });

        test('works without data parameter (backward compatibility)', function () {
            $response = $this->tester->testOk('Success without data');
            $data = json_decode($response->getContent(), true);

            expect($data)->toHaveKeys(['message', 'status'])
                ->and($data['data'] ?? null)->toBeNull();
        });

        test('includes both message and data when provided', function () {
            $testData = ['id' => 42, 'value' => 'test'];
            $response = $this->tester->testOk('Operation successful', $testData);
            $data = json_decode($response->getContent(), true);

            expect($data['message'])->toBe('Operation successful')
                ->and($data['data'])->toBe($testData)
                ->and($data['status'])->toBe(200);
        });
    });

    describe('created() method', function () {
        test('returns 201 status code', function () {
            $response = $this->tester->testCreated('Created');

            expect($response)->toBeInstanceOf(JsonResponse::class)
                ->and($response->status())->toBe(201);
        });

        test('includes message in response', function () {
            $response = $this->tester->testCreated('Resource created');
            $data = json_decode($response->getContent(), true);

            expect($data['message'])->toBe('Resource created');
        });

        test('accepts optional data parameter', function () {
            $testData = ['id' => 123, 'name' => 'New Resource'];
            $response = $this->tester->testCreated('Created', $testData);
            $data = json_decode($response->getContent(), true);

            expect($data['data'])->toBe($testData);
        });

        test('includes status code 201 in response', function () {
            $response = $this->tester->testCreated('Created');
            $data = json_decode($response->getContent(), true);

            expect($data['status'])->toBe(201);
        });

        test('works without data parameter (backward compatibility)', function () {
            $response = $this->tester->testCreated('Resource created without data');
            $data = json_decode($response->getContent(), true);

            expect($data)->toHaveKeys(['message', 'status'])
                ->and($data['data'] ?? null)->toBeNull();
        });

        test('includes both message and data when provided', function () {
            $testData = ['id' => 999, 'email' => 'new@example.com'];
            $response = $this->tester->testCreated('User created', $testData);
            $data = json_decode($response->getContent(), true);

            expect($data['message'])->toBe('User created')
                ->and($data['data'])->toBe($testData)
                ->and($data['status'])->toBe(201);
        });
    });

    describe('success() method', function () {
        test('returns provided status code', function () {
            $response = $this->tester->testSuccess('Test', 200);

            expect($response->status())->toBe(200);
        });

        test('accepts optional data parameter', function () {
            $testData = ['result' => 'success'];
            $response = $this->tester->testSuccess('Test', 200, $testData);
            $data = json_decode($response->getContent(), true);

            expect($data['data'])->toBe($testData);
        });

        test('works without data parameter (backward compatibility)', function () {
            $response = $this->tester->testSuccess('Test', 200);
            $data = json_decode($response->getContent(), true);

            expect($data)->toHaveKeys(['message', 'status'])
                ->and($data['data'] ?? null)->toBeNull();
        });
    });
});
