<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;
/**
 * Represents a constant value with a specified type.
 */
class Constant
{
    /**
     * @var string The type of the constant.
     */
    private $type;

    /**
     * @var mixed The value of the constant.
     */
    private $value;

    /**
     * Constructs a new Constant object.
     *
     * @param string $type The type of the constant.
     * @param mixed $value The value of the constant.
     */
    public function __construct($type, $value)
    {
        $this->type = $type;

        if ($type === "int")
            $this->value = (int)$value;
        elseif ($type === "bool")
            $this->value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        elseif ($type === "string")
            $this->value = (string)$value;
        elseif ($type === "nil")
            $this->value = null;
    }

    /**
     * Gets the type of the constant.
     *
     * @return string The type of the constant.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the value of the constant.
     *
     * @return mixed The value of the constant.
     */
    public function getValue()
    {
        return $this->value;
    }
}
