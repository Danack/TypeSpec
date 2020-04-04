<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Takes user input and converts it to an float value, or
 * generates appropriate validationProblems.
 */
class FloatInput implements ProcessRule
{
    /**
     * Convert a generic input value to an integer
     *
     * @param Path $path
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(
        Path $path,
        $value,
        ParamValues $validator,
        DataLocator $dataLocator
    ) : ValidationResult {
        // TODO - check is null
        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $dataLocator,
                    "Value is an empty string - must be a floating point number."
                );
            }

            $match = preg_match(
                "~        #delimiter
                    ^           # start of input
                    -?          # minus, optional
                    [0-9]+      # at least one digit
                    (           # begin group
                        \.      # a dot
                        [0-9]+  # at least one digit
                    )           # end of group
                    ?           # group is optional
                    $           # end of input
                ~xD",
                $value
            );

            if ($match !== 1) {
                // TODO - says what position bad character is at.
                return ValidationResult::errorResult(
                    $dataLocator,
                    "Value must be a floating point number."
                );
            }
        }

        return ValidationResult::valueResult(floatval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // todo - this seems like a not needed rule.
    }
}
