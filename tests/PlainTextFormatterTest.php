<?php

declare(strict_types=1);

use ilyaplot\pulse\formatters\PlainTextFormatter;
use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\ResultDto;
use ilyaplot\pulse\RuleResultDto;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\formatters\PlainTextFormatter
 */
class PlainTextFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $formatter = new PlainTextFormatter();
        $result = $formatter->format(new ResultDto(
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
        ));

        $actualResult = file_get_contents(__DIR__ . '/_data/plain-text-formatter-result.txt');

        self::assertEquals($result, $actualResult);
    }
}
