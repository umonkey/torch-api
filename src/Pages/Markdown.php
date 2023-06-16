<?php

declare(strict_types=1);

namespace App\Pages;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\AlreadyInitializedException;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use RuntimeException;

class Markdown
{
    private readonly MarkdownConverter $md;

    /**
     * @throws AlreadyInitializedException
     */
    public function __construct()
    {
        $env = new Environment([
            'heading_permalink' => [
                'symbol' => '#',
                'min_heading_level' => 2,
            ],

            'table_of_contents' => [
                'position' => 'placeholder',
                'min_heading_level' => 2,
                'placeholder' => '[TOC]',
            ],
        ]);

        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new HeadingPermalinkExtension());
        $env->addExtension(new SmartPunctExtension());
        $env->addExtension(new TableExtension());
        $env->addExtension(new TableOfContentsExtension());

        $this->md = new MarkdownConverter($env);
    }

    /**
     * @throws CommonMarkException
     * @throws RuntimeException
     */
    public function convert(string $source): string
    {
        return (string)$this->md->convert($source);
    }
}
