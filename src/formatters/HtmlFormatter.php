<?php

declare(strict_types=1);

namespace ilyaplot\pulse\formatters;

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\ResultDto;
use RuntimeException;

/**
 * @covers /src/formatters/html-template.php:90
- ilyaplot\pulse\ResultDto
- ilyaplot\pulse\RuleResultDto
 */
class HtmlFormatter
{
    public function __construct(
        private readonly string $templateFile = __DIR__ . '/html-template.php',
    ) {
    }

    public function format(ResultDto $resultDto): string
    {
        ob_start();
        $formatter = $this;
        /** @psalm-suppress UnresolvableInclude */
        require($this->templateFile);
        return ob_get_clean() ?: throw new RuntimeException('Failed to get output buffer');
    }

    public function getLevelString(LevelEnum $level): string
    {
        return match ($level) {
            LevelEnum::info => 'INFO',
            LevelEnum::warning => 'WARNING',
            LevelEnum::critical => 'CRITICAL',
        };
    }

    public function getLevelClass(LevelEnum $level): string
    {
        return match ($level) {
            LevelEnum::info => 'info',
            LevelEnum::warning => 'warning',
            LevelEnum::critical => 'critical',
        };
    }
}
