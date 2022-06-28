<?php

namespace Sheetpost\Model\API\Methods;

use Exception;
use Sheetpost\Model\API\Parameters\IntegerParameter;
use Sheetpost\Model\Database\Repositories\ExtendedPostRepository;

class GetPostSheetCount extends APIMethodAbstract
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