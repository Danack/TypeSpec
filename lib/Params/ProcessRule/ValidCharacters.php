<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\Exception\LogicException;
use Params\Messages;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Class ValidCharacters
 *
 * Checks that an input string contains only valid characters.
 * Flags used for preg_match are xu
 *
 */
class ValidCharacters implements ProcessRule
{
    private string $patternValidCharacters;

    public function __construct(string $patternValidCharacters)
    {
        $this->patternValidCharacters = $patternValidCharacters;
    }

    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult
    {
        $patternInvalidCharacters = "/[^" . $this->patternValidCharacters . "]+/xu";
        $matches = [];
        $count = preg_match($patternInvalidCharacters, $value, $matches, PREG_OFFSET_CAPTURE);

        if ($count === false) {
            throw new LogicException("preg_match failed");
        }

        if ($count !== 0) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                Messages::INVALID_CHAR_MESSAGE,
                $badCharPosition,
                $this->patternValidCharacters
            );
            return ValidationResult::errorResult($dataLocator, $message);
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setPattern($this->patternValidCharacters);
    }
}
