<?php

namespace Model\Vo;

class Set extends VoAbstract
{
    public function translate($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        $temp = [];

        if (is_object($value)) {
            foreach ($value as $k => $v) {
                $temp[$k] = $v;
            }
        }

        return $temp;
    }
}