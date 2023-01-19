<?php

declare(strict_types=1);

namespace App\Pages;

use App\Database\Entities\PageEntity;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\RecordNotFoundException;
use App\Database\Repositories\PageRepository;
use App\Exceptions\PageNotFoundException;
use App\Pages\Objects\PageObject;
use League\CommonMark\CommonMarkConverter;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Pages
{
    public function __construct(
        private readonly CommonMarkConverter $md,
        private readonly LoggerInterface $logger,
        private readonly PageRepository $pages,
    ) {
    }

    /**
     * @throws DatabaseException
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function get(string $key, UserEntity $user): PageObject
    {
        try {
            $page = $this->pages->get($key);
            $html = $this->md->convert($page->getText());

            $this->logger->debug('User reads page.', [
                'page' => $key,
                'user' => $user->getId(),
            ]);

            return (new PageObject())
                ->withEntity($page)
                ->withHTML((string)$html);
        } catch (RecordNotFoundException) {
            throw new PageNotFoundException();
        }
    }

    /**
     * @throws DatabaseException
     */
    public function put(string $id, string $text, UserEntity $user): void
    {
        try {
            $page = $this->pages->get($id);
            $page->setText($text);
            $page->setUpdated(time());

            $this->pages->update($page);

            $this->logger->info('Page updated.', [
                'id' => $id,
                'user' => $user->getId(),
            ]);
        } catch (RecordNotFoundException) {
            $page = new PageEntity();
            $page->setId($id);
            $page->setText($text);
            $page->setUpdated(time());

            $this->pages->add($page);

            $this->logger->info('Page created.', [
                'id' => $id,
                'user' => $user->getId(),
            ]);
        }
    }
}
