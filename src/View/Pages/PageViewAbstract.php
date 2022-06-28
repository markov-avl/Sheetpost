<?php

namespace Sheetpost\View\Pages;

use Sheetpost\View\ViewInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class PageViewAbstract implements ViewInterface
{
    private Environment $twig;
    protected ?string $user;
    protected array $posts;
    protected string $template;

    public function __construct(Environment $twig, ?string $user, array $posts, string $template)
    {
        $this->twig = $twig;
        $this->user = $user;
        $this->posts = $posts;
        $this->template = $template;
    }

    public function render(): string|LoaderError|RuntimeError|SyntaxError
    {
        try {
            return $this->twig->render($this->template, ['user' => $this->user, 'posts' => $this->posts]);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return $e;
        }
    }
}