<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Exceptions\BadTokenFormatException;
use App\Auth\Exceptions\BadTokenSignatureException;
use App\Auth\Exceptions\TokenExpiredException;
use App\Core\Config;
use App\Exceptions\ConfigException;
use App\Exceptions\UnauthorizedException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Throwable;
use UnexpectedValueException;

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
     * Low level JWT encoding.
     *
     * For a list of possible claims, see RFC 7519 section 4:
     * https://www.rfc-editor.org/rfc/rfc7519#section-4
     *
     * @param mixed[] $data
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
        } catch (ExpiredException) {
            throw new TokenExpiredException();
        } catch (SignatureInvalidException) {
            throw new BadTokenSignatureException();
        } catch (Throwable $e) {
            if ($e->getMessage() === 'Wrong number of segments') {
                throw new BadTokenFormatException();
            }
        }

        throw new UnauthorizedException($e->getMessage());
    }
}
