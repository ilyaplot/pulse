<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\Pulse;
use ilyaplot\pulse\rules\ClosureRule;
use ilyaplot\pulse\rules\RuleInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\rules\ClosureRule
 * @covers \ilyaplot\pulse\rules\AbstractRule
 * @covers \ilyaplot\pulse\Pulse
 * @covers \ilyaplot\pulse\ResultDto
 * @covers \ilyaplot\pulse\RuleResultDto
 */
class ClosureRuleTest extends TestCase
{
    private int $counter = 0;

    protected function setUp(): void
    {
        $this->counter = 0;
    }

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

    public function testRunsWithStatusCount(): void
    {
        $rule = new ClosureRule(function () {
            $this->counter++;
            return true;
        }, 'Test rule', LevelEnum::critical);
        $rule->getStatus();
        $rule->getStatus();
        $rule->getStatus();
        self::assertEquals(1, $this->counter);
    }

    public function testRunsCount(): void
    {
        $rule = new ClosureRule(function () {
            $this->counter++;
            return true;
        }, 'Test rule', LevelEnum::critical);
        $rule->run();
        $rule->run();
        $rule->run();
        self::assertEquals(3, $this->counter);
    }

    public function testFullRunCounts(): void
    {
        $rule = new ClosureRule(
            function () {
                $this->counter++;
                return true;
            },
            'Test rule'
        );

        $pulse = new Pulse([$rule, $rule]);
        $pulse->run();

        self::assertEquals(2, $this->counter);
    }
}
