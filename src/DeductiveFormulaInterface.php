<?php

namespace rizalmf\formula;

interface DeductiveFormulaInterface
{
    /**
     * set Variables
     * 
     * @param array $vars
     * @return void
     */
    function setVariables($vars);

    /**
     * set variable based on key
     * 
     * @param string $key
     * @param float $val
     * @return void
     */
    function setVariable($key, $val);

    /**
     * return value from given key
     * 
     * @param string $key
     * @return float value
     */
    function getVariable($key);

    /**
     * return all variables that has been set
     * 
     * @return array
     */
    function getVariables();

    /**
     * set formula
     * 
     * @param string|file $formula
     * @return array
     */
    function setFormula($formula);

    /**
     * show raw formula
     * 
     * @return string Formula
     */
    function getFormula();

    /**
     * return possible variables from Formula
     * 
     * @return array
     */
    function getRequestedVariables();

    /**
     * Show prepared formula before execute
     * 
     * @return string
     */
    function getFormulaExposed();

    /**
     * execute 
     * 
     * @param boolean $debug
     * @return numeric result
     */
    function execute($debug = false);

    /**
     * set limit iteration
     * 
     * @param int $limit
     * @return void
     */
    function setLimit($limit);

}