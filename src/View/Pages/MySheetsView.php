<?php

namespace Sheetpost\View\Pages;

class MySheetsView extends PageView
{
    public function __construct(string $username, array $posts)
    {
        parent::__construct($username, $posts, 'mysheets.html.twig');
    }
}