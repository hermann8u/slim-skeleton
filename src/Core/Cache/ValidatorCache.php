<?php

namespace Core\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ValidatorCache implements CacheInterface
{
    protected $rootDir;
    protected $env;
    protected $fileSystemCache;

    public function __construct(string $rootDir, string $env)
    {
        $this->rootDir = $rootDir;
        $this->env = $env;
        $this->fileSystemCache = new FilesystemCache($rootDir .'/var/cache/'.$env.'/validator', '.metadata.data');
    }

    /**
     * Returns whether metadata for the given class exists in the cache.
     *
     * @param string $class
     * @return bool
     */
    public function has($class)
    {
        return $this->fileSystemCache->contains($class);
    }

    /**
     * Returns the metadata for the given class from the cache.
     *
     * @param string $class Class Name
     *
     * @return ClassMetadata|false A ClassMetadata instance or false on miss
     */
    public function read($class)
    {
        return $this->fileSystemCache->fetch($class);
    }

    /**
     * Stores a class metadata in the cache.
     * @param ClassMetadata $metadata
     */
    public function write(ClassMetadata $metadata)
    {
        $this->fileSystemCache->save($metadata->name, $metadata);
    }
}