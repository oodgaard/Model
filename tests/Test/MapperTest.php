<?php

namespace Test;
use Model\Mapper\Mapper;
use Testes\Test\UnitAbstract;

/**
 * Tests the Mapper component.
 * 
 * @category Mapping
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class MapperTest extends UnitAbstract
{
    /**
     * Tests a one to one mapping.
     * 
     * @return void
     */
    public function oneToOne()
    {
        $data = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        $mapper = new Mapper;
        $mapper->move('key1', 'my-new-key1');
        $mapper->blacklist('key2');
        
        $output = $mapper->map($data);
        $this->assert(isset($output['my-new-key1']) && $output['my-new-key1'] === 'value1', 'The value for "key1" was not set.');
        $this->assert(!isset($output['key1']), 'The value for "key1" was passed through.');
        $this->assert(!isset($output['key2']), 'The value for "key2" was passed through.');
        
        $mapper->move('key2', 'my-new-key2');
        
        $output = $mapper->map($data);
        $this->assert(isset($output['my-new-key2']) && $output['my-new-key2'] === 'value2', 'The value for "key2" was not set.');
    }
    
    /**
     * Tests a one to two mapping.
     * 
     * @return void
     */
    public function oneToTwo()
    {
        $data = array(
            'key1' => 'value1'
        );

        $mapper = new Mapper;
        $mapper->move('key1', 'ns1.key1');
        
        $output = $mapper->map($data);
        $this->assert(
            isset($output['ns1'])
            && isset($output['ns1']['key1'])
            && $output['ns1']['key1'] === 'value1',
            'The values were not mapped to a second namespace.'
        );
    }
    
    /**
     * Tests a one to three mapping.
     * 
     * @return void
     */
    public function oneToThree()
    {
        $data = array(
            'key1' => 'value1'
        );

        $mapper = new Mapper;
        $mapper->move('key1', 'ns1.subns1.key1');

        $output = $mapper->map($data);
        $this->assert(
            isset($output['ns1'])
            && isset($output['ns1']['subns1'])
            && isset($output['ns1']['subns1']['key1'])
            && $output['ns1']['subns1']['key1'] === 'value1',
            'The values were not mapped to a third namespace.'
        );
    }
    
    /**
     * Covers other scenarios by testing a multi-to-multi one. As long as the
     * previous tests pass and this one passes, any-to-any dimensions of
     * mappings should work.
     * 
     * @return void
     */
    public function manyToMany()
    {
        $data = array(
            'ns1' => array(
                'key1' => 'value1'
            )
        );

        $mapper = new Mapper;
        $mapper->move('ns1.key1', 'ns2.key2');
        
        $output = $mapper->map($data);
        $this->assert(
            isset($output['ns2'])
            && isset($output['ns2']['key2'])
            && $output['ns2']['key2'] === 'value1',
            'Any to any dimensions do not map properly.'
        );
    }
    
    /**
     * Tests to make sure items can be mapped to numeric keys.
     * 
     * @return void
     */
    public function testNumericKeys()
    {
        $data = array(
            'ns1' => array(
                'key1' => 'value1'
            )
        );
        
        $mapper = new Mapper;
        $mapper->move('ns1.key1', 'ns2.1.key2');
        
        $output = $mapper->map($data);
        $this->assert(
            isset($output['ns2'])
            && isset($output['ns2'][1])
            && isset($output['ns2'][1]['key2'])
            && $output['ns2'][1]['key2'] === 'value1',
            'Numeric keys do not map properly.'
        );
    }
    
    /**
     * Tests to make sure auto-incrementing key placeholders work in mappings.
     * 
     * @return void
     */
    public function autoIncrementingKeys()
    {
        $data = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        
        $mapper = new Mapper;
        $mapper->move('key1', '$.key1');
        $mapper->move('key2', '$.key2');
        
        $output = $mapper->map($data);

        $this->assert(
            isset($output[0])
            && isset($output[0]['key1'])
            && $output[0]['key1'] === 'value1'
            && isset($output[1])
            && isset($output[1]['key2'])
            && $output[1]['key2'] === 'value2',
            'Numeric keys do not map properly.'
        );
    }

    /**
     * Tests filtering.
     * 
     * @return void
     */
    public function filtering()
    {
        $data = [
            'key1' => 'val1',
            'key2'  => [
                'key2key1' => 'val1',
                'key2key2' => 'val2'
            ]
        ];

        $mapper = new Mapper;
        $mapper->blacklist('key2');
        $mapper->move('key1', 'fields.key1');
        $mapper->filter(function($from, &$to) {
            foreach ($from['key2'] as $k => $v) {
                $to['fields']['field_' . $k] = $v;
            }
        });

        $mapped = $mapper->map($data);

        $this->assert(isset($mapped['fields']), 'Fields key should have been created.');
        $this->assert(isset($mapped['fields']['key1']), 'First key should have been moved.');
        $this->assert(isset($mapped['fields']['field_key2key1']), 'First sub-array item should have been mapped.');
        $this->assert(isset($mapped['fields']['field_key2key2']), 'Second sub-array item should have been mapped.');
        $this->assert(!array_key_exists('key2', $mapped), 'The key should have been removed.');
    }

    public function childBlacklist()
    {
        $data = [
            'key1' => 'val1',
            'key2' => [
                [
                   'id' => 1,
                   'name' => 'test1',
                   'list' => ['id'=> 2, 'key' => 'set']
                ],
                [
                    'id' => 2, 
                    'name' => 'test2',
                    'list' => ['id'=> 3, 'key' => 'set']
                ],
            ]
        ];

        $mapper = new Mapper;
        $mapper->blacklist('key2.$.id');
        $mapper->blacklist('key2.$.list.$.key');

        $mapped = $mapper->map($data);

        $this->assert(!isset($mapped['key2']['0']['id']), 'Blacklisted key was not removed');
        $this->assert(!isset($mapped['key2']['0']['list']['key']), 'Blacklisted key was not removed');
    }
}
