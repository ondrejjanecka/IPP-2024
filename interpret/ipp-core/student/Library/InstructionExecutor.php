<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Library\{
    Instruction,
    FrameLogic,
    DataStack
};

use IPP\Student\Exceptions\SemanticException;
use IPP\Student\Library\Memory;

use IPP\Core\Interface\{
    InputReader,
    OutputWriter
};

use IPP\Student\Executors\{
    ArithmeticExecutor,
    ControlFlowExecutor,
    DataStackExecutor,
    DebugExecutor,
    IOExecutor,
    MemoryAndCallsExecutor,
    RelBoolConvertExecutor,
    StringTypeExecutor
};

/**
 * Class InstructionExecutor
 *
 * This class is responsible for executing instructions in the interpreter.
 */
class InstructionExecutor
{
    /**
     * @var array<Instruction> Holds the instructions to execute.
     */
    private array $instructions;

    private Memory $memory;
    
    /**
     * @param array<Instruction> $instructions
     * @param InputReader $input
     * @param OutputWriter $stdout
     */
    public function __construct(array $instructions, InputReader $input, OutputWriter $stdout)
    {
        $this->instructions = $instructions;
        $this->memory = new Memory($input, $stdout, new FrameLogic(), new DataStack(), [], 1, new Stack());
    }

    /**
     * Executes the instructions stored in the InstructionExecutor object.
     *
     * This method iterates over the instructions array and executes each instruction
     * using the executeInstruction() method. It starts from the current instruction index
     * and continues until the end of the instructions array.
     *
     * @return void
     */
    public function executeInstructions() : void
    {
        $this->defineLabels();
        
        while ($this->memory->instructionPointer <= count($this->instructions)) 
        {
            $instruction = $this->instructions[$this->memory->instructionPointer-1];
            $this->executeInstruction($instruction);
        }
    }

    /**
     * Defines labels in the instructions array.
     *
     * This method iterates over the instructions array and checks for instructions with the opcode "LABEL".
     * For each "LABEL" instruction found, it extracts the label name and stores the index of the instruction in the labels array.
     * If a duplicate label is found, a SemanticException is thrown.
     *
     * @throws SemanticException if a duplicate label is found.
     * @return void
     */
    private function defineLabels() : void
    {
        foreach ($this->instructions as $index => $instruction) 
        {
            if ($instruction->opcode === "LABEL") 
            {
                $labelName = $instruction->getFirstArg()->getValue();
                
                if (isset($this->memory->labels[$labelName])) {
                    throw new SemanticException("Duplicate label found: $labelName");
                }

                $this->memory->labels[$labelName] = $index;
            }
        }
    }

    /**
     * Executes the given instruction.
     *
     * This method executes the given instruction by passing it through a chain of executors.
     * The chain of executors is defined in the handleRequest() method of each executor.
     * The instruction is passed to the first executor in the chain, which then decides whether to handle the instruction or pass it to the next executor.
     * The chain of executors is as follows:
     * MemoryAndCallsExecutor -> ArithmeticExecutor -> RelBoolConvertExecutor -> ControlFlowExecutor -> DataStackExecutor -> StringTypeExecutor -> IOExecutor -> DebugExecutor
     *
     * @param Instruction $instruction The instruction to execute.
     * @return void
     */
    private function executeInstruction(Instruction $instruction) : void
    {
        $ArithmeticExecutor = new ArithmeticExecutor();
        $ControlFlowExecutor = new ControlFlowExecutor();
        $DataStackExecutor = new DataStackExecutor();
        $DebugExecutor = new DebugExecutor();
        $IOExecutor = new IOExecutor();
        $MemoryAndCallsExecutor = new MemoryAndCallsExecutor();
        $RelBoolConvertExecutor = new RelBoolConvertExecutor();
        $StringTypeExecutor = new StringTypeExecutor();

        $MemoryAndCallsExecutor->setNextExecutor($ArithmeticExecutor);
        $ArithmeticExecutor->setNextExecutor($RelBoolConvertExecutor);
        $RelBoolConvertExecutor->setNextExecutor($ControlFlowExecutor);
        $ControlFlowExecutor->setNextExecutor($DataStackExecutor);
        $DataStackExecutor->setNextExecutor($StringTypeExecutor);
        $StringTypeExecutor->setNextExecutor($IOExecutor);
        $IOExecutor->setNextExecutor($DebugExecutor);

        $MemoryAndCallsExecutor->handleRequest($instruction, $this->memory);

        $this->memory->instructionPointer++;
    }
}
