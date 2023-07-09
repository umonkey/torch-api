<?php

declare(strict_types=1);

namespace Integration;

use App\Core\Testing\AbstractIntegrationTests;
use App\Core\Testing\ApiResponse;
use App\Core\Testing\Exceptions\ApiException;
use App\Exceptions\BadResponseException;
use App\Exceptions\ConfigException;

class PagesTests extends AbstractIntegrationTests
{
    /**
     * @throws ApiException
     * @throws BadResponseException
     * @throws ConfigException
     */
    public function testLogIn(): string
    {
        $userName = $this->env->req('API_LOGIN');
        $password = $this->env->req('API_PASSWORD');

        $res = $this->request('POST', '/v1/oauth/password', [
            'form_params' => [
                'grant_type' => 'password',
                'username' => $userName,
                'password' => $password,
            ],
        ]);

        self::assertEquals(200, $res->getStatusCode());

        $payload = $res->getJSON();
        self::assertArrayHasKey('access_token', $payload);
        self::assertIsString($payload['access_token']);

        return $payload['access_token'];
    }

    /**
     * @depends testLogIn
     * @throws ApiException
     * @throws BadResponseException
     */
    public function testPageNotFound(string $token): void
    {
        $id = rand(111111, 999999);
        $pageId = sprintf('testing:page:%u', $id);

        $res = $this->getPage($pageId, $token);

        self::assertEquals(404, $res->getStatusCode());
    }

    /**
     * @depends testLogIn
     * @throws ApiException
     * @throws BadResponseException
     */
    public function testEditPage(string $token): void
    {
        $id = rand(111111, 999999);
        $pageId = sprintf('testing:page:%u', $id);

        try {
            // Create page.
            $res = $this->putPage($pageId, $pageId, $token);
            self::assertEquals(202, $res->getStatusCode());

            // Update page, verify the edit.
            $res = $this->getPage($pageId, $token);
            self::assertEquals(200, $res->getStatusCode());
            self::assertEquals($pageId, $res->getJSON()['page']['source']);

            // Delete page.
            $res = $this->deletePage($pageId, $token);
            self::assertEquals(200, $res->getStatusCode(), $res->getString());
        } finally {
            // Just in case we failed, double check.
            $this->deletePage($pageId, $token);
        }
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     */
    private function getPage(string $pageId, string $token): ApiResponse
    {
        return $this->request('GET', sprintf('/v1/pages/%s', $pageId), [
            'headers' => [
                'authorization' => sprintf('bearer %s', $token),
            ],
        ]);
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     */
    private function putPage(string $pageId, string $contents, string $token): ApiResponse
    {
        return $this->request('PUT', sprintf('/v1/pages/%s', $pageId), [
            'headers' => [
                'authorization' => sprintf('bearer %s', $token),
            ],
            'json' => [
                'value' => $contents,
            ],
        ]);
    }

    /**
     * @throws ApiException
     * @throws BadResponseException
     */
    private function deletePage(string $pageId, string $token): ApiResponse
    {
        return $this->request('DELETE', sprintf('/v1/pages/%s', $pageId), [
            'headers' => [
                'authorization' => sprintf('bearer %s', $token),
            ],
            'json' => [
                'value' => '',
            ],
        ]);
    }
}
