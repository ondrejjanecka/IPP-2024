<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

class VarHelper
{
    public static function getVarName($var): string
    {
        $pos = strpos($var, "@");
        
        $name = substr($var, $pos + 1);

        return $name;
    }

    public static function getFrameName($var): string
    {
        $pos = strpos($var, "@");

        $name = substr($var, 0, $pos);

        return $name;
    }
}
