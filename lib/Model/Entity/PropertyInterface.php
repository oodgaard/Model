<?php

interface Model_Entity_PropertyInterface
{
    public function set($value);
    
    public function get();

    public function import($value);

    public function export();
}