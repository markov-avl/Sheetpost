<?php

namespace Sheetpost\View\Pages;

class MyPostsView extends PageView
{
    public function __construct(string $username, array $posts)
    {
        parent::__construct($username, $posts, 'myposts.html.twig');
    }
}