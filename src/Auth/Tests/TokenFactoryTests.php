<?php

declare(strict_types=1);

namespace App\Auth\Tests;

use App\Auth\Exceptions\BadTokenFormatException;
use App\Auth\Exceptions\BadTokenSignatureException;
use App\Auth\Exceptions\TokenExpiredException;
use App\Auth\TokenFactory;
use App\Core\AbstractTestCase;
use App\Core\Config;
use App\Exceptions\UnauthorizedException;
use Psr\Container\ContainerExceptionInterface;

class TokenFactoryTests extends AbstractTestCase
{
    private readonly TokenFactory $factory;

    /**
     * Make sure we get the right config to get predictable results.
     *
     * @throws ContainerExceptionInterface
     */
    public function testConfig(): void
    {
        $config = $this->container->get(Config::class);

        self::assertEquals('HS256', $config->getString('jwt.algo'));
        self::assertEquals('secret', $config->getString('jwt.secret'));
    }

    public function testEncode(): void
    {
        $token = $this->factory->encode([
            'sub' => 'phpunit',
        ]);

        self::assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJwaHB1bml0In0.g_fV5UD_RfpTHdV5VgUB1sjxZlerRe-j923uzbgdbd0', $token);
    }

    /**
     * @throws UnauthorizedException
     */
    public function testDecode(): void
    {
        $token = $this->factory->encode([
            'sub' => 'phpunit',
        ]);

        $payload = $this->factory->decode($token);

        self::assertEquals([
            'sub' => 'phpunit',
        ], $payload);
    }

    /**
     * @throws UnauthorizedException
     */
    public function testDecodeBroken(): void
    {
        $this->expectException(BadTokenFormatException::class);
        $this->factory->decode('foobar');
    }

    /**
     * @throws UnauthorizedException
     */
    public function testExpiredTokenDecode(): void
    {
        $this->expectException(TokenExpiredException::class);

        $token = $this->factory->encode([
            'sub' => 'phpunit',
            'exp' => time() - 86400,
        ]);

        $this->factory->decode($token);
    }

    /**
     * @throws UnauthorizedException
     */
    public function testCorruptTokenDecode(): void
    {
        $this->expectException(BadTokenSignatureException::class);

        $token = $this->factory->encode([
            'sub' => 'phpunit',
        ]) . 'abc';

        $this->factory->decode($token);
    }

    /**
     * @throws UnauthorizedException
     */
    public function testOtherException(): void
    {
        $this->expectException(UnauthorizedException::class);

        $token = $this->factory->encode([
            'sub' => 'phpunit',
            'nbf' => time() + 86400,
        ]);

        $this->factory->decode($token);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->container->get(TokenFactory::class);
    }
}
