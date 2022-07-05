<?php

namespace Sheetpost\Model\Database\Repositories;

use Doctrine\ORM\EntityRepository;
use Sheetpost\Model\Database\Entities\Post;
use Sheetpost\Model\Database\Entities\Sheet;

class PostRepository extends EntityRepository
{
    public function findById(string $id): ?Post
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function getWithSheetCount(): array
    {
        $sheetClass = Sheet::class;
        $query = $this->createQueryBuilder('p')
            ->select('p.id', 'u.username', 'p.date', 'p.message')
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s WHERE s.post = p.id) AS sheet_count")
            ->innerJoin('p.user', 'u')
            ->orderBy('p.date', 'DESC');
        return $query->getQuery()->getArrayResult();
    }

    public function getWithSheetCountAndSheeted(int $userId): array
    {
        $sheetClass = Sheet::class;
        $query = $this->createQueryBuilder('p')
            ->select('p.id', 'u.username', 'p.date', 'p.message')
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s0 WHERE s0.post = p.id) AS sheet_count")
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s1 WHERE s1.post = p.id AND s1.user = :user_id) AS sheeted")
            ->innerJoin('p.user', 'u')
            ->orderBy('p.date', 'DESC')
            ->setParameter('user_id', $userId);
        return $query->getQuery()->getArrayResult();
    }

    public function getUserPostsWithSheetCountAndSheeted(int $userId): array
    {
        $sheetClass = Sheet::class;
        $query = $this->createQueryBuilder('p')
            ->select('p.id', 'u.username', 'p.date', 'p.message')
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s0 WHERE s0.post = p.id) AS sheet_count")
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s1 WHERE s1.post = p.id AND s1.user = :user_id) AS sheeted")
            ->innerJoin('p.user', 'u')
            ->where('p.user = :user_id')
            ->orderBy('p.date', 'DESC')
            ->setParameter('user_id', $userId);
        return $query->getQuery()->getArrayResult();
    }

    public function getUserSheetsWithSheetCountAndSheeted(int $userId): array
    {
        $sheetClass = Sheet::class;
        $query = $this->createQueryBuilder('p')
            ->select('p.id', 'u.username', 'p.date', 'p.message')
            ->addSelect("(SELECT COUNT(1) FROM $sheetClass s0 WHERE s0.post = p.id) AS sheet_count")
            ->addSelect("1 AS sheeted")
            ->innerJoin('p.user', 'u')
            ->where('p.user = :user_id')
            ->andWhere("(SELECT COUNT(1) FROM $sheetClass s1 WHERE s1.post = p.id AND s1.user = :user_id) = 1")
            ->orderBy('p.date', 'DESC')
            ->setParameter('user_id', $userId);
        return $query->getQuery()->getArrayResult();
    }
}