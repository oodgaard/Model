<?php

use Model\EntitySet;
use Provider\CommentEntity;
use Provider\ContentEntity;
use Provider\UserEntity;

/**
 * Tests the Entity component.
 * 
 * @category Entities
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Test_Entity extends Testes_UnitTest_Test
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
     * Ensures that constructor events are called upon construction.
     * 
     * @return void
     */
    public function constructorEvents()
    {
        $content = new ContentEntity;
        $this->assert($content->init, 'Entity init() was not triggered.');
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
        $this->assert($entity->comments instanceof EntitySet, 'Comments relationship was not instantiated.');
        
        try {
            $entity->comments->offsetSet(0, new CommentEntity);
        } catch (\Exception $e) {
            $this->assert(false, 'Entity could not be added to set.');
        }
    }
}