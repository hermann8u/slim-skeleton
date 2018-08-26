<?php

namespace Core\Database;

use Core\AbstractEntity;
use Core\Exception\ClassNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractEntityRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var array
     */
    protected $columnsDefinition;

    /**
     * @var string
     */
    private $entityAlias;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * EntityRepository constructor.
     *
     * @param Connection $connection
     * @param PropertyAccessor $propertyAccessor
     * @param string $class
     *
     * @throws ClassNotFoundException
     */
    public function __construct(Connection $connection, PropertyAccessor $propertyAccessor, string $class)
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException();
        }

        $this->connection = $connection;
        $this->entityClass = $class;
        $this->propertyAccessor = $propertyAccessor;
        $this->columnsDefinition = $class::columnsDefinition();
    }

    /**
     * @param string $alias
     *
     * @return QueryBuilder
     *
     * @throws ClassNotFoundException
     */
    public function createQueryBuilder(string $alias)
    {
        $this->entityAlias = $alias;

        return $this
            ->connection
            ->createQueryBuilder()
            ->select($this->selectTable($this->entityClass, $alias))
            ->from(constant($this->entityClass.'::TABLE_NAME'), $alias);
    }

    /**
     * Generate a select statement based on the AbstractEntity columnsDefinition
     *
     * @param string $class The full namespace of the AbstractEntity
     * @param string $alias An alias for the table name
     *
     * @return string
     *
     * @throws ClassNotFoundException
     */
    protected function selectTable(string $class, string $alias)
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException();
        }
        
        $sql = '';
        foreach ($class::columnsDefinition() as $property => $column) {
            $sql .= $alias.'.'.$column['name'].' as '.$alias.$column['name'].',';
        }

        return substr($sql, 0, strlen($sql) - 1);
    }

    /**
     * Convert the data from database to the right type and load them in the given AbstractEntity instance.
     *
     * @param AbstractEntity $entity The entity to fill
     * @param array $data An associative array where the key is the column name and the value is the value to set.
     *
     * @return AbstractEntity
     */
    protected function loadData(AbstractEntity &$entity, array $data)
    {

        $cleanData = [];
        foreach ($this->columnsDefinition as $property => $column) {
            if ($value = $data[$column['name']]) {
                switch ($column['type']) {
                    case Type::INTEGER :
                        $value = (int) $value;
                        break;

                    case Type::STRING :
                        $value = (string) $value;
                        break;

                    case Type::DATETIME :
                        $value = new \DateTime($value);
                        break;
                }
            }

            $cleanData[$property] = $value;
        }

        foreach ($cleanData as $key => $value) {
            $this->propertyAccessor->setValue($entity, $key, $value);
        }

        return $entity;
    }

    protected function formatSingleResult(Statement $statement)
    {
        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            return null;
        }

        $entity = new $this->entityClass;

        foreach ($data as $key => $value) {
            $data[substr($key, strlen($this->entityAlias))] = $value;
            unset($data[$key]);
        }

        return $this->loadData($entity, $data);
    }

    protected function formatMultipleResult(Statement $statement)
    {
        $data = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if (!$data) {
            return null;
        }

        $entities = [];
        foreach ($data as $entityData) {
            $entity = new $this->entityClass;

            foreach ($entityData as $key => $value) {
                $entityData[substr($key, strlen($this->entityAlias))] = $value;
                unset($entityData[$key]);
            }

            $entities[] = $this->loadData($entity, $entityData);
        }

        return $entities;
    }
}