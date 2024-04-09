<?php

namespace IPP\Student\Library;

use IPP\Student\Exceptions\FrameAccessException;
use IPP\Student\Exceptions\InvalidSourceStructureException;
use IPP\Student\Library\Stack;

class FrameLogic
{
    private $globalFrame;
    private $localFrame;
    private $temporaryFrame;
    private $frameStack;

    public function __construct()
    {
        $this->globalFrame = new Frame();
        $this->frameStack = new Stack();
    }

    public function pushTempFrame(): void
    {
        if ($this->temporaryFrame === null)
            throw new FrameAccessException();
        $this->frameStack->push($this->temporaryFrame);
        $this->localFrame = $this->temporaryFrame;
        $this->temporaryFrame = null;
    }

    public function createFrame(): void
    {
        $this->temporaryFrame = new Frame();
    }

    public function popFrame(): void
    {
        $this->temporaryFrame = $this->frameStack->pop();
    }

    public function getFrame(string $name): Frame
    {
        if ($name === "GF")
            return $this->globalFrame;
        elseif ($name === "LF")
            return $this->localFrame ?? throw new FrameAccessException();
        elseif ($name === "TF")
            return $this->temporaryFrame ?? throw new FrameAccessException();
        else
            throw new InvalidSourceStructureException();
    }
}
