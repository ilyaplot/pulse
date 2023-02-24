<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\rules\ClosureRule;
use ilyaplot\pulse\rules\RuleInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\rules\ClosureRule
 */
class ClosureRuleTest extends TestCase
{
    public function testSuccess(): void
    {
        $rule = new ClosureRule(fn() => true, 'Test rule');
        $rule->run();
        self::assertTrue($rule->getStatus());
    }

    public function testFail(): void
    {
        $rule = new ClosureRule(fn() => false, 'Test rule');
        $rule->run();
        self::assertFalse($rule->getStatus());
    }

    public function testChangeLevel(): void
    {
        $rule = new ClosureRule(
            function (RuleInterface $rule) {
                $rule->setLevel(LevelEnum::warning);
                return true;
            },
            'Test rule',
            LevelEnum::critical
        );
        $rule->run();
        self::assertTrue($rule->getStatus());
        self::assertEquals(LevelEnum::warning, $rule->getLevel());
    }

    public function testDescription(): void
    {
        $rule = new ClosureRule(fn() => true, 'Test rule', LevelEnum::critical);
        self::assertEquals('Test rule', $rule->getDescription());
    }

    public function testBadClosure(): void
    {
        $rule = new ClosureRule(fn() => 'Wrong result', 'Test rule', LevelEnum::critical);
        self::assertEquals('Test rule', $rule->getDescription());
    }
}