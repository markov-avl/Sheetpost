<?php

namespace Sheetpost\Controller;

use Sheetpost\Model\Database\Repositories\ExtendedPostRepository;
use Sheetpost\View\Pages\MainView;

class MainPageController implements ControllerInterface
{
    private ExtendedPostRepository $extendedPostRepository;

    public function __construct(ExtendedPostRepository $extendedPostRepository)
    {
        $this->extendedPostRepository = $extendedPostRepository;
    }

    public function show(): void
    {
        $posts = $this->extendedPostRepository->all();
        $mainPageView = new MainView($posts);
        echo $mainPageView->render();
    }
}