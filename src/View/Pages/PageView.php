<?php

namespace Sheetpost\View\Pages;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class PageView
{
    protected ?string $username;
    protected array $posts;
    protected string $filename;

    public function __construct(?string $username, array $posts, string $filename)
    {
        $this->username = $username;
        $this->posts = $posts;
        $this->filename = $filename;
    }

    public function render(): SyntaxError|RuntimeError|LoaderError|string
    {
        $loader = new FilesystemLoader( dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'templates');
        $environment = new Environment($loader);
        try {
            return $environment->render($this->filename,
                [
                    'user' => $this->username,
                    'posts' => $this->posts
                ]
            );
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return $e;
        }
    }
}