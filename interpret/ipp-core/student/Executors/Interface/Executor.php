<?php
/**
 * IPP - PHP Project Executors
 * @author Ondřej Janečka
 */

namespace IPP\Student\Executors\Interface;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Memory;

/**
 * Interface Executor
 * 
 * This interface defines the contract for an executor that handles requests.
 */
interface Executor
{
    /**
     * Handles the given instruction using the provided memory.
     * 
     * @param Instruction $instruction The instruction to be handled.
     * @param Memory $memory The interpret memory object to be used for execution.
     * @return void
     */
    public function handleRequest(Instruction $instruction, Memory $memory): void;
    
    /**
     * Sets the next executor in the chain of responsibility.
     * 
     * @param Executor $handler The next executor in the chain.
     * @return void
     */
    public function setNextExecutor(Executor $handler): void;
}
