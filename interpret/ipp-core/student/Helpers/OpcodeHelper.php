<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

/**
 * Class OpcodeHelper
 * 
 * This class provides helper methods for working with opcodes.
 */
class OpcodeHelper
{
    private static $allowedOpcodes = [];


    private static $zeroArgsOpcodes = [
        "CREATEFRAME",
        "PUSHFRAME",
        "POPFRAME",
        "RETURN",
        "BREAK"
    ];

    private static $oneArgsOpcodes = [
        "DEFVAR",
        "CALL",
        "PUSHS",
        "WRITE",
        "LABEL",
        "JUMP",
        "EXIT",
        "DPRINT"
    ];

    private static $twoArgsOpcodes = [
        "MOVE",
        "INT2CHAR",
        "READ",
        "STRLEN",
        "TYPE",
        "NOT"
    ];

    private static $threeArgsOpcodes = [
        "POPS",
        "ADD",
        "SUB",
        "MUL",
        "IDIV",
        "LT",
        "GT",
        "EQ",
        "AND",
        "OR",
        "STRI2INT",
        "CONCAT",
        "GETCHAR",
        "SETCHAR",
        "JUMPIFEQ",
        "JUMPIFNEQ"
    ];

    /**
     * Checks if the given opcode is allowed.
     *
     * @param string $opcode The opcode to check.
     * @return bool True if the opcode is allowed, false otherwise.
     */
    public static function isOpcodeAllowed(string $opcode): bool
    {
        self::$allowedOpcodes = array_merge(self::$zeroArgsOpcodes, self::$oneArgsOpcodes, self::$twoArgsOpcodes, self::$threeArgsOpcodes);

        return in_array(strtoupper($opcode), self::$allowedOpcodes);
    }

    /**
     * Checks if the given opcode has the expected number of arguments.
     *
     * @param string $opcode The opcode to check.
     * @param int $argCount The expected number of arguments.
     * @return bool Returns true if the opcode has the expected number of arguments, false otherwise.
     */
    public static function checkArgCount(string $opcode, int $argCount): bool
    {
        if (self::isZeroArgOpcode($opcode) && $argCount === 0) 
            return true;

        if (self::isOneArgOpcode($opcode) && $argCount === 1)
            return true;

        if (self::isTwoArgOpcode($opcode) && $argCount === 2)
            return true;

        if (self::isThreeArgOpcode($opcode) && $argCount === 3)
            return true;

        return false;
    }

    /**
     * Checks if the given opcode is a zero-argument opcode.
     *
     * @param string $opcode The opcode to check.
     * @return bool True if the opcode is a zero-argument opcode, false otherwise.
     */
    private static function isZeroArgOpcode(string $opcode): bool
    {
        return in_array(strtoupper($opcode), self::$zeroArgsOpcodes);
    }

    /**
     * Checks if the given opcode is a one-argument opcode.
     *
     * @param string $opcode The opcode to check.
     * @return bool True if the opcode is a one-argument opcode, false otherwise.
     */
    private static function isOneArgOpcode(string $opcode): bool
    {
        return in_array(strtoupper($opcode), self::$oneArgsOpcodes);
    }

    /**
     * Checks if the given opcode is a two-argument opcode.
     *
     * @param string $opcode The opcode to check.
     * @return bool True if the opcode is a two-argument opcode, false otherwise.
     */
    private static function isTwoArgOpcode(string $opcode): bool
    {
        return in_array(strtoupper($opcode), self::$twoArgsOpcodes);
    }

    /**
     * Checks if the given opcode is a three-argument opcode.
     *
     * @param string $opcode The opcode to check.
     * @return bool True if the opcode is a three-argument opcode, false otherwise.
     */
    private static function isThreeArgOpcode(string $opcode): bool
    {
        return in_array(strtoupper($opcode), self::$threeArgsOpcodes);
    }
}
