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
use IPP\Student\Library\FrameLogic;

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
     * @param FrameLogic $frameLogic The frame logic object.
     * @return Constant The constant value.
     * @throws OperandTypeException If the argument type does not match the requested type.
     */
    public static function getConstant(Argument $arg, string $requestedType, FrameLogic $frameLogic)
    {
        if ($arg->getType() === "var")
        {
            $frame = $frameLogic->getFrame(VarHelper::getFrameName($arg->getValue()));
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

    /**
     * Retrieves the constant and type based on the given argument and frame.
     *
     * @param Argument $arg The argument object.
     * @param FrameLogic $frameLogic The frame logic object.
     * @return Constant The constant object containing the type and value.
     */
    public static function getConstantAndType(Argument $arg, FrameLogic $frameLogic)
    {
        if ($arg->getType() === "var")
        {
            $frame = $frameLogic->getFrame(VarHelper::getFrameName($arg->getValue()));
            $variable = $frame->getVariable(VarHelper::getVarName($arg->getValue()));
            return new Constant($variable->getType(), $variable->getValue());
        }
        else
        {
            return new Constant($arg->getType(), $arg->getValue());
        }
    }
}
