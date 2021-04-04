<?php

namespace rizalmf\formula\parser;

use rizalmf\formula\exception\BadFormulaException;

/**
 * String parser 
 */
class Parser
{
    /**
     * Parsing given formula to obtain possible variables
     * 
     * @param string $formula
     * @return array $variables
     * @throws BadFormulaException
     */
    public function parse($formula)
    {
        $count = 0;
        for ($i=0; $i < strlen($formula); $i++) { 
            if (preg_match('/[(){}]/', $formula[$i])) 
                $count++;
        }

        if ($count % 2 == 0) {
            preg_match_all('/{+(.*?)}/', $formula, $variables);
            // return without any brackets
            return isset($variables[1]) ? $variables[1] : [];
        } else 
            throw new BadFormulaException('Parentheses/Curly brackets count not valid.');
    }
}
