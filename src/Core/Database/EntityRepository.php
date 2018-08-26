<?php

namespace Core\Database;

use Core\AbstractEntity;

class EntityRepository extends AbstractEntityRepository
{
    public function findBy(array $parameters)
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($parameters as $property => $value) {
            if (!isset($this->columnsDefinition[$property])) {
                throw new \LogicException("The key \"$property\" is not in the columns definition of the Entity $this->entityClass");
            }

            $qb->andWhere('e.'.$this->columnsDefinition[$property]['name'].' = :'.$property);
        }

        $sql = $qb->getSQL();
        $statement = $this->connection->prepare($sql);

        foreach ($parameters as $property => $value) {
            $statement->bindValue($property, $value, $this->columnsDefinition[$property]['type']);
        }

        $statement->execute();

        return $this->formatMultipleResult($statement);
    }

    public function findOneBy(array $parameters)
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->setMaxResults(1);

        foreach ($parameters as $property => $value) {
            if (!isset($this->columnsDefinition[$property])) {
                throw new \LogicException("The key \"$property\" is not in the columns definition of the Entity $this->entityClass");
            }

            $qb->andWhere('e.'.$this->columnsDefinition[$property]['name'].' = :'.$property);
        }

        $sql = $qb->getSQL();
        $statement = $this->connection->prepare($sql);

        foreach ($parameters as $property => $value) {
            $statement->bindValue($property, $value, $this->columnsDefinition[$property]['type']);
        }

        $statement->execute();

        return $this->formatSingleResult($statement);
    }

    /**
     * Delete an Entity
     *
     * @param AbstractEntity $obj
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function delete(AbstractEntity $obj)
    {
        $query = $this
            ->connection
            ->createQueryBuilder()
            ->delete()
            ->from($this->entityClass::TABLE_NAME)
            ->where('id = :id')
            ->getSQL();

        $statement = $this->connection->prepare($query);
        $statement->bindValue('id', $obj->getId(), Type::INTEGER);

        return $statement->execute();
    }
}