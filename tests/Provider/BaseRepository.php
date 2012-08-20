<?php

namespace Provider;
use Exception;
use Model\Cache\Php;
use Model\Entity\Entity;
use Model\Repository\RepositoryAbstract;

abstract class BaseRepository extends RepositoryAbstract
{
    /**
     * Keeps track of the number of times "findById()" was called so we can test
     * if an item was cached or not.
     * 
     * @var int
     */
    public $findByIdCallCount = 0;
    
    /**
     * Mimics data storage for the entities.
     * 
     * @var array
     */
    private $entities = array();

    public function __construct()
    {
        $this->setCacheDriver('findById', new Php);
    }
    
    public function save(Entity $entity)
    {
        if ($entity->id) {
            $this->update($entity);
        } else {
            $this->insert($entity);
        }
        return $this;
    }
    
    public function remove(Entity $entity)
    {
        // expire the cache
        $this->clearCache('findById', array($entity->id));
        
        // then remove the item from the storage property
        unset($this->entities[$entity->id]);
    }
    
    protected function findById($id)
    {
        if (isset($this->entities[$id])) {
            $entity = $this->entities[$id];
        } else {
            $entity = false;
        }
        
        // keep track of the number of times this method was called for testing
        ++$this->findByIdCallCount;
        
        return $entity;
    }
    
    private function insert(Entity $entity)
    {
        // generate an id
        $entity->id = md5(microtime());
        
        // store in entity storage based on id
        $this->entities[$entity->id] = $entity;
        
        // store in cache for the specified method
        $this->setCache('findById', array($entity->id), $entity);
    }
    
    private function update(Entity $entity)
    {
        // make sure that it exists first as it can only be updated if it already exists
        // mimics database behavior
        if (!isset($this->entities[$entity->id])) {
            throw new Exception(get_class($entity) . ' does not exists, therefore it was not updated.');
        }
        
        // update the stored entity
        $this->entities[$entity->id] = $entity;
        
        // update the cache
        $this->setCacheFor(get_class($this), 'findById', array($entity->id), $entity);
    }
}