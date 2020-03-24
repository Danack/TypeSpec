<?php

declare(strict_types=1);

namespace Params\Value;

/**
 * Class Filtering
 */
class MultipleEnums
{
    /** @var array<string> */
    private array $values;

    /**
     * Order constructor.
     * @param string[] $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Returns the strings to filter by.
     * @return string[]
     */
    public function getValues()
    {
        return $this->values;
    }
}
