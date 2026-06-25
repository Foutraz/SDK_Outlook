<?php

namespace Foutraz\Outlook\Tests\Feature\Actions;

use Foutraz\Outlook\Dto\TokenResponse;
use Foutraz\Outlook\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ManagesAuthenticationTest extends TestCase
{
    #[Test]
    public function it_builds_the_authorize_url_with_default_scope(): void
    {
        $manager = $this->managerWithResponses([]);

        $url = $manager->auth()->authorizeUrl();

        $this->assertStringContainsString('https://login.microsoftonline.com/common/oauth2/v2.0/authorize', $url);
        $this->assertStringContainsString('client_id=client-id', $url);
        $this->assertStringContainsString('scope=Calendars.Read+offline_access+User.Read', $url);
        $this->assertStringContainsString('response_type=code', $url);
    }

    #[Test]
    public function it_allows_overriding_the_scope(): void
    {
        $manager = $this->managerWithResponses([]);

        $url = $manager->auth()->authorizeUrl(['Calendars.ReadWrite']);

        $this->assertStringContainsString('scope=Calendars.ReadWrite', $url);
    }

    #[Test]
    public function it_exchanges_a_code_for_a_token_response(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, [
                'access_token' => 'a',
                'refresh_token' => 'r',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'Calendars.Read',
            ]),
        ]);

        $token = $manager->auth()->exchangeToken('the-code');

        $this->assertInstanceOf(TokenResponse::class, $token);
        $this->assertSame('a', $token->accessToken);
        $this->assertSame('r', $token->refreshToken);
        $this->assertSame('https://login.microsoftonline.com/common/oauth2/v2.0/token', $this->lastRequestUri());
        $this->assertSame('application/x-www-form-urlencoded', $this->lastRequestContentType());

        $form = $this->lastRequestForm();
        $this->assertSame('client-id', $form['client_id']);
        $this->assertSame('client-secret', $form['client_secret']);
        $this->assertSame('the-code', $form['code']);
        $this->assertSame('https://example.test/callback', $form['redirect_uri']);
        $this->assertSame('authorization_code', $form['grant_type']);
    }

    #[Test]
    public function it_refreshes_a_token(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, [
                'access_token' => 'a2',
                'refresh_token' => 'r2',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ]),
        ]);

        $token = $manager->auth()->refreshToken('old-refresh');

        $this->assertSame('a2', $token->accessToken);
        $this->assertSame('r2', $token->refreshToken);
        $this->assertNull($token->scope);
        $this->assertSame('https://login.microsoftonline.com/common/oauth2/v2.0/token', $this->lastRequestUri());
        $this->assertSame('application/x-www-form-urlencoded', $this->lastRequestContentType());

        $form = $this->lastRequestForm();
        $this->assertSame('refresh_token', $form['grant_type']);
        $this->assertSame('old-refresh', $form['refresh_token']);
    }
}
