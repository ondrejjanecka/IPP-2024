<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Library\Frame;
use IPP\Student\Exceptions\FrameAccessException;

/**
 * Represents a frame stack data structure.
 */
class FrameStack extends Stack
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
     * Overrides the parent method to ensure that only Frame objects can be pushed onto the stack.
     * 
     * @param Frame $item The item to push onto the stack.
     */
    public function push($item) : void
    {
        array_push($this->stack, $item);
    }

    /**
     * Removes and returns the item at the top of the stack.
     * Overrides the parent method to ensure that only Frame objects can be popped from the stack.
     * 
     * @return Frame The item at the top of the stack.
     * @throws FrameAccessException
     */
    public function pop()
    {
        if ($this->isEmpty($this->stack)) 
            throw new FrameAccessException();

        return array_pop($this->stack);
    }
}
