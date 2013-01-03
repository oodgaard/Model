<?php

namespace Model\Configurator\DocComment;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class DocTagCollection implements Countable, IteratorAggregate
{
    private $tags = [];

    public function add(DocTagInterface $tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function get($name)
    {
        foreach ($this->tags as $tag) {
            if ($tag->name() === $name) {
                return $tag;
            }
        }
    }

    public function getAll($name)
    {
        $tags = [];

        foreach ($this->tags as $tag) {
            if ($tag->name() === $name) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }

    public function count()
    {
        return count($this->tags);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->tags);
    }
}