<?php

namespace Test;
use Exception;
use Model\Entity\Set;
use Provider\CommentEntity;
use Provider\ContentEntity;
use Provider\UserEntity;
use Testes\Test\UnitAbstract;

/**
 * Tests the Entity component.
 * 
 * @category Entities
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class EntityTest extends UnitAbstract
{
    /**
     * Ensures that data is properly imported when passing through the constructor.
     * 
     * @return void
     */
    public function constructorImporting()
    {
        $entity = new ContentEntity(array('id' => 1, 'name' => 'test'));
        $this->assert($entity->id && $entity->name, 'The id or name was not set.');
    }
    
    /**
     * Ensures that relationships are properly handled when getting/setting.
     * 
     * @return void
     */
    public function relationships()
    {
        $entity = new ContentEntity;
        $this->assert($entity->user instanceof UserEntity, 'User relationship was not instantiated.');
        $this->assert($entity->comments instanceof Set, 'Comments relationship was not instantiated.');
        
        try {
            $entity->comments->offsetSet(0, new CommentEntity);
        } catch (Exception $e) {
            $this->assert(false, 'Entity could not be added to set.');
        }
    }
    
    /**
     * Tests proxy functionality.
     * 
     * @return void
     */
    public function testMappedGetters()
    {
        $user    = new UserEntity;
        $content = $user->getContent();
        
        $this->assert(count($content) === 2, 'There must be 2 content items returned.');
        $this->assert($content instanceof Set, 'The content items must be an entity set.');
        $this->assert($user->isLastAdministrator === true, 'The user must be the last administrator.');
    }
}