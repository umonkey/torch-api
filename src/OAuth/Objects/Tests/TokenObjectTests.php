<?php

declare(strict_types=1);

namespace App\OAuth\Objects\Tests;

use App\Core\AbstractTestCase;
use App\OAuth\Objects\TokenObject;

class TokenObjectTests extends AbstractTestCase
{
    public function testSerialize(): void
    {
        $token = (new TokenObject())
            ->withAccessToken('foobar');

        self::assertEquals([
            'access_token' => 'foobar',
        ], $token->jsonSerialize());
    }
}
