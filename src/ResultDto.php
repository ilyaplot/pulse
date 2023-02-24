<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

final class ResultDto
{
    /**
     * @param bool $isSuccess
     * @param RuleResultDto[] $checks
     */
    public function __construct(
        public readonly bool $isSuccess,
        public readonly array $checks,
    ) {
    }
}
