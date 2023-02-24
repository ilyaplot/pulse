<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\RuleResultDto;
use PHPUnit\Framework\TestCase;

class RuleResultDtoTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $result = new RuleResultDto(
            true,
            'Rule 1',
            LevelEnum::info,
            null
        );

        self::assertEquals(
            '{"isSuccess":true,"description":"Rule 1","level":"info","errorMessage":null}',
            json_encode($result),
        );


        $result = new RuleResultDto(
            false,
            'Rule 2',
            LevelEnum::critical,
            "Second rule"
        );

        self::assertEquals(
            '{"isSuccess":false,"description":"Rule 2","level":"critical","errorMessage":"Second rule"}',
            json_encode($result),
        );
    }
}
