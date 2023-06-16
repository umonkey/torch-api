<?php

declare(strict_types=1);

namespace App\Pages\Responders;

use App\Core\AbstractResponder;
use App\Core\JsonResponse;
use App\Database\Exceptions\DatabaseException;
use App\Exceptions\BadResponseException;
use App\Pages\Objects\PageObject;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class PageResponder extends AbstractResponder
{
    /**
     * @throws BadResponseException
     * @throws DatabaseException
     * @throws InvalidArgumentException
     */
    public function respond(PageObject $page): ResponseInterface
    {
        $data = [
            'page' => $page->serialize(),
        ];

        return new JsonResponse($data);
    }
}
