<?php

/**
 * Returns a list of all pages.
 */

declare(strict_types=1);

namespace App\Pages;

use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\PageRepository;
use Psr\Log\LoggerInterface;

class ListPages
{
    public function __construct(private readonly LoggerInterface $logger, private readonly PageRepository $pages)
    {
    }

    /**
     * @return mixed[]
     * @throws DatabaseException
     */
    public function getPages(UserEntity $user): array
    {
        $this->logger->debug(sprintf('User "%s" requests page list.', $user->getId()));

        $pages = [];

        foreach ($this->pages->iter() as $page) {
            $pages[] = [
                'id' => $page->getId(),
            ];
        }

        return $pages;
    }
}
