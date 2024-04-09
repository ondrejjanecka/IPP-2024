<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

use IPP\Student\Library\Instruction;
use IPP\Student\Library\Argument;
use IPP\Student\Helpers\OpcodeHelper;
use DOMDocument;
use DOMElement;
use IPP\Student\Exceptions\InvalidSourceStructureException;

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
        if (!$rootElement instanceof DOMElement)
            throw new InvalidSourceStructureException("Invalid argument structure");
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
            throw new InvalidSourceStructureException("Root element must be 'program'");
        }
    }

    private function checkRoot() : void
    {
        $rootElement = $this->XmlPath->documentElement;
        if (!$rootElement instanceof DOMElement)
            throw new InvalidSourceStructureException("Invalid argument structure");
        
        if ($rootElement->nodeName != "program")
        {
            throw new InvalidSourceStructureException("Root element must be 'program'");
        }
        if ($rootElement->getAttribute("language") != "IPPcode24")
        {
            throw new InvalidSourceStructureException("Root element must have attribute 'language' with value 'IPPcode24'");
        }
        
        // TODO - zkontrolovat výskyt dalších atributů
    }

    private function checkInstructions() : void
    {
        $rootElement = $this->XmlPath->documentElement;
        if (!$rootElement instanceof DOMElement)
            throw new InvalidSourceStructureException("Invalid argument structure");

        $instructions = $rootElement->childNodes;
        foreach ($instructions as $instruction)
        {
            if ($instruction->nodeType != XML_ELEMENT_NODE)
            {
                continue;
            }
            if ($instruction->nodeName != "instruction")
            {
                throw new InvalidSourceStructureException("Only 'instruction' elements are allowed inside 'program'");
            }
            foreach ($instruction->attributes as $attribute) 
            {
                $attributeName = $attribute->nodeName;
                if ($attributeName != 'order' && $attributeName != 'opcode') 
                {
                    throw new InvalidSourceStructureException("Instruction must not have any additional attributes besides 'order' and 'opcode'");
                }
            }
            if ($instruction instanceof DOMElement)
            {
                if (!$instruction->hasAttribute("order"))
                {
                    throw new InvalidSourceStructureException("Instruction must have attribute 'order'");
                }
                if ($instruction->hasAttribute("opcode"))
                {
                    $opcode = $instruction->getAttribute("opcode");
                    if (!OpcodeHelper::isOpcodeAllowed($opcode))
                    {
                        throw new InvalidSourceStructureException("Invalid opcode '$opcode'");
                    }
                }
                else
                {
                    throw new InvalidSourceStructureException("Instruction must have attribute 'opcode'");
                }
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
            $labels = [];
            $order = (int)$instruction->getAttribute("order");
            
            if (in_array($order, $orders)) 
            {
                throw new InvalidSourceStructureException("Duplicate order of instruction");
            }
            else if ($order < 1) 
            {
                throw new InvalidSourceStructureException("Invalid order of instruction");
            }
            $orders[] = $order;

            $opcode = (string)strtoupper($instruction->getAttribute("opcode"));
            $args = [];

            foreach ($instruction->childNodes as $arg) 
            {
                if ($arg instanceof DOMElement)
                {
                    if ($arg->nodeType == XML_ELEMENT_NODE) 
                    {
                        $label = (string)$arg->nodeName;
                        $type = (string)$arg->getAttribute("type");

                        if (in_array($label, $labels) || ($label !== "arg1" && $label !== "arg2" && $label !== "arg3"))
                        {
                            throw new InvalidSourceStructureException("Duplicate argument label '$label'");
                        }

                        $value = trim((string)$arg->nodeValue);
                        $args[$label] = new Argument($type, $value);
                        $labels[] = $label;
                    }
                }
            }

            if (!OpcodeHelper::checkArgCount($opcode, count($args)))
            {
                throw new InvalidSourceStructureException("Invalid number of arguments for opcode '$opcode'");
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
