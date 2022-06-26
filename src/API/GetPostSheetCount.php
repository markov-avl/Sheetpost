<?php

namespace Sheetpost\API;

use Exception;
use Sheetpost\Database\Records\ExtendedPost;
use Sheetpost\Database\Repositories\ExtendedPostRepository;
use Sheetpost\Models\APIResponse;
use Sheetpost\Models\IntegerParameter;

class GetPostSheetCount extends APIResponse
{
    public function __construct()
    {
        parent::__construct(['post_id']);
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
            'sheet_count' => ExtendedPostRepository::getByFields(['id' => $getParameters['post_id']])[0]->sheetCount ?? -1
        ];
    }
}