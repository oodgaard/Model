<?php

use Habitat\EntitySet;
use Provider\ContentEntity;

/**
 * Tests the EntitySet component.
 * 
 * @category EntitySets
 * @package  Habitat
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Test_EntitySet extends Testes_UnitTest_Test
{
    public function setUp()
    {
        $this->set = new EntitySet('\Provider\ContentEntity');
        for ($i = 1; $i <= 10; $i++) {
            $this->set[] = new ContentEntity(array('id' => $i, 'name' => 'test ' . $i));
        }
    }
    
    public function aggregation()
    {
        $aggregated = $this->set->aggregate('id');
        
        // test aggregation count against the set count
        $this->assert(count($aggregated) === $this->set->count(), 'The aggregated items should match the length of the test set.');
        
        // test each aggregated item against each item in the set
        foreach ($this->set as $key => $item) {
            $this->assert(isset($aggregated[$key]) && $aggregated[$key] === $item->id, 'Item "' . $item->id . '" was not aggregated.');
        }
    }
    
    public function findingMany()
    {
        $query = array('name' => '^test [1-2]$');
        $found = $this->set->find($query);
        
        $this->assert($found->count() === 2, 'Wrong number of items found.');
        $this->assert($found[0]->id === 1, 'The first item should have an id of 1.');
        $this->assert($found[1]->id === 2, 'The first item should have an id of 2.');
        
        $found = $this->set->find($query, 1);
        $this->assert($found->count() === 1, 'The query should have only found one item.');
        $this->assert($found[0]->id === 1, 'The item found should have had an id of 1.');
        
        $found = $this->set->find($query, 1, 1);
        $this->assert($found[0]->id === 2, 'The item found should have an id of 2.');
    }
    
    public function findingOne()
    {
        $query = array('name' => '^test\s\d+$');
        $found = $this->set->findOne($query);
        
        $this->assert($found instanceof ContentEntity, 'Item found should be an instance of an entity.');
        $this->assert($found->id === 1, 'The first item should have been returned.');
    }
}