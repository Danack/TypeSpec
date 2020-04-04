<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MinIntValue;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class MinIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, (string)$underValue, true],
            [$minValue, (string)$exactValue, false],
            [$minValue, (string)$overValue, false],

            // TODO - think about these cases.
            [$minValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMinIntValueCases
     * @covers \Params\ProcessRule\MinIntValue
     */
    public function testValidation(int $minValue, string $inputValue, bool $expectError)
    {
        $rule = new MinIntValue($minValue);
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $inputValue,
            $validator,
            $dataLocator
        );

        if ($expectError === false) {
            $this->assertEmpty($validationResult->getValidationProblems());
        }
        else {
            $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
        }
    }
}
