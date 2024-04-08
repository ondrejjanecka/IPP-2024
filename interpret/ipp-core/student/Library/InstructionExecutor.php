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
use IPP\Student\Helpers\SymbolHelper;

use IPP\Student\Exceptions\VariableAccessException;
use IPP\Student\Exceptions\OperandTypeException;
use IPP\Student\Exceptions\OperandValueException;


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
            case "ADD":
                $this->executeArithmeticOp($instruction);
                break;
            case "SUB":
                $this->executeArithmeticOp($instruction);
                break;
            case "MUL":
                $this->executeArithmeticOp($instruction);
                break;
            case "IDIV":
                $this->executeArithmeticOp($instruction);
                break;
            case "LT":
                $this->executeRelationOp($instruction);
                break;
            case "GT":
                $this->executeRelationOp($instruction);
                break;
            case "EQ":
                $this->executeRelationOp($instruction);
                break;
            case "AND":
                $this->executeAndOr($instruction);
                break;
            case "OR":
                $this->executeAndOr($instruction);
                break;
            case "NOT":
                $this->executeNot($instruction);
                break;
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
            case "CONCAT":
                $this->executeConcat($instruction);
                break;
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

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));

        if ($arg2->getType() === "var") 
        {
            $symb = $this->globalFrame->getVariable(VarHelper::getVarName($arg2->getValue()));
        }
        else 
        {
            $symb = new Constant($arg2->getType(), $arg2->getValue());
        }
        
        if ($variable->getFrame() === "GF") 
        {
            $variable->setValue($symb->getValue());
        }
    }

    private function executeDefVar($instruction)
    {
        $var = $instruction->getFirstArg();

        $variable = new Variable(VarHelper::getVarName($var->getValue()), VarHelper::getFrameName($var->getValue()));
 
        if ($variable->getFrame() === "GF") 
        {
            $this->globalFrame->addVariable($variable); 
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
                $variable = $this->globalFrame->getVariable($name);
                exit((int) $variable->getValue());
            }
        }
    }

    private function executeArithmeticOp($instruction)
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "int", $this->globalFrame);
        $symb2 = SymbolHelper::getConstant($arg3, "int", $this->globalFrame);

        if ($operation === "ADD") 
        {
            $variable->setValue($symb1->getValue() + $symb2->getValue());
        }
        elseif ($operation === "SUB") 
        {
            $variable->setValue($symb1->getValue() - $symb2->getValue());
        }
        elseif ($operation === "MUL") 
        {
            $variable->setValue($symb1->getValue() * $symb2->getValue());
        }
        elseif ($operation === "IDIV") 
        {
            if ($symb2->getValue() === 0) 
            {
                // Division by zero exception
                throw new OperandValueException();
            }
            $variable->setValue($symb1->getValue() / $symb2->getValue());
        }
    }

    private function executeRelationOp($instruction)
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));
    }

    private function executeAndOr($instruction)
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "bool", $this->globalFrame);
        $symb2 = SymbolHelper::getConstant($arg3, "bool", $this->globalFrame);

        if ($operation === "AND") 
        {
            $variable->setValue($symb1->getValue() && $symb2->getValue());
        }
        elseif ($operation === "OR") 
        {
            $variable->setValue($symb1->getValue() || $symb2->getValue());
        }
    }

    private function executeNot($instruction)
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "bool", $this->globalFrame);

        $variable->setValue(!$symb->getValue());
    }

    private function executeConcat($instruction)
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $variable = $this->globalFrame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->globalFrame);
        $symb2 = SymbolHelper::getConstant($arg3, "string", $this->globalFrame);

        $variable->setValue($symb1->getValue() . $symb2->getValue());
    }
}   
