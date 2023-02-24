<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use Closure;
use ilyaplot\pulse\LevelEnum;
use Throwable;

class ClosureRule extends AbstractRule implements RuleInterface
{
    public function __construct(
        private readonly Closure $checkFunction,
        protected string $description,
        protected ?LevelEnum $level = null,
    ) {
    }

    public function run(): bool
    {
        try {
            $checkResult = call_user_func($this->checkFunction, $this);

            if (!is_bool($checkResult)) {
                $this->setErrorMessage('ClosureRule::checkFunction must return bool');
                $this->status = false;
                return false;
            }
        } catch (Throwable $exception) {
            $this->setErrorMessage($exception->getMessage());
            $this->status = false;
            return false;
        }

        $this->status = $checkResult;
        return $checkResult;
    }
}
