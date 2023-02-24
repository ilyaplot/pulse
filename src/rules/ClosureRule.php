<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use Closure;
use ilyaplot\pulse\LevelEnum;

class ClosureRule extends AbstractRule implements RuleInterface
{
    public function __construct(
        private readonly Closure $callable,
        protected string $description,
        protected ?LevelEnum $level = null,
    ) {
    }

    public function run(): bool
    {
        $checkResult = call_user_func($this->callable, $this);
        assert(is_bool($checkResult), 'Healthcheck callable must return bool');
        return $checkResult;
    }
}