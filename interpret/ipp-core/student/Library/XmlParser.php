<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Library\CustomError as ErrorExit;
use IPP\Core\ReturnCode;
use IPP\Student\Library\Instruction;
use IPP\Student\Library\Argument;
use IPP\Student\Helpers\OpcodeHelper;
use DOMDocument;
use DOMElement;

class XmlParser
{
    private DOMDocument $XmlPath;

    public function __construct(DOMDocument $XmlPath)
    {
        $this->XmlPath = $XmlPath;
    }

    public function parseXml() : void
    {
        $this->checkIntegrity();
        $this->checkRoot();
        $this->checkInstructions();
    }

    private function checkIntegrity() : void
    {
            $rootElement = $this->XmlPath->documentElement;

        // try
        // {
        //     $rootElement = $this->XmlPath->documentElement;
        // }
        // catch(\Exception $e)
        // {
        //     ErrorExit::printErrorExit($e->getMessage(), ReturnCode::INPUT_FILE_ERROR);
        // }
    
        if ($rootElement->nodeName != "program")
        {
            ErrorExit::printErrorExit("Root element must be 'program'", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }
    }

    private function checkRoot() : void
    {
        $rootElement = $this->XmlPath->documentElement;
        if ($rootElement->nodeName != "program")
        {
            ErrorExit::printErrorExit("Root element must be 'program'", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }
        if ($rootElement->getAttribute("language") != "IPPcode24")
        {
            ErrorExit::printErrorExit("Root element must have attribute 'language' with value 'IPPcode24'", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }
        
        // TODO - zkontrolovat výskyt dalších atributů
    }

    private function checkInstructions() : void
    {
        $instructions = $this->XmlPath->documentElement->childNodes;
        foreach ($instructions as $instruction)
        {
            if ($instruction->nodeType != XML_ELEMENT_NODE)
            {
                continue;
            }
            if ($instruction->nodeName != "instruction")
            {
                ErrorExit::printErrorExit("Only 'instruction' elements are allowed inside 'program'", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            foreach ($instruction->attributes as $attribute) 
            {
                $attributeName = $attribute->nodeName;
                if ($attributeName != 'order' && $attributeName != 'opcode') 
                {
                    ErrorExit::printErrorExit("Instruction must not have any additional attributes besides 'order' and 'opcode'", ReturnCode::INVALID_SOURCE_STRUCTURE);
                }
            }
            if (!$instruction->hasAttribute("order"))
            {
                ErrorExit::printErrorExit("Instruction must have attribute 'order'", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            if ($instruction->hasAttribute("opcode"))
            {
                $opcode = $instruction->getAttribute("opcode");
                if (!OpcodeHelper::isOpcodeAllowed($opcode))
                {
                    ErrorExit::printErrorExit("Invalid opcode '$opcode'", ReturnCode::INVALID_SOURCE_STRUCTURE);
                }
            }
            else
            {
                ErrorExit::printErrorExit("Instruction must have attribute 'opcode'", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
        }
    }

    /**
     * Retrieves the instructions from the XML file.
     *
     * @return array<Instruction> The array of Instruction objects.
     */
    public function getInstructions() : array
    {
        $instructions = [];
        $orders = [];

        $instructionNodes = $this->XmlPath->getElementsByTagName("instruction");

        foreach ($instructionNodes as $instruction)
        {
            $order = (int)$instruction->getAttribute("order");
            
            if (in_array($order, $orders)) 
            {
                ErrorExit::printErrorExit("Duplicate order of instruction", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            $orders[] = $order;

            $opcode = (string)strtoupper($instruction->getAttribute("opcode"));
            $args = [];

            foreach ($instruction->childNodes as $arg) 
            {
                if ($arg->nodeType == XML_ELEMENT_NODE) 
                {
                    $label = (string)$arg->nodeName;
                    $type = (string)$arg->getAttribute("type");
                    $value = trim((string)$arg->nodeValue);
                    $args[$label] = new Argument($type, $value);
                }
            }

            if (!OpcodeHelper::checkArgCount($opcode, count($args)))
            {
                ErrorExit::printErrorExit("Invalid number of arguments for opcode '$opcode'", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }

            $instructions[] = new Instruction($order, $opcode, $args);
        }

        return $this->sortInstructions($instructions);
    }

    /**
     * Sorts the instructions by their order.
     *
     * @param array<Instruction> $instructions The array of Instruction objects.
     * @return array<Instruction> The sorted array of Instruction objects.
     */
    private function sortInstructions(array $instructions) : array
    {
        usort($instructions, function($a, $b) {
            return $a->order - $b->order;
        });
    
        return $instructions;
    }
}
