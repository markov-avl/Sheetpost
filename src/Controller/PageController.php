<?php

namespace Sheetpost\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sheetpost\Model\Database\Entities\Post;
use Sheetpost\Model\Database\Entities\User;
use Sheetpost\View\Pages\HomeView;
use Sheetpost\View\Pages\MainView;
use Sheetpost\View\Pages\MyPostsView;
use Sheetpost\View\Pages\MySheetsView;
use Sheetpost\View\Pages\PageNotFoundView;
use Sheetpost\View\ViewInterface;
use Twig\Environment;

class PageController implements ControllerInterface
{
    private Environment $twig;
    private EntityManagerInterface $entityManager;
    private string $requestedPath;
    private string $rootPath;
    private ?User $user;
    private ?ViewInterface $view;

    public function __construct(Environment            $twig,
                                EntityManagerInterface $entityManager,
                                string                 $requestedPath,
                                string                 $rootPath,
                                ?User                  $user)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->requestedPath = $requestedPath;
        $this->rootPath = $rootPath;
        $this->user = $user;
        $this->view = null;
    }

    public function show(): void
    {
        switch ($this->requestedPath) {
            case $this->rootPath:
                $posts = $this->entityManager
                    ->getRepository(Post::class)
                    ->getWithSheetCount();
                $this->view = new MainView($this->twig, $this->rootPath, $posts);
                break;
            case "$this->rootPath/home":
                $posts = $this->entityManager
                    ->getRepository(Post::class)
                    ->getWithSheetCountAndSheeted($this->user->getId());
                $this->view = new HomeView($this->twig, $this->rootPath, $this->user->getUsername(), $posts);
                break;
            case "$this->rootPath/myposts":
                $posts = $this->entityManager
                    ->getRepository(Post::class)
                    ->getUserPostsWithSheetCountAndSheeted($this->user->getId());
                $this->view = new MyPostsView($this->twig, $this->rootPath, $this->user->getUsername(), $posts);
                break;
            case "$this->rootPath/mysheets":
                $posts = $this->entityManager
                    ->getRepository(Post::class)
                    ->getUserSheetsWithSheetCountAndSheeted($this->user->getId());
                $this->view = new MySheetsView($this->twig, $this->rootPath, $this->user->getUsername(), $posts);
                break;
            default:
                $this->view = new PageNotFoundView($this->twig, $this->rootPath);
        }
        echo $this->view->render();
    }
}