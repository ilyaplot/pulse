<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use AssertionError;
use ilyaplot\pulse\LevelEnum;
use Throwable;

abstract class AbstractRule implements RuleInterface
{
    protected ?bool $status = null;
    protected string $description = '';
    protected ?LevelEnum $level = null;
    protected ?string $errorMessage = null;

    /**
     * @throws AssertionError
     */
    public function getStatus(): bool
    {
        if ($this->status === null) {
            try {
                $this->status = $this->run();
            } catch (AssertionError $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                $this->status = false;
                $this->setErrorMessage($exception->getMessage());
            }
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

    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    abstract public function run(): bool;
}
