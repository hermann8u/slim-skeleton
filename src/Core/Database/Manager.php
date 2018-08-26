<?php

namespace Core\Database;

use Core\Exception\ClassNotFoundException;
use Doctrine\DBAL\Connection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Manager
{
    const DEFAULT_ENTITY_REPOSITORY = EntityRepository::class;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var AbstractEntityRepository[]
     */
    private $repositories;

    /**
     * Leave the second parameter "null" to use the default Entity Repository define in this class or (children classes)
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->connection->getWrappedConnection()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->repositories = [];
    }

    /**
     * @param string $class
     *
     * @return EntityRepository
     *
     * @throws ClassNotFoundException
     */
    public function getRepository(string $class)
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException();
        }
        
        if (!isset($this->repositories[$class])) {
            $repositoryClass = constant($class . '::REPOSITORY') ?: self::DEFAULT_ENTITY_REPOSITORY;
            $this->repositories[$class] = new $repositoryClass($this->connection, $this->propertyAccessor, $class);
        }

        return $this->repositories[$class];
    }
}