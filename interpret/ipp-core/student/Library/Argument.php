<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

/**
 * Represents an argument with a label, type, and value.
 */
class Argument
{
    /**
     * @var string The type of the argument.
     */
    private $type;

    /**
     * @var mixed The value of the argument.
     */
    private $value;

    /**
     * Argument constructor.
     *
     * @param string $type The type of the argument.
     * @param mixed $value The value of the argument.
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Get the type of the argument.
     *
     * @return string The type of the argument.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of the argument.
     *
     * @return mixed The value of the argument.
     */
    public function getValue()
    {
        return $this->value;
    }
}
