<?php

declare(strict_types=1);

namespace ParamsTest;

use ParamsTest\DTOTypes\BasicDTO;
use Params\Messages;
use ParamsTest\PropertyTypes\KnownColors;
use ParamsTest\PropertyTypes\MultipleBasicDTO;
use ParamsTest\DTOTypes\DTOThatHasArrayOfParam;
use ParamsTest\DTOTypes\AdvancedDTO;
use function Params\validate;

/**
 * @coversNothing
 */
class ObjectValidationTest extends BaseTestCase
{

    public function testWorksBasic()
    {
        $dto = new BasicDTO('red', 5);
        [$object, $validationProblems] = validate($dto);
        $this->assertEmpty($validationProblems);
    }

    public function testErrorsBasic()
    {
        $dto = new BasicDTO('purple', -15);
        [$object, $validationProblems] = validate($dto);

        $this->assertCount(2, $validationProblems);

        $this->assertNull($object);

        $this->assertValidationProblemRegexp(
            '/quantity',
            Messages::INT_TOO_SMALL,
            $validationProblems
        );

        $this->assertValidationProblemRegexp(
            '/color',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
            $validationProblems
        );

        $this->assertValidationProblemContains(
            '/color',
            'purple',
            $validationProblems
        );
        $this->assertValidationProblemContains(
            '/color',
            implode(", ", KnownColors::KNOWN_COLORS),
            $validationProblems
        );
    }

    public function testWorksAdvanced()
    {
        $dto1 = new BasicDTO('red', 5);
        $dto2 = new BasicDTO('green', 6);

        $dto = new AdvancedDTO([$dto1, $dto2], 10);
        [$object, $validationProblems] = validate($dto);
        $this->assertEmpty($validationProblems);
    }
}
