<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

final class RuleResultDto
{
    public function __construct(
        public readonly bool $isSuccess,
        public readonly string $description,
        public readonly LevelEnum $level,
    ) {
    }
}