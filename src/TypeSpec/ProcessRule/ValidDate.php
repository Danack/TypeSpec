<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

class ValidDate implements ProcessPropertyRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if ($dateTime instanceof \DateTimeInterface) {
            $dateTime = $dateTime->setTime(0, 0, 0, 0);
            return ValidationResult::valueResult($dateTime);
        }

        return ValidationResult::errorResult($inputStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATE);
    }
}
