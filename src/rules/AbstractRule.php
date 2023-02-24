<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use ilyaplot\pulse\LevelEnum;

abstract class AbstractRule implements RuleInterface
{
    protected ?bool $status = null;
    protected string $description = '';
    protected ?LevelEnum $level = null;

    public function getStatus(): bool
    {
        if ($this->status === null) {
            $this->status = $this->run();
        }
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLevel(): ?LevelEnum
    {
        return $this->level;
    }

    public function setLevel(LevelEnum $level): void
    {
        $this->level = $level;
    }

    abstract public function run(): bool;
}
