<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors;

use IPP\Student\Executors\Interface\Executor;

use IPP\Student\Helpers\SymbolHelper;
use IPP\Student\Helpers\VarHelper;

use IPP\Student\Library\FrameLogic;
use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;

use IPP\Student\Exceptions\OperandValueException;

/**
 * Class ArithmeticExecutor
 * 
 * This class is responsible for executing arithmetic operations.
 * 
 * Implements the ADD, SUB, MUL, and IDIV instructions.
 */
class ArithmeticExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "ADD",
        "SUB",
        "MUL",
        "IDIV"
    ];

    private Executor $nextExecutor;
    private Instruction $instruction;
    private FrameLogic $frameLogic;

    public function handleRequest(Instruction $instruction, Memory $memory): void
    {
        if (in_array($instruction->opcode, $this->opcodes)) 
        {
            $this->instruction = $instruction;
            $this->frameLogic = $memory->frameLogic;
            $this->execute();
        } 
        else 
            $this->nextExecutor->handleRequest($instruction, $memory);
    }

    public function setNextExecutor(Executor $handler): void
    {
        $this->nextExecutor = $handler;
    }

    /**
     * Executes arithmetic operations based on the given instruction.
     *
     * @return void
     */
    private function execute(): void
    {
        $operation = $this->instruction->opcode;
        $arg1 = $this->instruction->getFirstArg();
        $arg2 = $this->instruction->getSecondArg();
        $arg3 = $this->instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "int", $this->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "int", $this->frameLogic);

        if ($operation === "ADD") 
        {
            $variable->setValue($symb1->getValue() + $symb2->getValue());
        }
        else if ($operation === "SUB") 
        {
            $variable->setValue($symb1->getValue() - $symb2->getValue());
        }
        else if ($operation === "MUL") 
        {
            $variable->setValue($symb1->getValue() * $symb2->getValue());
        }
        else if ($operation === "IDIV") 
        {
            if ($symb2->getValue() === 0) 
            {
                throw new OperandValueException();
            }
            $variable->setValue($symb1->getValue() / $symb2->getValue());
        }
        $variable->setType("int");
    }
}
