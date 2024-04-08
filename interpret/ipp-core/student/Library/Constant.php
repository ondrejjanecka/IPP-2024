<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

class Constant
{
    private $type;
    private $value;

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

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }
}
