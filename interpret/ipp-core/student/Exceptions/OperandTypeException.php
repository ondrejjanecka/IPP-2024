<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class OperandTypeException extends IPPException
{
    public function __construct(string $message = "Operand type error")
    {
        parent::__construct($message, ReturnCode::OPERAND_TYPE_ERROR);
    }
}
