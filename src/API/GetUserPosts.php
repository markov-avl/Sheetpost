<?php

namespace Sheetpost\API;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use PDO;

class GetUserPosts extends Response
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['username'];
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(["success" => "bool", "data" => "array"])]
    protected function getQueryResponse(array $getParameters): array
    {
        $username = $getParameters['username'];
        return [
            "success" => true,
            "data" => $this->db->query("
                SELECT id, date, message,
                       (SELECT COUNT(*) FROM sheets
                                        WHERE posts.id = sheets.post_id) as sheet_count,
                       (SELECT COUNT(*) FROM sheets
                                        WHERE posts.id = sheets.post_id AND sheets.username='$username') as sheeted
                FROM posts
                WHERE username='$username'
                ORDER BY date DESC"
            )->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
}