<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class StringOperationException extends IPPException
{
    public function __construct($message = "String operation error.")
    {
        parent::__construct($message, ReturnCode::STRING_OPERATION_ERROR, null);
    }
}
