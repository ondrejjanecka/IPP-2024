<?php

namespace IPP\Student\Library;

use IPP\Student\Library\Variable;
use IPP\Student\Exceptions\VariableAccessException;

class Frame
{
    private $variables = [];

    public function addVariable(Variable $variable)
    {
        $this->variables[$variable->getName()] = $variable;
    }

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
        return $this->variables[$name];
    }

    public function variableExists($name): bool
    {
        return isset($this->variables[$name]);
    }
}
