<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class HomeView extends PageViewAbstract
{
    public function __construct(Environment $twig, string $rootPath, string $user, array $posts)
    {
        parent::__construct($twig, $rootPath, $user, $posts, 'home.html.twig');
    }
}