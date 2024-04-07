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
     * @var Argument[] The arguments of the instruction.
     */
    private $args = [];

    /**
     * Constructs a new Instruction object.
     *
     * @param int $order The order of the instruction.
     * @param string $opcode The opcode of the instruction.
     * @param Argument[] $args The arguments of the instruction.
     */
    public function __construct($order, $opcode, $args)
    {
        $this->order = $order;
        $this->opcode = $opcode;
        $this->args = $args;
    }

    /**
     * Gets the first argument.
     *
     * @return Argument The first argument.
     */
    public function getFirstArg()
    {
        return $this->args['arg1'];
    }

    /**
     * Gets the second argument.
     *
     * @return Argument The second argument.
     */
    public function getSecondArg()
    {
        return $this->args['arg2'];
    }

    /**
     * Gets the third argument.
     *
     * @return Argument The third argument.
     */
    public function getThirdArg()
    {
        return $this->args['arg3'];
    }
}
