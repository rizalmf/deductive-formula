<?php

namespace rizalmf\formula\deductive;

use rizalmf\formula\exception\BadFormulaException;
use rizalmf\formula\exception\FormulaException;

/**
 * Regex Expression based on RegExr
 * 
 * see @link https://regexr.com/
 */
class Machine {

    const OPERATOR_ADDITION = '+';
    const OPERATOR_SUBTRACTION = '-';
    const OPERATOR_MULTIPLYCATION = '*';
    const OPERATOR_DEVISION = '/';

    /**
     * High priority operator
     */
    const OPERATOR_POWER = '^';

    /**
     * All possible math operator
     */
    private $OPERATOR = [
        self::OPERATOR_ADDITION,
        self::OPERATOR_SUBTRACTION,
        self::OPERATOR_MULTIPLYCATION,
        self::OPERATOR_DEVISION,
        self::OPERATOR_POWER
    ];

    /**
     * List of prioritize operator that will execute first after operator power
     */
    private $PRIORITIZE_OPERATOR = [
        self::OPERATOR_MULTIPLYCATION,
        self::OPERATOR_DEVISION
    ];

    /**
     * Operator has nominal ex : -5
     */
    private $OPERATOR_NOMINAL = [
        self::OPERATOR_SUBTRACTION
    ];
    
    /**
     * Max iteration count while executing formula
     */
    private $MAX_ITERATOR_COUNT = 200;

    /**
     * State of count
     */
    private $COUNT = 1;

    /**
     * set max iteration while processing formula 
     * 
     * @param int $max
     * @return void
     */
    public function setMaxIteration($max)
    {
        if (intval($max) <= 0) {
            throw new BadFormulaException("Jangan gitu dong.");
        }

        $this->MAX_ITERATOR_COUNT = intval($max);
    }

    /**
     * Calculate given formula
     * 
     * @param string $formula
     * @return float
     * @throws FormulaException
     */
    public function calculate($formula, $debug = false)
    {
        if ($this->isOperatorExist($formula) && ($this->COUNT++ <= $this->MAX_ITERATOR_COUNT)) {
            if ($debug) {
                echo "iteration-".($this->COUNT-1)." => ".$formula.PHP_EOL;
            }

            $parentheses = $this->parentheses($formula);

            if (is_null($parentheses)) {
                // so far udah good
                $formula = $this->calculate($this->controller($formula), $debug);
            } else {
                // bug inside parentheses if there are more than 1 operator
                $formula = $this->calculate(
                    $this->reFormula(
                        $formula, 
                        $parentheses, 
                        $this->controller(preg_replace('/[()]/', '', $parentheses)),
                    ), 
                    $debug
                );
            }
        } else {
            $formula = preg_replace('/[()]/', '', $formula);
        }

        if ($this->COUNT >= $this->MAX_ITERATOR_COUNT) {
            throw new FormulaException("Max iteration count exceeded at '$formula'");
        }

        return $formula;
    }

    /**
     * get parentheses if exist. return first parentheses
     * 
     * @param string formula
     * @return string|null first parentheses | NULL
     */
    private function parentheses($formula)
    {
        return preg_match('/[()]/', $formula) ? 
            substr(explode(")",$formula, 2)[0].")",  strrpos(explode(")",$formula, 2)[0], "(")) 
            : null;
    }

    /**
     * Regenerate formula replacing executed formula with result
     * (Bug parentheses)
     * @param string $oldFormula
     * @param string $executedFormula
     * @param string|float $result
     */
    private function reFormula($oldFormula, $executedFormula, $result)
    {
        if (!preg_match('/[()]/', $result)) {
            $result = !empty(array_intersect($this->OPERATOR, str_split($result))) ? "($result)" : $result;
        }
        $pos = mb_strpos($oldFormula, $executedFormula);
        return mb_substr($oldFormula, 0, $pos) . $result . mb_substr($oldFormula, $pos + mb_strlen($executedFormula), null);
    }

    /**
     * control given formula to determine all preparation value for evaluating
     * 
     * @param string $formula
     * @return string 
     */
    private function controller($formula)
    {
        // do iteration evaluate based on prioritize operator
        $var1 = $var2 = $operator = '';
        $switch = $prioritize = $operatorFound = false;

        // OPERATOR_POWER is high priority. like you :3
        if (in_array(self::OPERATOR_POWER, str_split($formula))) {
            for ($i=0; $i < strlen($formula); $i++) { 
                $char = strval($formula[$i]);
                if ($char == self::OPERATOR_POWER && !$switch) {
                    $operator = strval($char);
                    $switch = $operatorFound = true;
                } elseif ($switch 
                    && !in_array($char, $this->OPERATOR)) {
                    $var2 .= $char;
                } elseif (!in_array($char, $this->OPERATOR)) {
                    $var1 .= $char;
                } elseif (!$switch 
                    && $char != self::OPERATOR_POWER) {
                    // reset $var1
                    $var1 = '';
                }elseif ($switch 
                    && in_array($char, $this->OPERATOR)) {
                    // end of single evaluation
                    break;
                }
            }
            
        } elseif (!empty(array_intersect($this->PRIORITIZE_OPERATOR, str_split($formula)))) {
            for ($i=0; $i < strlen($formula); $i++) { 
                $char = strval($formula[$i]);
                if (in_array($char, $this->PRIORITIZE_OPERATOR) 
                    && !$switch 
                    && !$prioritize
                ) {
                    $operator = strval($char);
                    $switch = $prioritize = $operatorFound = true;
                } elseif ($switch && !in_array($char, $this->OPERATOR)) {
                    $var2 .= $char;
                } elseif (!in_array($char, $this->OPERATOR)) {
                    $var1 .= $char;
                } elseif (!$switch 
                    && in_array($char, $this->OPERATOR) 
                    && !in_array($char, $this->PRIORITIZE_OPERATOR)) {
                    // reset $var1
                    $var1 = '';
                }elseif ($switch && in_array($char, $this->OPERATOR)) {
                    // end of single evaluation
                    break;
                }
            }
            
        } else {
            // there are no prioritize operator
            for ($i=0; $i < strlen($formula); $i++) { 
                $char = strval($formula[$i]);
                if (in_array($char, $this->OPERATOR) && !$switch) {
                    $operator = strval($char);
                    $switch = $prioritize = $operatorFound = true;
                } elseif ($switch && !in_array($char, $this->OPERATOR)) {
                    $var2 .= $char;
                } elseif (!in_array($char, $this->OPERATOR)) {
                    $var1 .= $char;
                } elseif ($switch && in_array($char, $this->OPERATOR)) {
                    // end of single evaluation
                    break;
                }
            }
        }
        // echo "expression: $formula, var1:$var1, var2:$var2, operator:$operator".PHP_EOL;

        return $operatorFound ? 
            $this->reFormula(
                $formula, 
                $var1.$operator.$var2, 
                $this->evaluate($var1, $operator, $var2)
            ) : $formula;
    }

    /**
     * cek if operator exist on formula
     * accepeted operator : (+), (-), (*), (/), (^)
     * 
     * @param string $formula
     * @return boolean
     */
    private function isOperatorExist($formula)
    {
        return boolval(preg_match("/[\^+*\/-]/", $formula));
    }

    /**
     * evaluate between 2 valid number and 1 operator
     * 
     * @param string $var1 first valid number
     * @param string $operator accepeted operator : (+), (-), (*), (/), (^)
     * @param string $var2 second valid number
     * @return float
     * @throws FormulaException
     */
    private function evaluate($var1, $operator, $var2)
    {
        $result = 0;
        switch ($operator) {
            case self::OPERATOR_ADDITION:
                $result = floatval($var1) + floatval($var2);
                break;
            case self::OPERATOR_SUBTRACTION:
                $result = floatval($var1) - floatval($var2);
                break;
            case self::OPERATOR_MULTIPLYCATION:
                $result = floatval($var1) * floatval($var2);
                break;
            case self::OPERATOR_DEVISION:
                $result = floatval($var1) / floatval($var2);
                break;
            case self::OPERATOR_POWER:
                $result = pow(floatval($var1), floatval($var2));
                break;
            default : $result = floatval($var1); // is this necessary?
        }

        if (is_infinite($result)) {
            throw new FormulaException('Infinite result.');
        } elseif (is_nan($result)) {
            throw new FormulaException('NaN result.');
        }

        return number_format($result, strlen($result) - strlen(floor($result)), '.', '');
    }
}