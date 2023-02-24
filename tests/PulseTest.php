<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\Pulse;
use ilyaplot\pulse\rules\ClosureRule;
use ilyaplot\pulse\rules\FileRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\Pulse
 * @covers \ilyaplot\pulse\LevelEnum
 * @covers \ilyaplot\pulse\ResultDto
 * @covers \ilyaplot\pulse\RuleResultDto
 * @covers \ilyaplot\pulse\rules\AbstractRule
 * @covers \ilyaplot\pulse\rules\FileRule
 *
 * @uses   \ilyaplot\pulse\rules\ClosureRule
 */
class PulseTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private Pulse $pulse;

    protected function setUp(): void
    {
        $this->pulse = new Pulse();
    }

    public function testEmptyRules(): void
    {
        self::assertTrue($this->pulse->run()->isSuccess, 'Empty rules should be ok');
    }

    public function testInfo(): void
    {
        $this->pulse->add(new ClosureRule(
            fn() => false,
            'Info rule',
            LevelEnum::info,
        ));

        self::assertTrue($this->pulse->run()->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::warning)->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::critical)->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::info)->isSuccess);
    }

    public function testWarning(): void
    {
        $this->pulse->add(new ClosureRule(
            fn() => false,
            'Warning rule',
            LevelEnum::warning,
        ));

        self::assertTrue($this->pulse->run()->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::warning)->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::critical)->isSuccess);
        self::assertFalse($this->pulse->run(LevelEnum::info)->isSuccess);
    }

    public function testCritical(): void
    {
        $this->pulse->add(new ClosureRule(
            fn() => false,
            'Critical rule',
            LevelEnum::critical,
        ));

        self::assertFalse($this->pulse->run()->isSuccess);
        self::assertFalse($this->pulse->run(LevelEnum::warning)->isSuccess);
        self::assertTrue($this->pulse->run(LevelEnum::critical)->isSuccess);
        self::assertFalse($this->pulse->run(LevelEnum::info)->isSuccess);
    }

    public function testAddInfo(): void
    {
        $this->pulse->addInfo(new ClosureRule(
            fn() => false,
            'Info rule',
        ));

        $this->pulse->addInfo(new ClosureRule(
            fn() => false,
            'Info rule',
            LevelEnum::warning,
        ));

        $resultDto = $this->pulse->run();

        self::assertEquals(LevelEnum::info, $resultDto->rules[0]->level);
        self::assertEquals(LevelEnum::info, $resultDto->rules[1]->level);
    }

    public function testAddWarning(): void
    {
        $this->pulse->addWarning(new ClosureRule(
            fn() => false,
            'Warning rule',
        ));

        $this->pulse->addWarning(new ClosureRule(
            fn() => false,
            'Warning rule',
            LevelEnum::info,
        ));

        $resultDto = $this->pulse->run();

        self::assertEquals(LevelEnum::warning, $resultDto->rules[0]->level);
        self::assertEquals(LevelEnum::warning, $resultDto->rules[1]->level);
    }

    public function testAddCritical(): void
    {
        $this->pulse->addCritical(new ClosureRule(
            fn() => false,
            'Critical rule',
        ));

        $this->pulse->add(new ClosureRule(
            fn() => false,
            'Critical rule',
        ), LevelEnum::critical);

        $resultDto = $this->pulse->run();

        self::assertEquals(LevelEnum::critical, $resultDto->rules[0]->level);
        self::assertEquals(LevelEnum::critical, $resultDto->rules[1]->level);
    }

    public function testConfiguredInstance(): void
    {
        $pulse = new Pulse([
            new ClosureRule(
                fn() => false,
                'Critical rule',
            ),
            new FileRule(
                __FILE__,
                'Critical rule',
            ),
        ]);

        self::assertFalse($pulse->run()->isSuccess);
    }
}
