<?php

declare(strict_types=1);

use ilyaplot\pulse\formatters\HtmlFormatter;
use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\ResultDto;
use ilyaplot\pulse\RuleResultDto;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\HtmlFormatter
 */
class HtmlFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $formatter = new HtmlFormatter();
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

        $needle = (string)file_get_contents(__DIR__ . '/_data/html-formatter-result.html');
        self::assertStringStartsWith($needle, $result);
    }
}
