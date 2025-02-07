<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\LaterThanParam;
use DataTypeTest\BaseTestCase;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class LaterThanParamTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testWorks()
    {
        $previousTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-02T10:00:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $previousTime]);
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rule = new LaterThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);
        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testSameTimeErrors()
    {
        $previousTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-02T10:00:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-02T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $previousTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('bar', $value);

        $rule = new LaterThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/bar',
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PARAM_ERROR,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testInvalidMinutes()
    {
        $this->expectExceptionMessageMatchesTemplateString(Messages::MINUTES_MUST_BE_GREATER_THAN_ZERO);
        new LaterThanParam('foo', -5);
    }

    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testMissing()
    {
        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );
        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorage = $dataStorage->moveKey('foo');

        $rule = new LaterThanParam('foo', 0);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_NO_PREVIOUS_PARAMETER,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testPreviousTimeWrongType()
    {
        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => 'John']);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new LaterThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);


        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testCurrentTimeWrongType()
    {
        $value = 'John';

        $previousTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-02T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $previousTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new LaterThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testErrorsCorrect()
    {

        $afterTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:09:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $afterTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new LaterThanParam('foo', 10);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PARAM_ERROR,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\LaterThanParam
     */
    public function testDescription()
    {

        $parameterName = 'foo';

        $rule = new LaterThanParam($parameterName, 5);
        $description = $this->applyRuleToDescription($rule);

        $this->assertStringMatchesTemplateString(
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PREVIOUS_VALUE,
            $description->getDescription()
        );

        $this->assertStringContainsString($parameterName, $description->getDescription());
    }
}
