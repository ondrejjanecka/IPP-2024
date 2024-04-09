<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Helpers\VarHelper;
use IPP\Student\Library\Stack;
use IPP\Student\Library\Variable;
use IPP\Student\Library\Constant;
use IPP\Student\Library\Frame;
use IPP\Student\Helpers\EscapeSequenceConvertor as StringConvertor;
use IPP\Student\Helpers\SymbolHelper;
use IPP\Student\Library\Instruction;
use IPP\Student\Library\FrameLogic;

use IPP\Student\Exceptions\OperandTypeException;
use IPP\Student\Exceptions\OperandValueException;
use IPP\Student\Exceptions\StringOperationException;
use IPP\Student\Exceptions\FrameAccessException;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Student\Exceptions\InvalidSourceStructureException;
use IPP\Student\Exceptions\ValueException;

class InstructionExecutor
{
    /**
     * @var array<Instruction> Holds the instructions to execute.
     */
    private array $instructions;

    private Frame $globalFrame;

    private FrameLogic $frameLogic; 
    // private $localFrame;
    // private $tempFrame;
    // private Stack $frameStack;
    
    private InputReader $input;
    private OutputWriter $stdout;
    // private $stderr;

    // public function __construct($instructions, $input, $stdout, $stderr)
    /**
     * @param array<Instruction> $instructions
     * @param InputReader $input
     * @param OutputWriter $stdout
     */
    public function __construct(array $instructions, InputReader $input, OutputWriter $stdout)
    {
        $this->instructions = $instructions;
        $this->globalFrame = new Frame();
        $this->frameLogic = new FrameLogic();
        $this->input = $input;
        $this->stdout = $stdout;
        // $this->stderr = $stderr;
    }

    public function executeInstructions() : void
    {
        foreach ($this->instructions as $instruction) 
        {
            $this->executeInstruction($instruction);
        }
    }

    private function executeInstruction(Instruction $instruction) : void
    {
        switch ($instruction->opcode) 
        {
            case "MOVE":
                $this->executeMove($instruction);
                break;
            case "CREATEFRAME":
                $this->executeCreateFrame();
                break;
            case "PUSHFRAME":
                $this->executePushFrame();
                break;
            case "POPFRAME":
                $this->executePopFrame();
                break;
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
            case "INT2CHAR":
                $this->executeInt2Char($instruction);
                break;
            case "STRI2INT":
                $this->executeStri2Int($instruction);
                break;
            case "READ":
                $this->executeRead($instruction);
                break;
            case "WRITE":
                $this->executeWrite($instruction);
                break;
            case "CONCAT":
                $this->executeConcat($instruction);
                break;
            case "STRLEN":
                $this->executeStrLen($instruction);
                break;
            case "GETCHAR":
                $this->executeGetChar($instruction);
                break;
            case "SETCHAR":
                $this->executeSetChar($instruction);
                break;
            case "TYPE":
                $this->executeType($instruction);
                break;
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
            case "DPRINT":
                break;
            case "BREAK":
                break;
            default:
                break;
        }
    }

    private function executeCreateFrame() : void
    {
        $this->frameLogic->createFrame();
    }

    private function executePushFrame() : void
    {
        $this->frameLogic->pushTempFrame();
    }

    private function executePopFrame() : void
    {
        $this->frameLogic->popFrame();
    }

    private function executeMove(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame1 = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));

        $variable = $frame1->getVariable(VarHelper::getVarName($arg1->getValue()));

        if ($arg2->getType() === "var") 
        {
            $frame2 = $this->frameLogic->getFrame(VarHelper::getFrameName($arg2->getValue()));
            $symb = $frame2->getVariable(VarHelper::getVarName($arg2->getValue()));
        }
        else 
        {
            $symb = new Constant($arg2->getType(), $arg2->getValue());
        }
        
        $variable->setValue($symb->getValue());
        $variable->setType($symb->getType());
    }

    private function executeDefVar(Instruction $instruction) : void
    {
        $var = $instruction->getFirstArg();

        $variable = new Variable(VarHelper::getVarName($var->getValue()), VarHelper::getFrameName($var->getValue()));
 
        $frame = $this->frameLogic->getFrame($variable->getFrame());
        $frame->addVariable($variable);
    }

    private function executeWrite(Instruction $instruction) : void
    {
        $symb = $instruction->getFirstArg();
        $type = $symb->getType();

        if ($type === "var") 
        {
            $name = VarHelper::getVarName($symb->getValue());
            $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($symb->getValue()));

            $variable = $frame->getVariable($name);

            if ($variable->getType() === "nil") 
            {
                $this->stdout->writeString("");
                return;
            }
            else if ($variable->getType() === "bool")
            {
                $this->stdout->writeString($variable->getValue() ? "true" : "false");
                return;
            }
            else 
                $this->stdout->writeString(StringConvertor::convert($variable->getValue()));
        }
        else if ($type === "int" || $type === "string") 
        {
            $this->stdout->writeString(StringConvertor::convert($symb->getValue()));
        }
        else if ($type === "bool") 
        {
            $this->stdout->writeString($symb->getValue() ? "true" : "false");
        }
        else if ($type === "nil") 
        {
            $this->stdout->writeString("");
        }
        else
        {
            throw new StringOperationException();
        }
    }

    private function executeExit(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();

        if ($arg1->getType() === "int") 
        {
            exit((int) $arg1->getValue());
        }
        else if ($arg1->getType() === "var") 
        {
            $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
            $symb = SymbolHelper::getConstant($arg1, "int", $this->frameLogic);
            exit((int) $symb->getValue());
        }
        else
        {
            throw new OperandTypeException();
        }
    }

    private function executeArithmeticOp(Instruction $instruction) : void
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

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
                // Division by zero exception
                throw new OperandValueException();
            }
            $variable->setValue($symb1->getValue() / $symb2->getValue());
        }
        $variable->setType("int");
    }

    private function executeRelationOp(Instruction $instruction) : void
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstantAndType($arg2, $this->frameLogic);
        $symb2 = SymbolHelper::getConstantAndType($arg3, $this->frameLogic);

        if ($symb1->getType() !== $symb2->getType() && $symb1->getType() !== "nil")
        {
            throw new OperandTypeException();
        }

        if ($operation === "LT") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() < $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) < 0);
            else
                throw new OperandTypeException("Invalid operand type for LT operation");
        }

        else if ($operation === "GT") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() > $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) > 0);
            else
                throw new OperandTypeException("Invalid operand type for GT operation");
        }

        else if ($operation === "EQ") 
        {
            if ($symb1->getType() === "int" || $symb1->getType() === "bool")
                $variable->setValue($symb1->getValue() == $symb2->getValue());
            else if ($symb1->getType() === "string")
                $variable->setValue(strcmp($symb1->getValue(), $symb2->getValue()) == 0);
            else if ($symb1->getType() === "nil") 
                $variable->setValue($symb2->getType() === "nil");
            else
                throw new OperandTypeException("Invalid operand type for EQ operation");
        }

        $variable->setType("bool");
    }

    private function executeAndOr(Instruction $instruction) : void
    {
        $operation = $instruction->opcode;
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "bool", $this->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "bool", $this->frameLogic);

        if ($operation === "AND") 
        {
            $variable->setValue($symb1->getValue() && $symb2->getValue());
        }
        else if ($operation === "OR") 
        {
            $variable->setValue($symb1->getValue() || $symb2->getValue());
        }

        $variable->setType("bool");
    }

    private function executeNot(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "bool", $this->frameLogic);

        $variable->setValue(!$symb->getValue());
        $variable->setType("bool");
    }

    private function executeConcat(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "string", $this->frameLogic);

        $variable->setValue($symb1->getValue() . $symb2->getValue());
        $variable->setType("string");
    }

    private function executeStrLen(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "string", $this->frameLogic);

        $variable->setValue(strlen($symb->getValue()));
        $variable->setType("int");
    }

    private function executeGetChar(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "int", $this->frameLogic);

        $string = $symb1->getValue();
        $index = $symb2->getValue();

        if ($index < 0 || $index >= strlen($string)) 
        {
            throw new StringOperationException();
        }

        $variable->setValue($string[$index]);
        $variable->setType("string");
    }

    private function executeSetChar(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));
        if ($variable->getType() !== "string") 
            throw new OperandTypeException();

        $symb1 = SymbolHelper::getConstant($arg2, "int", $this->frameLogic);      
        $symb2 = SymbolHelper::getConstant($arg3, "string", $this->frameLogic);

        $string = $variable->getValue();
        $index = $symb1->getValue();
        if ($symb2->getValue() < 1) 
            throw new StringOperationException();
        $char = $symb2->getValue()[0];

        if ($index < 0 || $index >= strlen($string)) 
        {
            throw new StringOperationException();
        }

        $string[$index] = $char;

        $variable->setValue($string);
        $variable->setType("string");
    }

    private function executeType(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));
        try
        {
            $symb = SymbolHelper::getConstantAndType($arg2, $this->frameLogic);
            $variable->setValue($symb->getType());
        }
        catch (ValueException $e)
        {
            $variable->setValue("nil");
            $variable->setType("nil");
            return;
        }
        $variable->setType("string");
    }

    private function executeInt2Char(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb = SymbolHelper::getConstant($arg2, "int", $this->frameLogic);

        if ($symb->getValue() < 0 || $symb->getValue() > 1114112) 
        {
            throw new StringOperationException();
        }

        $variable->setValue(chr($symb->getValue()));
        $variable->setType("string");
    }

    private function executeStri2Int(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();
        $arg3 = $instruction->getThirdArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $symb1 = SymbolHelper::getConstant($arg2, "string", $this->frameLogic);
        $symb2 = SymbolHelper::getConstant($arg3, "int", $this->frameLogic);

        $string = $symb1->getValue();
        $index = $symb2->getValue();

        if ($index < 0 || $index >= strlen($string)) 
        {
            throw new StringOperationException();
        }

        $variable->setValue(ord($string[$index]));
        $variable->setType("int");
    }

    private function executeRead(Instruction $instruction) : void
    {
        $arg1 = $instruction->getFirstArg();
        $arg2 = $instruction->getSecondArg();

        $frame = $this->frameLogic->getFrame(VarHelper::getFrameName($arg1->getValue()));
        $variable = $frame->getVariable(VarHelper::getVarName($arg1->getValue()));

        $type = $arg2->getValue();

        if ($type === "bool")
        {
            $input = $this->input->readBool();

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
            $input = $this->input->readInt();

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
            $input = $this->input->readString();

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
