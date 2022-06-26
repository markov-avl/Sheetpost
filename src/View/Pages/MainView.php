<?php

namespace Sheetpost\View\Pages;

class MainView extends PageView
{
    public function __construct(array $posts)
    {
        parent::__construct(null, $posts, 'sheetpost-v4.html.twig');
    }
}