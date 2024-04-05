<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;
use IPP\Student\Library\Stack;
use IPP\Student\Library\Variable;
use IPP\Student\Library\Frame;
use IPP\Student\Helpers\EscapeSequenceConvertor as StringConvertor;
use IPP\Core\Settings;

class InstructionExecutor
{
    private $instructions;
    private $globalFrame;
    private $localFrame;
    private $tempFrame;
    private $string;

    public function __construct($instructions)
    {
        $this->instructions = $instructions;
        $this->globalFrame = new Frame();
        $this->localFrame = new Frame();
        $this->tempFrame = new Frame();
        $this->string = "";
    }

    public function executeInstructions(): string
    {
        foreach ($this->instructions as $instruction) 
        {
            $this->executeInstruction($instruction);
        }

        return $this->string;
    }

    private function executeInstruction($instruction)
    {
        switch ($instruction->opcode) 
        {
            case "MOVE":
                $this->executeMove($instruction);
                break;
            // case "CREATEFRAME":
            //     $this->executeCreateFrame($instruction);
            //     break;
            // case "PUSHFRAME":
            //     $this->executePushFrame($instruction);
            //     break;
            // case "POPFRAME":
            //     $this->executePopFrame($instruction);
            //     break;
            case "DEFVAR":
                $this->executeDefVar($instruction);
                break;
            // case "CALL":
            //     $this->executeCall($instruction);
            //     break;
            // case "RETURN":
            //     $this->executeReturn($instruction);
            //     break;
            // case "PUSHS":
            //     $this->executePushS($instruction);
            //     break;
            // case "POPS":
            //     $this->executePopS($instruction);
            //     break;
            // case "ADD":
            //     $this->executeAdd($instruction);
            //     break;
            // case "SUB":
            //     $this->executeSub($instruction);
            //     break;
            // case "MUL":
            //     $this->executeMul($instruction);
            //     break;
            // case "IDIV":
            //     $this->executeIDiv($instruction);
            //     break;
            // case "LT":
            //     $this->executeLT($instruction);
            //     break;
            // case "GT":
            //     $this->executeGT($instruction);
            //     break;
            // case "EQ":
            //     $this->executeEQ($instruction);
            //     break;
            // case "AND":
            //     $this->executeAnd($instruction);
            //     break;
            // case "OR":
            //     $this->executeOr($instruction);
            //     break;
            // case "NOT":
            //     $this->executeNot($instruction);
            //     break;
            // case "INT2CHAR":
            //     $this->executeInt2Char($instruction);
            //     break;
            // case "STRI2INT":
            //     $this->executeStri2Int($instruction);
            //     break;
            // case "READ":
            //     $this->executeRead($instruction);
            //     break;
            case "WRITE":
                $this->executeWrite($instruction);
                break;
            // case "CONCAT":
            //     $this->executeConcat($instruction);
            //     break;
            // case "STRLEN":
            //     $this->executeStrLen($instruction);
            //     break;
            // case "GETCHAR":
            //     $this->executeGetChar($instruction);
            //     break;
            // case "SETCHAR":
            //     $this->executeSetChar($instruction);
            //     break;
            // case "TYPE":
            //     $this->executeType($instruction);
            //     break;
            // case "LABEL":
            //     $this->executeLabel($instruction);
            //     break;
            // case "JUMP":
            //     $this->executeJump($instruction);
            //     break;
            // case "JUMPIFEQ":
            //     $this->executeJumpIfEq($instruction);
            //     break;
            // case "JUMPIFNEQ":
            //     $this->executeJumpIfNEq($instruction);
            //     break;
            // case "EXIT":
            //     $this->executeExit($instruction);
            //     break;
            // case "DPRINT":
            //     $this->executeDPrint($instruction);
            //     break;
            // case "BREAK":
            //     $this->executeBreak($instruction);
            //     break;
            default:
                break;
        }
    }

    private function executeMove($instruction)
    {
        $var = $instruction->args['arg1']['value'];
        $name = VarHelper::getVarName($var);
        $frame = VarHelper::getFrameName($var);
        $symb = $instruction->args['arg2'];

        if ($frame === "GF") 
        {
            if ($this->globalFrame->variableExists($name)) 
            {
                $variable = $this->globalFrame->getVariable($name);
                $variable->setValue($symb);
            }
        }
    }

    private function executeDefVar($instruction)
    {
        $var = $instruction->args['arg1']['value'];

        $variable = new Variable(VarHelper::getVarName($var), null);

        $frame = VarHelper::getFrameName($var);

        if ($frame === "GF") 
        {
            $this->globalFrame->addVariable($variable);
        }
    }

    private function executeWrite($instruction)
    {
        $type = $instruction->args['arg1']['type'];
        $symb = $instruction->args['arg1'];

        if ($type === "var") 
        {
            $var = $symb['value'];
            $name = VarHelper::getVarName($var);
            $frame = VarHelper::getFrameName($var);

            if ($frame === "GF") 
            {
                $variable = $this->globalFrame->getVariable($name);
                $this->string .= StringConvertor::convert($variable->getValue());
            }
        }
        else if ($type === "int" || $type === "bool" || $type === "string") 
        {
            $this->string .= StringConvertor::convert($symb['value']);
        }
    }
}
