<?php

declare(strict_types=1);

namespace Integration;

use App\Core\Testing\AbstractIntegrationTests;
use App\Core\Testing\ApiResponse;
use App\Core\Testing\Exceptions\ApiException;
use App\Exceptions\BadResponseException;
use App\Exceptions\ConfigException;

class LoginTests extends AbstractIntegrationTests
{
    /**
     * @throws ApiException
     * @throws BadResponseException
     * @throws ConfigException
     */
    public function testUserNotFound(): void
    {
        $login = $this->env->req('API_LOGIN');
        $password = $this->env->req('API_PASSWORD');

        $res = $this->logIn($login . $login, $password);

        self::assertEquals(401, $res->getStatusCode());
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     * @throws ConfigException
     */
    public function testBadPassword(): void
    {
        $login = $this->env->req('API_LOGIN');
        $password = $this->env->req('API_PASSWORD');

        $res = $this->logIn($login, $password . $password);

        self::assertEquals(401, $res->getStatusCode());
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     * @throws ConfigException
     */
    public function testSuccess(): void
    {
        $login = $this->env->req('API_LOGIN');
        $password = $this->env->req('API_PASSWORD');

        $res = $this->logIn($login, $password);
        self::assertEquals(200, $res->getStatusCode());

        $payload = $res->getJSON();
        self::assertArrayHasKey('access_token', $payload);
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     */
    private function logIn(string $userName, string $password): ApiResponse
    {
        return $this->request('POST', '/v1/oauth/password', [
            'form_params' => [
                'grant_type' => 'password',
                'username' => $userName,
                'password' => $password,
            ],
        ]);
    }
}
