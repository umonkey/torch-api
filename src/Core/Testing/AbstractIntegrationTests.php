<?php

declare(strict_types=1);

namespace App\Core\Testing;

use App\Core\AbstractTestCase;
use App\Core\Testing\Exceptions\ApiException;
use App\Exceptions\BadResponseException;

abstract class AbstractIntegrationTests extends AbstractTestCase
{
    private readonly ApiClient $api;

    public function testEnvironment(): void
    {
        $value = $this->env->get('APP_ENV');
        self::assertNotNull($value, 'APP_ENV not set');
        self::assertEquals('integration_tests', $value, 'wrong APP_ENV value');

        $root = $this->env->get('API_ROOT');
        self::assertNotNull($root, 'API_ROOT not set');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = $this->container->get(ApiClient::class);
    }

    /**
     * @param mixed[] $options
     * @throws ApiException
     * @throws BadResponseException
     */
    protected function request(string $method, string $path, array $options = []): ApiResponse
    {
        return $this->api->request($method, $path, $options);
    }
}
