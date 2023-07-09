<?php

declare(strict_types=1);

namespace App\Core\Testing;

use App\Core\Config\Environment;
use App\Core\Testing\Exceptions\ApiException;
use App\Exceptions\BadResponseException;
use App\Exceptions\ConfigException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Throwable;

class ApiClient
{
    private readonly Client $http;

    /**
     * @throws ConfigException
     */
    public function __construct(Environment $env)
    {
        $baseURI = $env->req('API_ROOT');

        $this->http = new Client([
            'base_uri' => $baseURI,
        ]);
    }

    /**
     * @param mixed[] $options
     * @throws ApiException
     * @throws BadResponseException
     */
    public function request(string $method, string $path, array $options): ApiResponse
    {
        try {
            $res = $this->http->request($method, $path, $options);
        } catch (ClientException | ServerException $e) {
            $res = $e->getResponse();
        } catch (Throwable $e) {
            throw new ApiException($e->getMessage());
        }

        return ApiResponse::fromResponse($res);
    }
}
