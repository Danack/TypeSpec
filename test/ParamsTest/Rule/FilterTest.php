<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use ParamsTest\BaseTestCase;
use Params\Rule\MultipleKnownEnum;

/**
 * @coversNothing
 */
class FilterTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['time', ['time'], false],
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestCases
     */
    public function testValidation($testValue, $expectedFilters, $expectError)
    {
        $validator = new MultipleKnownEnum(['time', 'distance']);
        $validationResult = $validator('foo', $testValue);

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(\Params\Value\MultipleEnums::class, $value);

        /** @var $value \Params\Value\MultipleEnums */
        $this->assertEquals($expectedFilters, $value->getValues());
    }
}
