<?php

declare(strict_types=1);

namespace ilyaplot\pulse\formatters;

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\ResultDto;
use ilyaplot\pulse\RuleResultDto;

class PlainTextFormatter
{
    public function format(ResultDto $resultDto): string
    {
        $lines = array_map(
            fn(RuleResultDto $ruleResultDto) => $this->formatResult($ruleResultDto),
            $resultDto->checks,
        );

        $result = implode(PHP_EOL, $lines);
        $result .= PHP_EOL . PHP_EOL . 'Healthcheck summary: ' . ($resultDto->isSuccess ? 'PASS' : 'FAIL') . PHP_EOL;

        return $result;
    }

    private function formatResult(RuleResultDto $ruleResultDto): string
    {
        $levelString = $this->getLevelString($ruleResultDto->level);
        $statusString = $ruleResultDto->isSuccess ? 'PASS' : 'FAIL';

        return sprintf(
            '%s (%s): %s%s',
            $ruleResultDto->description,
            $levelString,
            $statusString,
            $ruleResultDto->errorMessage ? ' with message: ' . $ruleResultDto->errorMessage : '',
        );
    }

    private function getLevelString(LevelEnum $level): string
    {
        return match ($level) {
            LevelEnum::info => 'INFO',
            LevelEnum::warning => 'WARNING',
            LevelEnum::critical => 'CRITICAL',
        };
    }
}
