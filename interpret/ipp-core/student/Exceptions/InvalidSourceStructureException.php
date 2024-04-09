<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class InvalidSourceStructureException extends IPPException
{
    public function __construct(string $message = "Invalid source structure")
    {
        parent::__construct($message, ReturnCode::INVALID_SOURCE_STRUCTURE);
    }
}
