<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

use JsonSerializable;

final class ResultDto implements JsonSerializable
{
    /**
     * @param bool $isSuccess
     * @param RuleResultDto[] $rules
     */
    public function __construct(
        public readonly bool $isSuccess,
        public readonly array $rules,
    ) {
    }

    public function jsonSerialize(): array
    {
        $critical = count(array_filter(
            $this->rules,
            fn(RuleResultDto $rule) => !$rule->isSuccess && $rule->level === LevelEnum::critical
        ));

        $warnings = count(array_filter(
            $this->rules,
            fn(RuleResultDto $rule) => !$rule->isSuccess && $rule->level === LevelEnum::warning
        ));

        return [
            'healthy' => $this->isSuccess,
            'healthchecks' => $this->rules,
            'critical' => $critical,
            'warnings' => $warnings,
        ];
    }
}
