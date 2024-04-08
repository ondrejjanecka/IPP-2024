<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;
use IPP\Student\Helpers\TypeHelper;
use IPP\Student\Exceptions\ValueException;

/**
 * Represents a variable in the program.
 */
class Variable extends VarHelper
{
    /**
     * @var string The name of the variable.
     */
    private $name;

    /**
     * @var string The frame of the variable.
     */
    private $frame;

    /**
     * @var mixed The value of the variable.
     */
    private $value;

    /**
     * Constructs a new Variable object.
     *
     * @param string $name The name of the variable.
     * @param string $frame The frame of the variable.
     */
    public function __construct($name, $frame)
    {
        $this->name = $name;
        $this->frame = $frame;
    }

    /**
     * Gets the name of the variable.
     *
     * @return string The name of the variable.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the frame of the variable.
     *
     * @return string The frame of the variable.
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * Gets the value of the variable.
     *
     * @return mixed The value of the variable.
     */
    public function getValue()
    {
        if (is_null($this->value ))
            throw new ValueException("Value is missing");

        else
            return $this->value;
    }

    /**
     * Sets the value of the variable.
     *
     * @param mixed $value The new value of the variable.
     */
    public function setValue($value) : void
    {
        $this->value = $value;
    }

    /**
     * Gets the type of the variable.
     *
     * @return string The type of the variable.
     */
    public function getType()
    {
        return TypeHelper::getType($this->value);   
    }
}
