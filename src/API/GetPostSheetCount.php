<?php

namespace Sheetpost\API;

use Exception;
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
    protected function getQueryResponse(array $getParameters): array
    {
        $postId = $getParameters['post_id'];
        if (!ctype_digit($postId)) {
            return ['success' => false, 'error' => 'post id is not an integer'];
        }
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