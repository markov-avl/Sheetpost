<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\API\Parameters\StringParameter;
use Sheetpost\Model\Database\Entities\Post;

class EditPost extends APIMethodAbstract
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ['username', 'password', 'post_id', 'message']);
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

        $post = $this->entityManager->getRepository(Post::class)->findById($getParameters['post_id']);
        if ($post === null) {
            return ['success' => false, 'error' => 'post not found'];
        }

        $user = $post->getUser();
        if ($user->getUsername() === $getParameters['username'] && $user->authenticate($getParameters['password'])) {
            $post->setMessage($getParameters['message']);
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'this post was not created by this user'];
    }
}