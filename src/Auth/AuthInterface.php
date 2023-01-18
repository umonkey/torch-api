<?php

declare(strict_types=1);

namespace App\Auth;

use App\Database\Entities\UserEntity;
use Psr\Http\Message\ServerRequestInterface;

interface AuthInterface
{
    public function authenticate(ServerRequestInterface $request): UserEntity;
}
