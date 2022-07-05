<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class MainView extends PageViewAbstract
{
    public function __construct(Environment $twig, string $rootPath, array $posts)
    {
        parent::__construct($twig, $rootPath, null, $posts, 'main.html.twig');
    }
}