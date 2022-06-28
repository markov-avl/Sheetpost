<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class MySheetsView extends PageViewAbstract
{
    public function __construct(Environment $twig, string $user, array $posts)
    {
        parent::__construct($twig, $user, $posts, 'mysheets.html.twig');
    }
}