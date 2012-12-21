<?php

namespace Model\Util;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class DotNotatedArray implements ArrayAccess, Countable, IteratorAggregate
{
    private $arrays = [];

    private $data = [];

    public function offsetSet($name, $value)
    {
        if (!$name) {
            return;
        }

        extract($this->parseDotNotatedName($name));

        if (!isset($this->arrays[$first])) {
            $this->arrays[$first] = new self;
        }

        if ($deep) {
            $this->arrays[$first]->offsetSet($rest, $value);
        } else {
            $this->data[$first] = $value;
        }

        return $this;
    }

    public function offsetGet($name)
    {
        if (!$name) {
            return [];
        }

        extract($this->parseDotNotatedName($name));

        $data = [];

        if (isset($this->data[$first])) {
            array_push($data, $this->data[$first]);
        }

        if (isset($this->arrays[$first])) {
            $data = array_merge($data, $this->arrays[$first][$rest]);
        }

        return $data;
    }

    public function offsetExists($name)
    {
        if (!$name) {
            return false;
        }
        
        extract($this->parseDotNotatedName($name));

        return isset($this->data[$first]) || (
            isset($this->arrays[$first]) &&
            isset($this->arrays[$first][$rest])
        );
    }

    public function offsetUnset($name)
    {
        if (!$name) {
            return $this;
        }
        
        extract($this->parseDotNotatedName($name));

        if ($rest && isset($this->arrays[$first])) {
            unset($this->arrays[$first][$rest]);
        } else {
            unset($this->data[$first]);
            unset($this->arrays[$first]);
        }

        return $this;
    }

    public function count()
    {
        return count($this->data) + count($this->arrays);
    }

    public function getIterator()
    {
        return new RecursiveIteratorIterator(new RecursiveArrayIterator(array_merge($this->data, $this->arrays)));
    }

    private function parseDotNotatedName($name)
    {
        if (isset(self::$cache[$name])) {
            return self::$cache[$name];
        }

        $all   = explode('.', $name);
        $rest  = $all;
        $first = array_shift($rest);
        $rest  = implode('.', $rest);

        return self::$cache[$name] = [
            'first' => $first,
            'rest'  => $rest,
            'all'   => $all,
            'deep'  => $rest ? true : false
        ];
    }
}