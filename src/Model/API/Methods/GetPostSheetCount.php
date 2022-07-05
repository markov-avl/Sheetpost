<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\API\Parameters\IntegerParameter;
use Sheetpost\Model\Database\Entities\Sheet;

class GetPostSheetCount extends APIMethodAbstract
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ['post_id']);
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
            'sheet_count' => $this->entityManager->getRepository(Sheet::class)->getCountByPostId($getParameters['post_id'])
        ];
    }
}