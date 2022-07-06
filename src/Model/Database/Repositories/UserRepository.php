<?php

namespace Sheetpost\Model\Database\Repositories;

use Doctrine\ORM\EntityRepository;
use Sheetpost\Model\Database\Entities\User;

class UserRepository extends EntityRepository
{
    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function findByUsernameAndPassword(string $username, string $password): ?User
    {
        return $this->findOneBy(['username' => $username, 'password' => $password]);
    }
}