<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

/**
 * Class EscapeSequenceConvertor
 * 
 * This class provides a method to convert escape sequences in a string.
 */
class EscapeSequenceConvertor
{
    /**
     * Converts escape sequences in a string.
     * 
     * @param string $string The input string with escape sequences.
     * @return string The converted string with escape sequences replaced.
     */
    public static function convert($string): string
    {
        $string = preg_replace_callback('/\\\\([0-9]{3})/', function($matches) {
            $char = mb_chr((int) $matches[1], 'UTF-8');
            return $char;
        }, $string);    
        if ($string === null)
            $string = "";
        return $string;
    }
}
