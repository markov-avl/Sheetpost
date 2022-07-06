<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class PageNotFoundView extends PageViewAbstract
{
    public function __construct(Environment $twig, string $rootPath)
    {
        parent::__construct($twig, $rootPath, null, null, 'pagenotfound.html.twig');
    }
}