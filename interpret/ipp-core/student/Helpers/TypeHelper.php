<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

class TypeHelper
{
    public static function getType($variable)
    {
        if (is_int($variable))
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