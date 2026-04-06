<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginRateLimiterTest extends TestCase
{
    public function test_login_rate_limiter_is_registered(): void
    {
        // Test that the 'login' rate limiter is registered
        $this->assertNotNull(
            RateLimiter::limiter('login'),
            'Login rate limiter should be registered in AppServiceProvider'
        );
    }

    public function test_login_rate_limiter_enforces_5_per_minute_limit(): void
    {
        // Get the login limiter
        $limiter = RateLimiter::limiter('login');

        // The limiter should return a callable that creates a Limit instance
        $this->assertIsCallable($limiter);

        // Execute limiter with mock request
        $mockRequest = new class
        {
            public function ip()
            {
                return '127.0.0.1';
            }
        };

        $limit = $limiter($mockRequest);

        // Check that limit is set to 5 per minute
        $this->assertEquals(5, $limit->maxAttempts);
        $this->assertEquals('127.0.0.1', $limit->key);
    }
}
