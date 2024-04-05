<?php

namespace IPP\Student\Library;

use IPP\Student\Library\Variable;

class Frame
{
    private $variables = [];

    public function addVariable(Variable $variable)
    {
        $this->variables[$variable->getName()] = $variable;
    }

    public function getVariable($name): Variable
    {
        return $this->variables[$name];
    }

    public function variableExists($name): bool
    {
        return isset($this->variables[$name]);
    }
}
