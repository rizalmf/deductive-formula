<?php declare(strict_types=1);

namespace rizalmf\formula\test;

use PHPUnit\Framework\TestCase;
use rizalmf\formula\DeductiveFormula;
use rizalmf\formula\exception\BadFormulaException;
use rizalmf\formula\exception\FormulaException;

final class FormulaTest extends TestCase
{
    /**
     * @var \rizalmf\formula\DeductiveFormulaInterface
     */
    private $formula;

    public function setUp()
    {
        $this->formula = new DeductiveFormula;
    }

    public function testCanSetFormula()
    {
        $this->assertIsArray(
            $this->formula->setFormula('{a}^({b}/2)')
        );
    }

    public function testCannotParsingFormula(): void
    {
        $this->expectException(BadFormulaException::class);
        $this->formula->setFormula('((2/2)');
    }

    public function testCannotExecuteBecauseMissingVariables()
    {
        $this->expectException(FormulaException::class);
        $this->formula->setFormula('{a}^({b}/2)');
        $this->formula->execute();
    }

    public function testCanEvaluateExpression()
    {
        $this->formula->setFormula('{foo}^({bar}/2+(2+3^(1/2)))');
        $this->formula->setVariables([
            'foo' => 4,
            'bar' => 1
        ]);
        $this->assertIsNumeric($this->formula->execute());
    }
}
