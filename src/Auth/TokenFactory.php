<?php

declare(strict_types=1);

namespace App\Auth;

use App\Core\Config;
use App\Exceptions\ConfigException;
use App\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class TokenFactory
{
    private readonly string $algo;

    private readonly string $secret;

    /**
     * @throws ConfigException
     */
    public function __construct(Config $config)
    {
        $this->algo = $config->requireString('jwt.algo');
        $this->secret = $config->requireString('jwt.secret');
    }

    /**
     * @param array<mixed> $data
     */
    public function encode(array $data): string
    {
        return JWT::encode($data, $this->secret, $this->algo);
    }

    /**
     * @return array<mixed>
     * @throws UnauthorizedException
     */
    public function decode(string $token): array
    {
        try {
            $key = new Key($this->secret, $this->algo);
            $object = JWT::decode($token, $key);

            return (array)$object;
        } catch (Throwable $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }
}
