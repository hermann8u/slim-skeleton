<?php

namespace Core;

abstract class AbstractEntity
{
    /**
     * The database table name. Leave blank to be auto-generated from class name
     */
    const TABLE_NAME = '';

    /**
     * The repository class. Override it to define a custom repository.
     */
    const REPOSITORY = '';

    /**
     * Define the relation between properties and database table columns.
     * The key is the property name and the value is the column name.
     *
     * @return array
     */
    public abstract static function columnsDefinition();
}