<?php
/**
 * IPP - PHP Project
 * @author Ondřej Janečka
 */

namespace IPP\Student;

use IPP\Core\AbstractInterpreter;
use IPP\Student\Library\XmlParser as parse;
use IPP\Student\Library\InstructionExecutor as executor;

class Interpreter extends AbstractInterpreter
{
    public function execute(): int
    {
        $dom = $this->source->getDOMDocument();

        $xmlParser = new parse($dom);

        $xmlParser->parseXml();

        $instructions = $xmlParser->getInstructions();

        $executor = new executor($instructions, $this->input, $this->stdout);
        $executor->executeInstructions();
        
        exit(0);
    }
}
