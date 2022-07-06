<?php

namespace Sheetpost\Model\Database\Repositories;

use Doctrine\ORM\EntityRepository;
use Sheetpost\Model\Database\Entities\Sheet;

class SheetRepository extends EntityRepository
{
    public function getCountByPostId(int $postId): int
    {
        $query = $this->createQueryBuilder('s')
            ->select('COUNT(1) AS count')
            ->where('s.post = :post_id')
            ->setParameter('post_id', $postId);
        $queryResult = $query->getQuery()->getResult();
        return $queryResult[0]['count'];
    }

    public function findByUserIdAndPostId(int $userId, int $postId): ?Sheet
    {
        return $this->findOneBy(['user' => $userId, 'post' => $postId]);
    }
}