<?php

declare(strict_types=1);


namespace ParamsTest;

use Params\Param;
use Params\InputParameter;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessRule\IsRgbColor;

class ColorParam implements Param
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getInputParameter(): InputParameter
    {
        return new InputParameter(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
