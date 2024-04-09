<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Library\Frame;
use IPP\Student\Exceptions\FrameAccessException;

/**
 * Represents a stack data structure.
 */
class Stack
{
    /**
     * @var array<Frame> Holds the items in the stack.
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
     * @param Frame $item The item to push onto the stack.
     */
    public function push($item) : void
    {
        array_push($this->stack, $item);
    }

    /**
     * Removes and returns the item at the top of the stack.
     *
     * @return Frame The item at the top of the stack.
     */
    public function pop()
    {
        if ($this->isEmpty()) 
            throw new FrameAccessException();

        return array_pop($this->stack);
    }

    /**
     * Returns the item at the top of the stack without removing it.
     *
     * @return Frame The item at the top of the stack.
     */
    public function top()
    {
        return end($this->stack);
    }

    /**
     * Checks if the stack is empty.
     *
     * @return bool True if the stack is empty, false otherwise.
     */
    public function isEmpty()
    {
        return empty($this->stack);
    }
}
