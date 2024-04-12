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

/**
 * Class RelBoolConvertExecutor
 * 
 * This class is responsible for executing relational and boolean conversion instructions.
 * 
 * Implements the LT, GT, EQ, AND, OR, NOT, INT2CHAR, and STRI2INT instructions.
 */
class RelBoolConvertExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "LT",
        "GT",
        "EQ",
        "AND",
        "OR",
        "NOT",
        "INT2CHAR",
        "STRI2INT"
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
            case "LT":
            case "GT":
            case "EQ":
                $this->executeRelationOp($this->instruction);
                break;
            case "AND":
            case "OR":
                $this->executeAndOr($this->instruction);
                break;
            case "NOT":
                $this->executeNot($this->instruction);
                break;
            case "INT2CHAR":
                $this->executeInt2Char($this->instruction);
                break;
            case "STRI2INT":
                $this->executeStri2Int($this->instruction);
                break;
        }
    }

    /**
     * Executes a relation operation based on the given instruction.
     * 
     * Implements LT, GT, and EQ instructions.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @throws OperandTypeException If the operand types are invalid for the operation.
     * @return void
     */
    private function executeRelationOp(Instruction $instruction) : void
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstantAndType($arg2, $this->memory->frameLogic);
        $symb2 = SymbolHelper::getConstantAndType($arg3, $this->memory->frameLogic); //tady

        if ($symb1->getType() !== $symb2->getType() && $symb1->getType() !== "nil")
        {
            throw new OperandTypeException();
        }

        if ($operation === "LT") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() < $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) < 0);
            else
                throw new OperandTypeException("Invalid operand type for LT operation");
        }

        else if ($operation === "GT") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() > $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) > 0);
            else
                throw new OperandTypeException("Invalid operand type for GT operation");
        }

        else if ($operation === "EQ") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() == $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) == 0);
            else if ($symb1->getType() === "nil") 
                $variable->setValue($symb2->getType() === "nil");
            else
                throw new OperandTypeException("Invalid operand type for EQ operation");
        }

        $variable->setType("bool");
    }

    /**
     * Executes the AND or OR operation based on the given instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeAndOr(Instruction $instruction) : void
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "bool", $this->memory->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "bool", $this->memory->frameLogic);

        if ($operation === "AND") 
        {
            $variable->setValue($symb1->getValue() && $symb2->getValue());
        }
        else if ($operation === "OR") 
        {
            $variable->setValue($symb1->getValue() || $symb2->getValue());
        }

        $variable->setType("bool");
    }

    /**
     * Executes the NOT operation based on the given instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeNot(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "bool", $this->memory->frameLogic);

        $variable->setValue(!$symb->getValue());
        $variable->setType("bool");
    }

    /**
     * Executes the INT2CHAR instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeInt2Char(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "int", $this->memory->frameLogic);

        if ($symb->getValue() < 0 || $symb->getValue() > 1114112) 
        {
            throw new StringOperationException();
        }

        $variable->setValue(chr($symb->getValue()));
        $variable->setType("string");
    }

    /**
     * Executes the STRI2INT instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeStri2Int(Instruction $instruction) : void
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

        $variable->setValue(ord($string[$index]));
        $variable->setType("int");
    }
}
