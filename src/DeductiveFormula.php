<?php

namespace rizalmf\formula;

use rizalmf\formula\deductive\Machine;
use rizalmf\formula\exception\BadFormulaException;
use rizalmf\formula\exception\FormulaException;
use rizalmf\formula\parser\Parser;
use rizalmf\formula\DeductiveFormulaInterface;

/**
 * ALPHA VERSION
 * 
 */
class DeductiveFormula implements DeductiveFormulaInterface
{
    private $parser;
    private $machine;
    private $variables;
    private $formula;

    public function __construct() {
        $this->parser = new Parser();
        $this->machine = new Machine();
        $this->variables = [];
    }

    /**
     * set Variables
     * 
     * @param array $vars
     * @return void
     */
    public function setVariables($vars)
    {
        foreach ($vars as $var) {
            $this->checkVariable($var);
        }

        $this->variables = $vars;
    }

    /**
     * check given value
     * 
     * @param array $vars
     * @return void
     * @throws FormulaException
     */
    private function checkVariable($val)
    {
        if (!is_numeric($val) || is_null($val)) {
            throw new FormulaException("Only numeric value is allowed.");
        }
    }

    /**
     * set variable based on key
     * 
     * @param string $key
     * @param float $val
     * @return void
     */
    public function setVariable($key, $val)
    {
        $this->checkVariable($val);
        $this->variables[$key] = $val;
    }

    /**
     * return value from given key
     * 
     * @param string $key
     * @return float value
     * @throws FormulaException
     */
    public function getVariable($key)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }

        throw new FormulaException("Variable '$key' not found.");
    }

    /**
     * return all variables that has been set
     * 
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * set formula
     * 
     * @param string $formula
     * @return array possible variables
     * @throws BadFormulaException
     */
    public function setFormula($formula)
    {
        if (is_string($formula)) {
            $this->formula = $formula;
            return $this->getRequestedVariables();
        } else 
            throw new BadFormulaException("Cannot format given param as a Formula.");
        
    }

    /**
     * show raw formula
     * 
     * @return string Formula
     * @throws FormulaException
     */
    public function getFormula()
    {
        if (is_null($this->formula)) {
            throw new FormulaException("Formula is not defined.");
        }

        return $this->formula;
    }

    /**
     * return possible variables from Formula
     * 
     * @return array
     */
    public function getRequestedVariables()
    {
        return $this->parser->parse($this->getFormula());
    }

    /**
     * Show prepared formula before execute
     * 
     * @return string
     * @throws FormulaException
     */
    public function getFormulaExposed()
    {
        $requested = $this->getRequestedVariables();

        $diff = array_diff($requested, array_keys($this->variables));
        if (count($diff) >= 1) {
            throw new FormulaException("Undefined variable found! (".implode(',', $diff).")");  
        }

        $formula = $this->formula;
        foreach ($requested as $var) {
            $formula = str_replace("{".$var."}", $this->variables[$var], $formula);
        }

        return $formula;
    }

    /**
     * execute 
     * 
     * @param boolean $debug
     * @return numeric result
     * @throws FormulaException
     */
    public function execute($debug = false)
    {
        $result =  $this->machine->calculate($this->getFormulaExposed(), $debug);
        if (is_numeric($result)) {
            return $result;
        }

        throw new FormulaException("NaN result.");  
    }

    /**
     * set limit iteration. default 200
     * 
     * @param int $limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->machine->setMaxIteration($limit);
    }

}