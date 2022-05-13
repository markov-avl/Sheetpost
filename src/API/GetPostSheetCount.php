<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\IntegerParameter;

class GetPostSheetCount extends APIResponse
{
    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        parent::__construct($host, $dbname, $user, $password);
        $this->parameters = ['post_id'];
        $this->query = 'SELECT * FROM sheets WHERE sheets.post_id = :post_id';
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $postId = new IntegerParameter($getParameters['post_id'], 'post id', 0, 4294967295);
        $postIdError = $postId->check();
        if ($postIdError) {
            return ['success' => false, 'error' => $postIdError];
        }

        return [
            'success' => true,
            'sheet_count' => $this->db->query($this->query, [':post_id' => $getParameters['post_id']])->rowCount()
        ];
    }
}