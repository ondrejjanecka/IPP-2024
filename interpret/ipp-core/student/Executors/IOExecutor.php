<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors;

use IPP\Student\Executors\Interface\Executor;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;

use IPP\Student\Helpers\EscapeSequenceConvertor as StringConvertor;
use IPP\Student\Helpers\VarHelper;

use IPP\Student\Exceptions\InvalidSourceStructureException;
use IPP\Student\Exceptions\StringOperationException;

/**
 * Class IOExecutor
 * 
 * This class is responsible for executing IO instructions.
 * 
 * Implements the READ and WRITE instructions.
 */
class IOExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "READ",
        "WRITE"
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
            case "READ":
                $this->executeRead($this->instruction);
                break;
            case "WRITE":
                $this->executeWrite($this->instruction);
                break;
        }
    }

    /**
     * Executes the WRITE instruction.
     *
     * @param Instruction $instruction The instruction to execute.
     * @return void
     */
    private function executeWrite(Instruction $instruction) : void
    {
        $symb = $instruction->getFirstArg();
        $type = $symb->getType();

        if ($type === "var") 
        {
            $name = VarHelper::getVarName($symb->getValue());
            $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($symb->getValue()));

            $variable = $frame->getVariable($name);

            if ($variable->getType() === "nil") 
            {
                $this->memory->stdout->writeString("");
                return;
            }
            else if ($variable->getType() === "bool")
            {
                $this->memory->stdout->writeString($variable->getValue() ? "true" : "false");
                return;
            }
            else 
                $this->memory->stdout->writeString(StringConvertor::convert($variable->getValue()));
        }
        else if ($type === "int" || $type === "string") 
        {
            $this->memory->stdout->writeString(StringConvertor::convert($symb->getValue()));
        }
        else if ($type === "bool") 
        {
            $this->memory->stdout->writeString($symb->getValue() ? "true" : "false");
        }
        else if ($type === "nil") 
        {
            $this->memory->stdout->writeString("");
        }
        else
        {
            throw new StringOperationException();
        }
    }

    /**
     * Executes the READ instruction.
     *
     * @param Instruction $instruction The instruction to be executed.
     * @return void
     */
    private function executeRead(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->memory->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $type = $arg2->getValue();

        if ($type === "bool")
        {
            $input = $this->memory->input->readBool();

            if ($input === null) 
            {
                $variable->setValue("nil");
                $variable->setType("nil");
                return;
            }

            $variable->setValue($input);
            $variable->setType("bool");
        }
        else if ($type === "int")
        {
            $input = $this->memory->input->readInt();

            if ($input === null) 
            {
                $variable->setValue("nil");
                $variable->setType("nil");
                return;
            }

            $variable->setValue($input);
            $variable->setType("int");
        }
        else if ($type === "string")
        {
            $input = $this->memory->input->readString();

            if ($input === null) 
            {
                $variable->setValue("nil");
                $variable->setType("nil");
                return;
            }

            $variable->setValue($input);
            $variable->setType("string");
        }
        else
        {
            throw new InvalidSourceStructureException();
        }
    }
}
