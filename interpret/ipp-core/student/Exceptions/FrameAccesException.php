<?php
/**
 * IPP - PHP Project Exceptions
 * @author Ondřej Janečka
 */

namespace IPP\Student\Exceptions;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;

class FrameAccessException extends IPPException
{
    public function __construct(string $message = "Frame access error")
    {
        parent::__construct($message, ReturnCode::FRAME_ACCESS_ERROR);
    }
}