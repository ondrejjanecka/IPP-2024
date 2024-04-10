<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors;

use IPP\Student\Executors\Interface\Executor;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;

use IPP\Student\Exceptions\InvalidSourceStructureException;

/**
 * Class DebugExecutor
 * 
 * This class is responsible for handling the DPRINT and BREAK instructions.
 * 
 * This is the last executor in the chain of responsibility.
 */
class DebugExecutor implements Executor
{
    /**
     * @var array<string> $opcodes Holds the opcodes that this executor can handle.
     */
    private array $opcodes = [
        "DPRINT",
        "BREAK"
    ];

    public function handleRequest(Instruction $instruction, Memory $memory): void
    {
        if (in_array($instruction->opcode, $this->opcodes)) 
        {
            $this->execute();
        } 
        else 
        {
            throw new InvalidSourceStructureException("Invalid instruction opcode: " . $instruction->opcode);
        }
    }

    public function setNextExecutor(Executor $handler): void
    {
        // This is the last executor in the chain.
    }

    private function execute(): void
    {
        // TODO: Implement execute() method.
    }
}
