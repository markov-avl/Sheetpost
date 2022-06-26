<?php

namespace Sheetpost\View\Pages;

class HomeView extends PageView
{
    public function __construct(string $username, array $posts)
    {
        parent::__construct($username, $posts, 'home.html.twig');
    }
}