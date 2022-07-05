<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Entities\User;


class CreateNewUser extends APIMethodAbstract
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
        foreach ([
                     new StringParameter($getParameters['username'], 'username', 32),
                     new StringParameter($getParameters['password'], 'password', 64)
                 ] as $parameter) {
            $parameterError = $parameter->check();
            if ($parameterError) {
                return ['success' => false, 'error' => $parameterError];
            }
        }

        $user = $this->entityManager->getRepository(User::class)->findByUsername($getParameters['username']);

        if ($user === null) {
            $newUser = new User();
            $newUser->setUsername($getParameters['username']);
            $newUser->setPassword($getParameters['password']);
            $this->entityManager->persist($newUser);
            $this->entityManager->flush();
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'this username is already taken'];
    }
}