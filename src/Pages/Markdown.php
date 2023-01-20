<?php

declare(strict_types=1);

namespace App\Pages;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class Markdown extends MarkdownConverter
{
    public function __construct()
    {
        $env = new Environment();
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new TableExtension());

        parent::__construct($env);
    }
}
