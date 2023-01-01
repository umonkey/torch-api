<?php

/**
 * Error handling middleware.
 *
 * If bugsnag.bugsnagKey config option is set, sends bug reports to bugsnag.com.
 * If log_errors ini option is set, sends reports to PHP error log.
 **/

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\AbstractException;
use Bugsnag\Client as BugsnagClient;
use ErrorException;
use InvalidArgumentException;
use Invoker\Exception\NotCallableException;
use JsonException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ErrorHandlerMiddleware
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     **/
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
                if ((error_reporting() & $errno) === 0) {
                    return false;
                }

                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });

            return $handler->handle($request);
        } catch (AbstractException $exception) {
            if ($exception->getStatusCode() >= 500) {
                $this->reportError($exception);
            }

            return $this->sendJSON($exception->getResponse(), $exception->getStatusCode());
        } catch (HttpMethodNotAllowedException $httpMethodNotAllowedException) {
            return $this->sendError($httpMethodNotAllowedException, 405);
        } catch (HttpNotFoundException $httpNotFoundException) {
            return $this->sendError($httpNotFoundException, 404);
        } catch (NotCallableException $notCallableException) {
            return $this->sendError($notCallableException, 503);
        } catch (Throwable $throwable) {
            $this->reportError($throwable);
            return $this->sendError($throwable, 500);
        } finally {
            restore_error_handler();
        }
    }

    private function reportError(Throwable $e): void
    {
        $this->logger->error('Unhandled exception.', [
            'class' => $e::class,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }

    /**
     * @param array<mixed> $data
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     **/
    private function sendJSON(array $data, int $status): Response
    {
        $response = $this->responseFactory->createResponse()
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($data, \JSON_THROW_ON_ERROR));

        return $response->withHeader('Cache-Control', 'no-store');
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     **/
    private function sendError(Throwable $e, int $status): Response
    {
        $parts = explode('\\', $e::class);
        $code = array_pop($parts);

        $response = [
            'error' => [
                'code' => $code,
                'message' => $e->getMessage(),
            ],
        ];

        if (APP_ENV !== 'production') {
            $response['error']['debug'] = [
                'class' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }

        return $this->sendJSON($response, $status);
    }
}
