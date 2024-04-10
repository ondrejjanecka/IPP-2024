<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;

/**
 * Class Memory
 * 
 * This class represents the Memory of the interpreter.
 */
class Memory
{
    public InputReader $input;
    public OutputWriter $stdout;

    public FrameLogic $frameLogic;
    public DataStack $dataStack;

    /**
     * @var array<int> Holds the indexes of defined labels.
     */
    public array $labels;
    public int $instructionPointer;
    public Stack $callStack;

    /**
     * Constructor of the Memory object.
     * 
     * @param InputReader $input
     * @param OutputWriter $stdout
     * @param FrameLogic $frameLogic
     * @param DataStack $dataStack
     * @param array<int> $labels
     * @param int $instructionPointer
     * @param Stack $callStack
     */
    public function __construct(InputReader $input, OutputWriter $stdout, FrameLogic $frameLogic, DataStack $dataStack, array $labels, int $instructionPointer, Stack $callStack)
    {
        $this->input = $input;
        $this->stdout = $stdout;
        $this->frameLogic = $frameLogic;
        $this->dataStack = $dataStack;
        $this->labels = $labels;
        $this->instructionPointer = $instructionPointer;
        $this->callStack = $callStack;
    }
}
