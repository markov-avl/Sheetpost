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
        $user = $this->entityManager->getRepository(User::class)->findByUsername($getParameters['username']);
        return [
            'success' => true,
            'exists' => isset($user) && $user->authenticate($getParameters['password'])
        ];
    }
}