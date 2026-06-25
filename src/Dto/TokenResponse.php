<?php

namespace Foutraz\Outlook\Dto;

class TokenResponse
{
    public function __construct(
        public string $accessToken,
        public ?string $refreshToken,
        public int $expiresAt,
        public int $expiresIn,
        public string $tokenType,
        public ?string $scope,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $expiresIn = (int) ($data['expires_in'] ?? 0);

        return new self(
            (string) ($data['access_token'] ?? ''),
            isset($data['refresh_token']) ? (string) $data['refresh_token'] : null,
            time() + $expiresIn,
            $expiresIn,
            (string) ($data['token_type'] ?? 'Bearer'),
            isset($data['scope']) ? (string) $data['scope'] : null,
        );
    }
}
