<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors;

use IPP\Student\Executors\Interface\Executor;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;
use IPP\Student\Library\Constant;

use IPP\Student\Helpers\VarHelper;

/**
 * Class DataStackExecutor
 * 
 * This class is responsible for executing data stack instructions.
 * 
 * Implements the PUSHS and POPS instructions.
 */
class DataStackExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "PUSHS",
        "POPS"
    ];

    private Executor $nextExecutor;
    private Instruction $instruction;
    private Memory $memory;

    public function handleRequest(Instruction $instruction, Memory $memory): void
    {
        if (in_array($instruction->opcode, $this->opcodes)) 
        {
            $this->instruction = $instruction;
            $this->memory = $memory;
            $this->execute();
        } 
        else 
            $this->nextExecutor->handleRequest($instruction, $memory);
    }

    public function setNextExecutor(Executor $handler): void
    {
        $this->nextExecutor = $handler;
    }

    private function execute(): void
    {
        switch ($this->instruction->opcode) 
        {
            case "PUSHS":
                $this->executePushs($this->instruction);
                break;
            case "POPS":
                $this->executePops($this->instruction);
                break;
        }
    }

    /**
     * Executes the PUSHS instruction.
     *
     * @param Instruction $instruction The instruction to execute.
     * @return void
     */
    private function executePushs(Instruction $instruction) : void
    {
        $symb = $instruction->getFirstArg();
        $type = $symb->getType();

        if ($type === "var") 
        {
            $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($symb->getValue()));
            $variable = $frame->getVariable(VarHelper::getVarName($symb->getValue()));

            $this->memory->dataStack->push(new Constant($variable->getType(), $variable->getValue()));
        }
        else 
        {
            $this->memory->dataStack->push(new Constant($symb->getType(), $symb->getValue()));
        }
    }

    /**
     * Executes the POPS instruction.
     *
     * @param Instruction $instruction The instruction to execute.
     * @return void
     */
    private function executePops(Instruction $instruction) : void
    {
        $var = $instruction->getFirstArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($var->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($var->getValue()));

        $const = $this->memory->dataStack->pop();
        $variable->setValue($const->getValue());
        $variable->setType($const->getType());
    }

}
