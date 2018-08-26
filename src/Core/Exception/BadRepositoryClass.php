<?php

namespace Core\Exception;

class BadRepositoryClass extends \Exception
{
    protected $message = 'The given repository in the Entity have to implements the EntityRepositoryInterface';
}