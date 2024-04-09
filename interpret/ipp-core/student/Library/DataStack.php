<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

 namespace IPP\Student\Library;

 use IPP\Student\Library\Stack;
 use IPP\Student\Library\Constant;
 use IPP\Student\Exceptions\ValueException;

 /**
  * Represents a stack data structure.
  */
 class DataStack extends Stack
 {
    /**
     * @var array<Constant> Holds the items in the stack.
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
      * @param Constant $item The item to push onto the stack.
      */
     public function push($item) : void
     {
         array_push($this->stack, $item);
     }
 
     /**
      * Removes and returns the item at the top of the stack.
      *
      * @return Constant The item at the top of the stack.
      */
     public function pop()
     {
        if ($this->isEmpty($this->stack)) 
            throw new ValueException();
         return array_pop($this->stack);
     }
}
