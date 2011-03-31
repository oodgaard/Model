<?php

use Habitat\Mapper;

/**
 * Tests the Mapper component.
 * 
 * @category Mapping
 * @package  Habitat
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Test_Mapper extends Testes_UnitTest_Test
{
    /**
     * Tests a one to one mapping.
     * 
     * @return void
     */
    public function oneToOne()
    {
        $mapper = new Mapper(array(
            'key1' => 'value1',
            'key2' => 'value2'
        ));
        $mapper->map('key1', 'my-new-key1');
        
        $output = $mapper->convert();
        $this->assert(isset($output['my-new-key1']) && $output['my-new-key1'] === 'value1', 'The value for "key1" was not set.');
        $this->assert(!isset($output['key1']), 'The value for "key1" was passed through.');
        $this->assert(!isset($output['key2']), 'The value for "key2" was passed through.');
        
        $mapper->map('key2', 'my-new-key2');
        
        $output = $mapper->convert();
        $this->assert(isset($output['my-new-key2']) && $output['my-new-key2'] === 'value2', 'The value for "key2" was not set.');
    }
    
    /**
     * Tests a one to two mapping.
     * 
     * @return void
     */
    public function oneToTwo()
    {
        $mapper = new Mapper(array(
            'key1' => 'value1'
        ));
        $mapper->map('key1', 'ns1.key1');
        
        $output = $mapper->convert();
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
        $mapper = new Mapper(array(
            'key1' => 'value1'
        ));
        $mapper->map('key1', 'ns1.subns1.key1');

        $output = $mapper->convert();
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
        $mapper = new Mapper(array(
            'ns1' => array(
                'key1' => 'value1'
            )
        ));
        $mapper->map('ns1.key1', 'ns2.key2');
        
        $output = $mapper->convert();
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
        $mapper = new Mapper(array(
            'ns1' => array(
                'key1' => 'value1'
            )
        ));
        $mapper->map('ns1.key1', 'ns2.1.key2');
        
        $output = $mapper->convert();
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
        $mapper = new Mapper(array(
            'key1' => 'value1',
            'key2' => 'value2'
        ));
        $mapper->map('key1', '$.key1');
        $mapper->map('key2', '$.key2');
        
        $output = $mapper->convert();
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
}