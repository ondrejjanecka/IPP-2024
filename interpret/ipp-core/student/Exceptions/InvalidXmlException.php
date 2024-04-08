<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class InvalidXmlException extends IPPException
{
    public function __construct(string $message = "Invalid XML format")
    {
        parent::__construct($message, ReturnCode::INVALID_XML_ERROR);
    }
}
