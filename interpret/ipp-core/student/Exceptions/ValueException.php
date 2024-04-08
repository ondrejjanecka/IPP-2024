<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class ValueException extends IPPException
{
    public function __construct($message)
    {
        parent::__construct($message, ReturnCode::VALUE_ERROR);
    }
}
