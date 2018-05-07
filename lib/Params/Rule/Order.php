<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use Params\Value\OrderElement;
use Params\Value\OrderParam;
use Params\Functions;
use Params\Value\Ordering;

/**
 * Class Order
 *
 * Supports a parameter to specify ordering of results
 * For example "+name,-date" would be equivalent to ordering
 * by name ascending, then date descending.
 */
class Order implements Rule
{
    /** @var string[] */
    private $knownOrderNames;

    /**
     * OrderValidator constructor.
     * @param string[] $knownOrderNames
     */
    public function __construct(array $knownOrderNames)
    {
        $this->knownOrderNames = $knownOrderNames;
    }

    public function __invoke(string $name, $value) : ValidationResult
    {
        $parts = explode(',', $value);
        $orderElements = [];

        foreach ($parts as $part) {
            list($partName, $partOrder) = Functions::normalise_order_parameter($part);
            if (in_array($partName, $this->knownOrderNames, true) !== true) {
                $message = sprintf(
                    "Cannot order by [%s] for [%s], as not known for this operation. Known are [%s]",
                    $partName,
                    $name,
                    implode(', ', $this->knownOrderNames)
                );

                return ValidationResult::errorResult($message);
            }
            $orderElements[] = new OrderElement($partName, $partOrder);
        }

        return ValidationResult::valueResult(new Ordering($orderElements));
    }
}
