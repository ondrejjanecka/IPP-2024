<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;
use IPP\Student\Helpers\TypeHelper;

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
        return TypeHelper::getType($this->value);   
    }
}
