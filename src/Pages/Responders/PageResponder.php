<?php

declare(strict_types=1);

namespace App\Pages\Responders;

use App\Core\AbstractResponder;
use App\Database\Entities\PageEntity;
use App\Database\Exceptions\DatabaseException;
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
    public function respond(ResponseInterface $response, PageEntity $page): ResponseInterface
    {
        $data = [
            'page' => [
                'id' => $page->getId(),
                'created' => $page->getCreated(),
                'updated' => $page->getUpdated(),
                'text' => $page->getText(),
            ],
        ];

        return $this->sendJSON($response, $data);
    }
}
