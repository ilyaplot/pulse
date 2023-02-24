<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\ResultDto;
use ilyaplot\pulse\RuleResultDto;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\RuleResultDto
 */
class ResultDtoTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $result = new ResultDto(
            false,
            [
                new RuleResultDto(
                    true,
                    'Rule 1',
                    LevelEnum::info,
                    null
                ),
                new RuleResultDto(
                    true,
                    'Rule 2',
                    LevelEnum::warning,
                    'Test'
                ),
                new RuleResultDto(
                    true,
                    'Rule 3',
                    LevelEnum::critical,
                ),
                new RuleResultDto(
                    false,
                    'Rule 4',
                    LevelEnum::info,
                    null
                ),
                new RuleResultDto(
                    false,
                    'Rule 5',
                    LevelEnum::warning,
                    'Test'
                ),
                new RuleResultDto(
                    false,
                    'Rule 6',
                    LevelEnum::critical,
                ),
            ]
        );

        $arrayResult = [
            'healthy' => false,
            'healthchecks' => [
                new RuleResultDto(
                    true,
                    'Rule 1',
                    LevelEnum::info,
                    null
                ),
                new RuleResultDto(
                    true,
                    'Rule 2',
                    LevelEnum::warning,
                    'Test'
                ),
                new RuleResultDto(
                    true,
                    'Rule 3',
                    LevelEnum::critical,
                ),
                new RuleResultDto(
                    false,
                    'Rule 4',
                    LevelEnum::info,
                    null
                ),
                new RuleResultDto(
                    false,
                    'Rule 5',
                    LevelEnum::warning,
                    'Test'
                ),
                new RuleResultDto(
                    false,
                    'Rule 6',
                    LevelEnum::critical,
                ),
            ],
            'critical' => 1,
            'warnings' => 1,
        ];

        self::assertEquals($arrayResult, $result->jsonSerialize());

        $actualResult = file_get_contents(__DIR__ . '/_data/json-format-result.txt');
        self::assertEquals(json_encode($result, JSON_PRETTY_PRINT), $actualResult);
    }
}
