<?php
/**
 * IPP - PHP Project Helpers
 * @author Ondřej Janečka
 */

namespace IPP\Student\Helpers;

use IPP\Student\Library\Constant;
use IPP\Student\Library\Argument;
use IPP\Student\Helpers\VarHelper;
use IPP\Student\Library\Frame;
use IPP\Student\Exceptions\OperandTypeException;

/**
 * Class SymbolHelper
 * 
 * This class provides helper methods for working with symbols.
 */
class SymbolHelper
{
    /**
     * Get the constant value based on the argument, requested type, and frame.
     *
     * @param Argument $arg The argument object.
     * @param string $requestedType The requested type of the constant.
     * @param Frame $frame The frame object.
     * @return Constant The constant value.
     * @throws OperandTypeException If the argument type does not match the requested type.
     */
    public static function getConstant(Argument $arg, string $requestedType, Frame $frame)
    {
        if ($arg->getType() === "var")
        {
            $variable = $frame->getVariable(VarHelper::getVarName($arg->getValue()));

            if ($variable->getType() === $requestedType)
                return new Constant($variable->getType(), $variable->getValue());
            else
                throw new OperandTypeException("Expected type $requestedType, got " . $variable->getType());
        }
        else if ($arg->getType() === $requestedType)
        {
            return new Constant($arg->getType(), $arg->getValue());
        }
        else
        {
            throw new OperandTypeException("Expected type $requestedType, got " . $arg->getType());
        } 
    }

    public static function getConstantAndType(Argument $arg, Frame $frame)
    {
        if ($arg->getType() === "var")
        {
            $variable = $frame->getVariable(VarHelper::getVarName($arg->getValue()));
            return new Constant($variable->getType(), $variable->getValue());
        }
        else
        {
            return new Constant($arg->getType(), $arg->getValue());
        }
    }
}
