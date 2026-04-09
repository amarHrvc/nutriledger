<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

beforeEach(function () {
    $this->withoutMiddleware(RestrictedDocsAccess::class);
});

test('spec endpoint returns 200', function () {
    $this->getJson('/docs/api.json')->assertOk();
});

test('core API paths are present in spec', function () {
    $spec = $this->getJson('/docs/api.json')->json();

    expect($spec['paths'])->toHaveKey('/login')
        ->and($spec['paths'])->toHaveKey('/users');
});

test('docs UI is accessible', function () {
    $this->get('/docs/api')->assertOk();
});

test('bearer security scheme is registered in components', function () {
    $spec = $this->getJson('/docs/api.json')->json();

    expect($spec['components']['securitySchemes'])->toHaveKey('http');
    expect($spec['components']['securitySchemes']['http']['type'])->toBe('http');
    expect($spec['components']['securitySchemes']['http']['scheme'])->toBe('bearer');
});

test('POST /api/users has request body schema in spec', function () {
    $spec = $this->getJson('/docs/api.json')->json();

    expect($spec['paths']['/users']['post'])->toHaveKey('requestBody');
});

test('docs routes are absent from spec paths', function () {
    $spec = $this->getJson('/docs/api.json')->json();

    expect($spec['paths'])->not->toHaveKey('/docs/api');
    expect($spec['paths'])->not->toHaveKey('/docs/api.json');
});
