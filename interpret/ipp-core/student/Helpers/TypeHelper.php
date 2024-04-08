<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

/**
 * Class TypeHelper
 * 
 * This class provides helper methods for determining the type of a variable.
 */
class TypeHelper
{
    /**
     * Get the type of a variable.
     * 
     * @param mixed $variable The variable to determine the type of.
     * @return string|null The type of the variable, or null if the type cannot be determined.
     */
    public static function getType($variable)
    {
        if (is_numeric($variable))
            return "int";
        elseif (is_bool($variable))
            return "bool";
        elseif (is_string($variable))
            return "string";
        elseif ($variable === null)
            return "nil";
        else
            return null;
    }
}