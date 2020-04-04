<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueDataLocator;
use Params\DataLocator\StandardDataLocator;
use Params\Messages;
use Params\ProcessRule\MinimumCount;
use ParamsTest\BaseTestCase;
use Params\Exception\LogicException;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class MinimumCountTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [0, [1]], // 0 <= 1
            [1, [1]], // 1 <= 1
            [2, [1, 2]], // 2 <= 2
            [2, [1, 2, 3, 4, 5]], // 2 <= 5
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testWorks(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $values,
            $validator,
            $dataLocator
        );
        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public function provideFailsCases()
    {
        return [
            [1, []], // 3 > 0
            [3, [1, 2]], // 4 > 3
            [50, [1, 2]], // 4 > 3
        ];
    }

    /**
     * @dataProvider provideFailsCases
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testFails(int $minimumCount, $values)
    {
        $rule = new MinimumCount($minimumCount);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $values,
            $validator,
            SingleValueDataLocator::create($values)
        );
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        $this->assertRegExp(
//            stringToRegexp(MinimumCount::ERROR_TOO_FEW_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            createPath([]),
            Messages::ERROR_TOO_FEW_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
        new MinimumCount(-2);
    }

    /**
     * @covers \Params\ProcessRule\MinimumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MinimumCount(3);
        $this->expectException(LogicException::class);

        $validator = new ParamsValuesImpl();
        $this->expectErrorMessageMatches(
            stringToRegexp(Messages::ERROR_WRONG_TYPE)
        );

        $rule->process(
            Path::fromName('foo'),
            'a banana',
            $validator,
            SingleValueDataLocator::create('a banana')
        );
    }
}
