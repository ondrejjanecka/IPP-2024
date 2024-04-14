<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Exceptions\FrameAccessException;
use IPP\Student\Exceptions\InvalidSourceStructureException;
use IPP\Student\Library\FrameStack;

/**
 * Class FrameLogic
 * Represents the logic for managing frames in an interpreter.
 */
class FrameLogic
{
    /**
     * @var Frame The global frame.
     */
    private $globalFrame;
    
    /**
     * @var ?Frame The local frame.
     */
    private $localFrame;
    

    /**
     * @var ?Frame The temporary frame.
     */
    private $temporaryFrame;
    
    /**
     * @var FrameStack The frame stack.
     */
    private $frameStack;

    /**
     * FrameLogic constructor.
     * Initializes the global frame and frame stack.
     */
    public function __construct()
    {
        $this->globalFrame = new Frame();
        $this->localFrame = null;
        $this->temporaryFrame = null;
        $this->frameStack = new FrameStack();
    }

    /**
     * Pushes the temporary frame onto the frame stack and sets it as the local frame.
     *
     * @throws FrameAccessException if the temporary frame is null.
     */
    public function pushTempFrame(): void
    {
        if ($this->temporaryFrame === null)
            throw new FrameAccessException();
        $this->frameStack->push($this->temporaryFrame);
        $this->localFrame = $this->temporaryFrame;
        $this->temporaryFrame = null;
    }

    /**
     * Creates a new temporary frame.
     */
    public function createFrame(): void
    {
        $this->temporaryFrame = new Frame();
    }

    /**
     * Pops the top frame from the frame stack and sets it as the temporary frame.
     */
    public function popFrame(): void
    {
        $this->temporaryFrame = $this->frameStack->pop();
        $this->localFrame = $this->frameStack->peek();
    }

    /**
     * Retrieves the specified frame.
     *
     * @param string $name The name of the frame to retrieve ("GF", "LF", or "TF").
     * @return Frame The retrieved frame.
     * @throws FrameAccessException if the frame does not exist.
     * @throws InvalidSourceStructureException if the name is invalid.
     */
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
