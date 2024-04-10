<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors;

use IPP\Student\Executors\Interface\Executor;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;

use IPP\Student\Helpers\SymbolHelper;

use IPP\Student\Exceptions\OperandTypeException;
use IPP\Student\Exceptions\OperandValueException;
use IPP\Student\Exceptions\SemanticException;

/**
 * Class ControlFlowExecutor
 * 
 * This class is responsible for executing control flow instructions.
 * 
 * Implements the LABEL, JUMP, JUMPIFEQ, JUMPIFNEQ, and EXIT instructions.
 */
class ControlFlowExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "LABEL",
        "JUMP",
        "JUMPIFEQ",
        "JUMPIFNEQ",
        "EXIT"
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
            case "LABEL":
                break;
            case "JUMP":
                $this->executeJump($this->instruction);
                break;
            case "JUMPIFEQ":
            case "JUMPIFNEQ":
                $this->executeJumpIf($this->instruction);
                break;
            case "EXIT":
                $this->executeExit($this->instruction);
                break;
        }
    }

    /**
     * Executes the JUMP instruction.
     *
     * This method retrieves the label from the instruction's first argument and checks if it exists in the labels array.
     * If the label is not found, a SemanticException is thrown.
     * If the label is found, the instruction index is updated to the corresponding label index.
     *
     * @param Instruction $instruction The jump instruction to execute.
     * @throws SemanticException If the label specified in the instruction is not found.
     * @return void
     */
    private function executeJump(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $label = $arg1->getValue();

        if (!isset($this->memory->labels[$label])) 
        {
            throw new SemanticException("Label not found: $label");
        }

        $this->memory->instructionPointer = $this->memory->labels[$label];
    }

    /**
     * Executes the JUMPIFEQ or JUMPIFNEQ instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @throws OperandTypeException If the operand types are not compatible.
     * @return void
     */
    private function executeJumpIf(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();
        $label = $arg1->getValue();

        $symb1 = SymbolHelper::getConstantAndType($arg2, $this->memory->frameLogic);      
        $symb2 = SymbolHelper::getConstantAndType($arg3, $this->memory->frameLogic);

        if (($symb1->getType() === $symb2->getType()) || $symb1->getType() === "nil" || $symb2->getType() === "nil")
        {
            if ($instruction->opcode === "JUMPIFEQ") 
            {
                if ($symb1->getValue() === $symb2->getValue()) 
                {
                    $this->memory->instructionPointer = $this->memory->labels[$label];
                }
            }
            else if ($instruction->opcode === "JUMPIFNEQ") 
            {
                if ($symb1->getValue() !== $symb2->getValue()) 
                {
                    $this->memory->instructionPointer = $this->memory->labels[$label];
                }
            }
        }
        else
        {
            throw new OperandTypeException();
        }
    }

    /**
     * Executes the EXIT instruction.
     * 
     * Exits the program with the specified exit code in range 0-9.
     *
     * @param Instruction $instruction The instruction to execute.
     * @return void
     */
    private function executeExit(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();

        if ($arg1->getType() === "int") 
        {
            $exitCode = (int) $arg1->getValue();
            if ($exitCode < 0 || $exitCode > 9) 
                throw new OperandValueException();

            exit((int) $arg1->getValue());
        }
        else if ($arg1->getType() === "var") 
        {
            $symb = SymbolHelper::getConstant($arg1, "int", $this->memory->frameLogic);
            $exitCode = (int) $symb->getValue();

            if ($exitCode < 0 || $exitCode > 9) 
                throw new OperandValueException();

            exit((int) $symb->getValue());
        }
        else
        {
            throw new OperandTypeException();
        }
    }
}
