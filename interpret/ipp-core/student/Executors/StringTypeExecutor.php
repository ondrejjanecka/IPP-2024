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
use IPP\Student\Helpers\VarHelper;

use IPP\Student\Exceptions\OperandTypeException;
use IPP\Student\Exceptions\StringOperationException;
use IPP\Student\Exceptions\ValueException;

/**
 * Class StringTypeExecutor
 * 
 * This class is responsible for executing string type instructions.
 * 
 * Implements the CONCAT, STRLEN, GETCHAR, SETCHAR, and TYPE instructions.
 */
class StringTypeExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "CONCAT",
        "STRLEN",
        "GETCHAR",
        "SETCHAR",
        "TYPE"
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
            case "CONCAT":
                $this->executeConcat($this->instruction);
                break;
            case "STRLEN":
                $this->executeStrLen($this->instruction);
                break;
            case "GETCHAR":
                $this->executeGetChar($this->instruction);
                break;
            case "SETCHAR":
                $this->executeSetChar($this->instruction);
                break;
            case "TYPE":
                $this->executeType($this->instruction);
                break;
        }
    }

    /**
     * Executes the CONCAT instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeConcat(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->memory->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "string", $this->memory->frameLogic);

        $variable->setValue($symb1->getValue() . $symb2->getValue());
        $variable->setType("string");
    }

    /**
     * Executes the STRLEN instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeStrLen(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "string", $this->memory->frameLogic);

        $variable->setValue(strlen($symb->getValue()));
        $variable->setType("int");
    }

    /**
     * Executes the GETCHAR instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeGetChar(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->memory->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "int", $this->memory->frameLogic);

        $string = $symb1->getValue();
        $index = $symb2->getValue();

        if ($index < 0 || $index >= strlen($string)) 
        {
            throw new StringOperationException();
        }

        $variable->setValue($string[$index]);
        $variable->setType("string");
    }

    /**
     * Executes the SETCHAR instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeSetChar(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));
        if ($variable->getType() !== "string") 
            throw new OperandTypeException();

        $symb1 = SymbolHelper::getConstant($arg2, "int", $this->memory->frameLogic);      
        $symb2 = SymbolHelper::getConstant($arg3, "string", $this->memory->frameLogic);

        $string = $variable->getValue();
        $index = $symb1->getValue();
        if ($symb2->getValue() < 1) 
            throw new StringOperationException();
        $char = $symb2->getValue()[0];

        if ($index < 0 || $index >= strlen($string)) 
        {
            throw new StringOperationException();
        }

        $string[$index] = $char;

        $variable->setValue($string);
        $variable->setType("string");
    }

    /**
     * Executes the TYPE instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeType(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));
        try
        {
            $symb = SymbolHelper::getConstantAndType($arg2, $this->memory->frameLogic);
            $variable->setValue($symb->getType());
        }
        catch (ValueException $e)
        {
            $variable->setValue("nil");
            $variable->setType("nil");
            return;
        }
        $variable->setType("string");
    }
}
