<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;
use IPP\Student\Library\Stack;
use IPP\Student\Library\Argument;
use IPP\Student\Library\Variable;
use IPP\Student\Library\Constant;
use IPP\Student\Library\Frame;
use IPP\Student\Helpers\EscapeSequenceConvertor as StringConvertor;
use IPP\Student\Helpers\TypeHelper;
use IPP\Student\Exceptions\VariableAccessException;


class InstructionExecutor
{
    private $instructions;
    private $globalFrame;
    private $localFrame;
    private $tempFrame;
    private $input;
    private $stdout;
    private $stderr;

    public function __construct($instructions, $input, $stdout, $stderr)
    {
        $this->instructions = $instructions;
        $this->globalFrame = new Frame();
        $this->localFrame = new Frame();
        $this->tempFrame = new Frame();
        $this->input = $input;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    public function executeInstructions()
    {
        // print_r($this->instructions);

        foreach ($this->instructions as $instruction) 
        {
            // print_r($instruction);
            $this->executeInstruction($instruction);
        }
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
            case "EXIT":
                $this->executeExit($instruction);
                break;
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
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        if ($this->globalFrame->variableExists(VarHelper::getVarName($arg1->getValue())) && VarHelper::getFrameName($arg1->getValue()) == "GF")
        {
            $var = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));
        }
        else
        {
            throw new VariableAccessException();
        }

        if ($arg2->getType() === "var") 
        {

            if($this->globalFrame->variableExists(VarHelper::getVarName($arg2->getValue())))
            {
                $symb = $this->globalFrame->getVariable(VarHelper::getVarName($arg2->getValue()));
            }
            else
            {
                throw new VariableAccessException();
            }

        }
        else 
        {
            $symb = new Constant($arg2->getType(), $arg2->getValue());
        }
        
        if ($var->getFrame() === "GF") 
        {
            $var->setValue($symb->getValue());
        }
    }

    private function executeDefVar($instruction)
    {
        $var = $instruction->getFirstArg();

        $variable = new Variable(VarHelper::getVarName($var->getValue()), VarHelper::getFrameName($var->getValue()));
 
        if ($variable->getFrame() === "GF") 
        {
            if (!$this->globalFrame->variableExists($variable->getName())) 
            {
                $this->globalFrame->addVariable($variable);
            }
        }
    }

    private function executeWrite($instruction)
    {
        $symb = $instruction->getFirstArg();
        $type = $symb->getType();

        if ($type === "var") 
        {
            $name = VarHelper::getVarName($symb->getValue());
            $frame = VarHelper::getFrameName($symb->getValue());

            if ($frame === "GF") 
            {
                $variable = $this->globalFrame->getVariable($name);
                $this->stdout->writeString(StringConvertor::convert($variable->getValue()));
            }
        }
        else if ($type === "int" || $type === "bool" || $type === "string") 
        {
            $this->stdout->writeString(StringConvertor::convert($symb->getValue()));
        }
    }

    private function executeExit($instruction)
    {
        $symb = $instruction->getFirstArg();

        if ($symb->gettype() === "int") 
        {
            exit((int) $symb->getValue());
        }
        elseif ($symb->gettype() === "var") 
        {
            $name = VarHelper::getVarName($symb->getValue());

            if (VarHelper::getFrameName($symb->getValue()) === "GF") 
            {
                if ($this->globalFrame->variableExists($name)) 
                {
                    $variable = $this->globalFrame->getVariable($name);
                    exit((int) $variable->getValue());
                }
            }
        }
    }
}   
