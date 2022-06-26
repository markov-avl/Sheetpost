<?php

namespace Sheetpost\View;

class APIResponseView
{
    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function render(): string
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return json_encode($this->response);
    }

}