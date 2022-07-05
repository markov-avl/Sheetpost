<?php

namespace Sheetpost\Model\API\Methods;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sheetpost\Model\Database\Entities\Post;

class DeletePost extends APIMethodAbstract
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
        $post = $this->entityManager->getRepository(Post::class)->findById($getParameters['post_id']);
        if ($post === null) {
            return ['success' => false, 'error' => 'post not found'];
        }
        $user = $post->getUser();
        if ($user->getUsername() !== $getParameters['username'] || $user->getPassword() !== $getParameters['password']) {
            return ['success' => false, 'error' => 'this post was not created by this user'];
        }
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return ['success' => true];
    }
}