<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class OperandValueException extends IPPException
{
    public function __construct($message = "Operand value error.")
    {
        parent::__construct($message, ReturnCode::OPERAND_VALUE_ERROR, null);
    }
}
