<?php

namespace Sheetpost\Controller;

use Sheetpost\Model\Database\Repositories\ExtendedPostRepository;
use Sheetpost\View\Pages\HomeView;

class HomePageController implements ControllerInterface
{
    private ExtendedPostRepository $extendedPostRepository;
    private string $username;

    public function __construct(ExtendedPostRepository $extendedPostRepository, string $username)
    {
        $this->extendedPostRepository = $extendedPostRepository;
        $this->username = $username;
    }

    public function show(): void
    {
        $posts = $this->extendedPostRepository->allAuthorized($this->username);
        $homePageView = new HomeView($this->username, $posts);
        echo $homePageView->render();
    }
}