<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;

class RequiredIntExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'amount';

    const MIN = 10;

    const MAX = 100;

    public static function getInputParameterList()
    {
        return [
            new InputParameter(
                self::NAME,
                new GetInt(),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ),
        ];
    }
}
