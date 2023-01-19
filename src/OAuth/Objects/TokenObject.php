<?php

declare(strict_types=1);

namespace App\OAuth\Objects;

use JsonSerializable;

class TokenObject implements JsonSerializable
{
    private ?string $accessToken = null;

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'access_token' => $this->accessToken,
        ];
    }

    public function withAccessToken(string $value): self
    {
        $this->accessToken = $value;
        return $this;
    }
}
