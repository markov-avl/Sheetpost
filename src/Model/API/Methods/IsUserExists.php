<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\Database\Entities\User;

class IsUserExists extends APIMethodAbstract
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ['username', 'password']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        return [
            "success" => true,
            "exists" => $this->entityManager->getRepository(User::class)->findByUsernameAndPassword(
                    $getParameters['username'],
                    $getParameters['password']
                ) !== null
        ];
    }
}