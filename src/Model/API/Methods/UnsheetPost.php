<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\API\Parameters\IntegerParameter;
use Sheetpost\Model\Database\Entities\Sheet;
use Sheetpost\Model\Database\Entities\User;

class UnsheetPost extends APIMethodAbstract
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ['username', 'password', 'post_id']);
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

        $user = $this->entityManager->getRepository(User::class)->findByUsernameAndPassword(
            $getParameters['username'],
            $getParameters['password']
        );
        if ($user === null) {
            return ['success' => false, 'error' => 'invalid username or password'];
        }

        $sheet = $this->entityManager->getRepository(Sheet::class)->findByUserIdAndPostId(
            $user->getId(),
            $getParameters['post_id']
        );
        if ($sheet !== null) {
            $this->entityManager->remove($sheet);
            $this->entityManager->flush();
        }
        return ['success' => true];
    }
}