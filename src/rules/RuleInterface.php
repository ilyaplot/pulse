<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use ilyaplot\pulse\LevelEnum;

interface RuleInterface
{
    public function run(): bool;

    public function getStatus(): bool;

    public function getDescription(): string;

    public function getLevel(): ?LevelEnum;

    public function setLevel(LevelEnum $level): void;

    public function setErrorMessage(string $errorMessage): void;

    public function getErrorMessage(): ?string;
}
