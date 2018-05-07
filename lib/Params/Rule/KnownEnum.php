<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;

/**
 * Class KnownEnum
 *
 * Checks that the value is one of a known
 */
class KnownEnum implements Rule
{
    /** @var array  */
    private $allowedValues;

    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        if (in_array($value, $this->allowedValues, true) !== true) {
            return ValidationResult::errorResult(
                "Value is not known. Please use one of " . implode(', ', $this->allowedValues)
            );
        }

        return ValidationResult::valueResult($value);
    }
}
