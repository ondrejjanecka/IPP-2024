<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

class Stack
{
    private $stack;

    public function __construct()
    {
        $this->stack = [];
    }

    public function push($item)
    {
        array_push($this->stack, $item);
    }

    public function pop()
    {
        return array_pop($this->stack);
    }

    public function top()
    {
        return end($this->stack);
    }

    public function isEmpty()
    {
        return empty($this->stack);
    }
}
