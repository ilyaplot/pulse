<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

use AssertionError;
use ilyaplot\pulse\rules\RuleInterface;

class Pulse
{
    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(
        private array $rules = [],
    ) {
    }

    public function add(RuleInterface $rule, ?LevelEnum $levelEnum = null): void
    {
        $newRule = clone $rule;

        if ($levelEnum !== null) {
            $newRule->setLevel($levelEnum);
        } elseif ($newRule->getLevel() === null) {
            $newRule->setLevel(LevelEnum::critical);
        }

        $this->rules[] = $newRule;
    }

    public function addInfo(RuleInterface $rule): void
    {
        $this->add($rule, LevelEnum::info);
    }

    public function addWarning(RuleInterface $rule): void
    {
        $this->add($rule, LevelEnum::warning);
    }

    public function addCritical(RuleInterface $rule): void
    {
        $this->add($rule);
    }

    /**
     * @throws AssertionError
     */
    public function run(LevelEnum $successLevel = LevelEnum::warning): ResultDto
    {
        foreach ($this->rules as $rule) {
            assert($rule instanceof RuleInterface, 'Rule must implement RuleInterface');
            $rule->run();
        }

        $valuableResults = array_filter(
            $this->rules,
            fn(RuleInterface $result) => $this->getRuleLevel($result)->value > $successLevel->value,
        );

        $status = true;

        foreach ($valuableResults as $valuableResult) {
            if (!$valuableResult->getStatus()) {
                $status = false;
                break;
            }
        }

        return new ResultDto($status, array_map(
            fn(RuleInterface $rule) => $this->getRuleResult($rule),
            $this->rules
        ));
    }

    private function getRuleLevel(RuleInterface $rule): LevelEnum
    {
        return $rule->getLevel() ?? LevelEnum::critical;
    }

    private function getRuleResult(RuleInterface $rule): RuleResultDto
    {
        return new RuleResultDto(
            $rule->getStatus(),
            $rule->getDescription(),
            $this->getRuleLevel($rule),
            $rule->getErrorMessage(),
        );
    }
}
