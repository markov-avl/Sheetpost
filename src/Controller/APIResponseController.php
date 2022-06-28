<?php

namespace Sheetpost\Controller;

use Sheetpost\View\APIResponseView;

class APIResponseController implements ControllerInterface
{
    private string $requestedMethod;

    public function __construct(string $requestedPath)
    {
        $path = explode('/', explode('?', $requestedPath)[0]);
        $this->requestedMethod = end($path);
    }

    public function show()
    {
        $methodClass = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->requestedMethod)));
        $methodClassPath = join('\\', ['Sheetpost', 'Model', 'API', $methodClass]);

        if ($methodClass !== 'APIMethodAbstract' && class_exists($methodClassPath)) {
            $method = new $methodClassPath();
            $response = $method->getResponse($_GET);
        } else {
            $response = ['success' => false, 'error' => 'unknown method'];
        }

        $responseView = new APIResponseView($response);
        echo $responseView->render();
    }
}