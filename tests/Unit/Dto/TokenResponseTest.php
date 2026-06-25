<?php

namespace Foutraz\Outlook\Tests\Unit\Dto;

use Foutraz\Outlook\Dto\TokenResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TokenResponseTest extends TestCase
{
    #[Test]
    public function it_maps_a_full_payload(): void
    {
        $before = time();

        $token = TokenResponse::fromArray([
            'access_token' => 'access',
            'refresh_token' => 'refresh',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'Calendars.Read User.Read',
        ]);

        $this->assertSame('access', $token->accessToken);
        $this->assertSame('refresh', $token->refreshToken);
        $this->assertSame(3600, $token->expiresIn);
        $this->assertSame('Bearer', $token->tokenType);
        $this->assertSame('Calendars.Read User.Read', $token->scope);
        $this->assertGreaterThanOrEqual($before + 3600, $token->expiresAt);
    }

    #[Test]
    public function it_defaults_optional_fields_to_null(): void
    {
        $token = TokenResponse::fromArray([
            'access_token' => 'access',
            'expires_in' => 0,
        ]);

        $this->assertNull($token->refreshToken);
        $this->assertNull($token->scope);
        $this->assertSame('Bearer', $token->tokenType);
        $this->assertSame(0, $token->expiresIn);
    }
}
