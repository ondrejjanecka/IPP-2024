<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

/**
 * CustomError class provides methods for printing error messages and exiting the program.
 */
class CustomError
{
    /**
     * Prints an error message to the standard error output and exits the program with the specified exit code.
     *
     * @param string $message The error message to be printed.
     * @param int $exitCode The exit code to be used when exiting the program.
     * @return void
     */
    public static function printErrorExit(string $message, int $exitCode): void
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit($exitCode);
    }
}