<?php

namespace IPP\Student\Library;

use IPP\Student\Library\Variable;
use IPP\Student\Exceptions\VariableAccessException;

/**
 * Represents a frame in the interpreter's memory.
 */
class Frame
{
    /**
     * @var array Holds the variables in the frame.
     */
    private $variables = [];

    /**
     * Adds a variable to the frame.
     *
     * @param Variable $variable The variable to add.
     */
    public function addVariable(Variable $variable)
    {
        if (!$this->variableExists($variable->getName()))
            $this->variables[$variable->getName()] = $variable;
    }

    /**
     * Retrieves a variable from the frame.
     *
     * @param string $name The name of the variable.
     * @return Variable The retrieved variable.
     * @throws VariableAccessException If the variable does not exist in this frame.
     */
    public function getVariable($name): Variable
    {
        if ($this->variableExists($name)) 
        {
            return $this->variables[$name];
        } 
        else 
        {
            throw new VariableAccessException("Variable $name does not exist in this frame");
        }
    }

    /**
     * Checks if a variable exists in the frame.
     *
     * @param string $name The name of the variable.
     * @return bool true if the variable exists, false otherwise.
     */
    public function variableExists($name): bool
    {
        return isset($this->variables[$name]);
    }
}
