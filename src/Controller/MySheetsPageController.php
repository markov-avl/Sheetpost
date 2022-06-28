<?php

namespace Sheetpost\Controller;

use Sheetpost\Model\Database\Repositories\ExtendedPostRepository;
use Sheetpost\View\Pages\MySheetsView;

class MySheetsPageController implements ControllerInterface
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
        $posts = $this->extendedPostRepository->allUserSheets($this->username);
        $mySheetsPageView = new MySheetsView($this->username, $posts);
        echo $mySheetsPageView->render();
    }
}