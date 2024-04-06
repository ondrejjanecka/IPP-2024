<?php
/**
 * IPP - PHP Project Library
 * @author Ondřej Janečka
 */

namespace IPP\Student\Library;

/**
 * Represents an instruction in a program.
 */
class Instruction
{
    /**
     * @var int The order of the instruction.
     */
    public $order;

    /**
     * @var string The opcode of the instruction.
     */
    public $opcode;

    /**
     * @var array The arguments of the instruction.
     */
    private $args = [];

    /**
     * Constructs a new Instruction object.
     *
     * @param int $order The order of the instruction.
     * @param string $opcode The opcode of the instruction.
     * @param array $args The arguments of the instruction.
     */
    public function __construct($order, $opcode, $args)
    {
        $this->order = $order;
        $this->opcode = $opcode;
        $this->args = $args;
    }

    /**
     * Adds a new argument to the instruction.
     *
     * @param string $type The type of the argument.
     * @param mixed $value The value of the argument.
     */
    public function addArg($type, $value)
    {
        $this->args[] = [
            'type' => $type,
            'value' => $value
        ];
    }

    public function getFirstArg()
    {
        return $this->args['arg1'];
    }

    public function getSecondArg()
    {
        return $this->args['arg2'];
    }

    public function getThirdArg()
    {
        return $this->args['arg3'];
    }
}