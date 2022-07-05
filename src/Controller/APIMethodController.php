<?php

namespace Sheetpost\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sheetpost\View\APIResponseView;

class APIMethodController implements ControllerInterface
{
    private EntityManagerInterface $entityManager;
    private string $requestedMethod;

    public function __construct(EntityManagerInterface $entityManager, string $requestedPath)
    {
        $this->entityManager = $entityManager;
        $path = explode('/', explode('?', $requestedPath)[0]);
        $this->requestedMethod = end($path);
    }

    public function show()
    {
        $methodClass = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->requestedMethod)));
        $methodClassPath = join('\\', ['Sheetpost', 'Model', 'API', 'Methods', $methodClass]);

        if ($methodClass !== 'APIMethodAbstract' && class_exists($methodClassPath)) {
            $method = new $methodClassPath($this->entityManager);
            $response = $method->getResponse($_GET);
        } else {
            $response = ['success' => false, 'error' => 'unknown method'];
        }

        $responseView = new APIResponseView($response);
        echo $responseView->render();
    }
}