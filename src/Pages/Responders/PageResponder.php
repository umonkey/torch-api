<?php

declare(strict_types=1);

namespace App\Pages\Responders;

use App\Core\AbstractResponder;
use App\Database\Exceptions\DatabaseException;
use App\Pages\Objects\PageObject;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PageResponder extends AbstractResponder
{
    /**
     * @throws DatabaseException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws RuntimeException
     */
    public function respond(ResponseInterface $response, PageObject $page): ResponseInterface
    {
        $data = [
            'page' => $page->serialize(),
        ];

        return $this->sendJSON($response, $data);
    }
}
