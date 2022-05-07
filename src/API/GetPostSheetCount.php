<?php

namespace Sheetpost\API;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use PDO;

class GetPostSheetCount extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['post_id'];
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(["success" => "bool", "sheet_count" => "int"])]
    protected function getQueryResponse(array $getParameters): array
    {
        $postId = $getParameters['post_id'];
        return [
            "success" => true,
            "sheet_count" => $this->db->query("
                SELECT COUNT(*) as count
                FROM sheets
                WHERE sheets.post_id=$postId"
            )->fetch(PDO::FETCH_ASSOC)['count']
        ];
    }
}