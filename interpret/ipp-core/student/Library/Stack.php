<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

/**
 * Represents a stack data structure.
 */
class Stack
{
    /**
     * @var array<mixed> Holds the items in the stack.
     */
    private $stack;

    /**
     * Initializes a new instance of the Stack class.
     */
    public function __construct()
    {
        $this->stack = [];
    }

    /**
     * Pushes an item onto the top of the stack.
     *
     * @param mixed $item The item to push onto the stack.
     */
    public function push($item) : void
    {
        array_push($this->stack, $item);
    }

    /**
     * Removes and returns the item at the top of the stack.
     *
     * @return mixed The item at the top of the stack.
     */
    public function pop()
    {
        return array_pop($this->stack);
    }

    /**
     * Checks if the stack is empty.
     *
     * @return bool True if the stack is empty, false otherwise.
     */
    public function isEmpty(mixed $stack)
    {
        return empty($stack);
    }
}