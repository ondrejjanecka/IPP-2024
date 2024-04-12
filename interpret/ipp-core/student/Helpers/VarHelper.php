<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

use IPP\Student\Exceptions\InvalidXmlException;

/**
 * The VarHelper class provides helper methods for working with variables.
 */
class VarHelper
{
    /**
     * Retrieves the name of the variable from the given string representation.
     *
     * @param string $var The string representation of the variable.
     * @return string The name of the variable.
     */
    public static function getVarName($var): string
    {
        $pos = strpos($var, "@");
        
        $name = substr($var, $pos + 1);

        if ($name == null)
            throw new InvalidXmlException("Invalid XML format");

        return $name;
    }

    /**
     * Retrieves the name of the frame from the given string representation.
     *
     * @param string $var The string representation of the variable.
     * @return string The name of the frame.
     */
    public static function getFrameName($var): string
    {
        (int) $pos = strpos($var, "@");

        if ($pos == null)
            throw new InvalidXmlException("Invalid XML format");

        $name = substr($var, 0, $pos);

        return $name;
    }
}
