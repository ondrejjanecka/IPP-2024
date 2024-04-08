<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;

class Variable extends VarHelper
{
    private $name;
    private $frame;
    private $value;

    public function __construct($name, $frame)
    {
        $this->name = $name;
        $this->frame = $frame;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFrame()
    {
        return $this->frame;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        if (is_numeric($this->value))
            return "int";
        elseif (is_bool($this->value))
            return "bool";
        elseif (is_string($this->value))
            return "string";
        elseif ($this->value === null)
            return "nil";
        else
            return null;    
    }
}
