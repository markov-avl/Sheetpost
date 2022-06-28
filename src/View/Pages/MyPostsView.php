<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;

class MyPostsView extends PageViewAbstract
{
    public function __construct(Environment $twig, string $user, array $posts)
    {
        parent::__construct($twig, $user, $posts, 'myposts.html.twig');
    }
}