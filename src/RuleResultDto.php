<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

final class RuleResultDto implements \JsonSerializable
{
    public function __construct(
        public readonly bool $isSuccess,
        public readonly string $description,
        public readonly LevelEnum $level,
        public readonly ?string $errorMessage = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'isSuccess' => $this->isSuccess,
            'description' => $this->description,
            'level' => match ($this->level) {
                LevelEnum::info => 'info',
                LevelEnum::warning => 'warning',
                LevelEnum::critical => 'critical',
                default => null,
            },
            'errorMessage' => $this->errorMessage,
        ];
    }
}
