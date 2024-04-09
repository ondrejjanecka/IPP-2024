<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class VariableAccessException extends IPPException
{
    public function __construct(string $message = "Variable access error")
    {
        parent::__construct($message, ReturnCode::VARIABLE_ACCESS_ERROR);
    }
}
