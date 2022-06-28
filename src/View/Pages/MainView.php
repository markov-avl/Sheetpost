<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class MainView extends PageViewAbstract
{
    public function __construct(Environment $twig, array $posts)
    {
        parent::__construct($twig, null, $posts, 'sheetpost-v4.html.twig');
    }
}