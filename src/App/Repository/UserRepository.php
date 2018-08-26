<?php

namespace App\Repository;

use App\Entity\User;
use Core\Database\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByEmail($email = null)
    {
        return $this->findOneBy([
            'name' => 'Florian Hermann'
        ]);

        $sql = $this
            ->createQueryBuilder('u')
            //->leftJoin('u', User::TABLE_NAME, 'u2', 'u.id = u2.id')
            //->where('u.email = :email')
            //->addSelect($this->selectTable(User::class, 'u2'))
            ->getSQL();

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('email', $email, 'string');
        $statement->execute();

        dump($this->formatMultipleResult($statement));

        /*return $this->findOneBy([
            'email' => $email
        ]);*/
        /*$query = $this
            ->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($this->classData['table'])
            ->where('email = :email')
            ->getSQL()
        ;

        $statement = $this->connection->prepare($query);
        $statement->bindValue('email', $email, Type::STRING);
        $statement->execute();

        return $this->formatSingleResult($statement->fetch());*/
    }
}