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
use IPP\Student\Library\Variable;

use IPP\Student\Helpers\VarHelper;

use IPP\Student\Exceptions\SemanticException;
use IPP\Student\Exceptions\ValueException;

/**
 * Class MemoryAndCallsExecutor
 * 
 * This class is responsible for executing memory and call instructions.
 * 
 * Implements the MOVE, CREATEFRAME, PUSHFRAME, POPFRAME, DEFVAR, CALL, and RETURN instructions.
 */
class MemoryAndCallsExecutor implements Executor 
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "MOVE",
        "CREATEFRAME",
        "PUSHFRAME",
        "POPFRAME",
        "DEFVAR",
        "CALL",
        "RETURN"
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
            case "MOVE":
                $this->executeMove($this->instruction);
                break;
            case "CREATEFRAME":
                $this->memory->frameLogic->createFrame();
                break;
            case "PUSHFRAME":
                $this->memory->frameLogic->pushTempFrame();
                break;
            case "POPFRAME":
                $this->memory->frameLogic->popFrame();
                break;
            case "DEFVAR":
                $this->executeDefVar($this->instruction);
                break;
            case "CALL":
                $this->executeCall($this->instruction);
                break;
            case "RETURN":
                $this->executeReturn($this->instruction);
                break;
        }
    }

    /**
     * Executes the MOVE instruction.
     *
     * @param Instruction $instruction The move instruction to execute.
     * @return void
     */
    private function executeMove(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame1 = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));

        $variable = $frame1->getVariable(VarHelper::getVarName($arg1->getValue()));

        if ($arg2->getType() === "var") 
        {
            $frame2 = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg2->getValue()));
            $symb = $frame2->getVariable(VarHelper::getVarName($arg2->getValue()));
        }
        else 
        {
            $symb = new Constant($arg2->getType(), $arg2->getValue());
        }
        
        $variable->setValue($symb->getValue());
        $variable->setType($symb->getType());
    }

    /**
     * Executes the DEFVAR instruction.
     *
     * @param Instruction $instruction The DEFVAR instruction to execute.
     * @return void
     */
    private function executeDefVar(Instruction $instruction) : void
    {
        $var = $instruction->getFirstArg();

        $variable = new Variable(VarHelper::getVarName($var->getValue()), VarHelper::getFrameName($var->getValue()));

        $frame = $this->memory->frameLogic->getFrame($variable->getFrame());
        $frame->addVariable($variable);
    }

    /**
     * Executes a call instruction.
     *
     * @param Instruction $instruction The call instruction to execute.
     * @throws SemanticException If the label specified in the instruction is not found.
     */
    private function executeCall(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $label = $arg1->getValue();

        if (!isset($this->memory->labels[$label])) 
        {
            throw new SemanticException("Label not found: $label");
        }

        $this->memory->callStack->push($this->memory->instructionPointer);
        $this->memory->instructionPointer = $this->memory->labels[$label];
    }

    /**
     * Executes a return instruction.
     *
     * @param Instruction $instruction The return instruction to execute.
     * @throws ValueException If the return is called without a call.
     */
    private function executeReturn(Instruction $instruction) : void
    {
        $index = $this->memory->callStack->pop();
        if ($index === null) 
            throw new ValueException("Return called without a call");

        $this->memory->instructionPointer = $index;
    }
}
