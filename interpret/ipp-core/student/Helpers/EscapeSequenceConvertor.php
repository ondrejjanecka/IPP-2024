<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

class EscapeSequenceConvertor
{
    public static function convert($string): string
    {
        $string = str_replace('\010', "\n", $string);

        return $string;
    }
}
