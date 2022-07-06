<?php

namespace Sheetpost\Model\API\Methods;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Entities\Post;
use Sheetpost\Model\Database\Entities\User;

class CreateNewPost extends APIMethodAbstract
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ['username', 'password', 'message']);
    }

    /**
     * @throws Exception
     */
    protected function getQueryResponse(array $getParameters): array
    {
        $getParameters['message'] = trim($getParameters['message']);
        $message = new StringParameter($getParameters['message'], 'message', 4096, false);
        $messageError = $message->check();
        if ($messageError) {
            return ['success' => false, 'error' => $messageError];
        }

        $user = $this->entityManager->getRepository(User::class)->findByUsername($getParameters['username']);

        if (isset($user) && $user->authenticate($getParameters['password'])) {
            $newPost = new Post();
            $newPost->setUser($user);
            $newPost->setDate(new DateTime());
            $newPost->setMessage($getParameters['message']);
            $this->entityManager->persist($newPost);
            $this->entityManager->flush();
            return ['success' => true, 'post_id' => $newPost->getId()];
        }
        return ['success' => false, 'error' => 'invalid username or password'];
    }
}