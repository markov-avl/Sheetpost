<?php

namespace Sheetpost\Controller;

use Sheetpost\Model\Database\Repositories\ExtendedPostRepository;
use Sheetpost\View\Pages\MyPostsView;

class MyPostsPageController implements ControllerInterface
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
        $posts = $this->extendedPostRepository->allUserPosts($this->username);
        $myPostsPageView = new MyPostsView($this->username, $posts);
        echo $myPostsPageView->render();
    }
}