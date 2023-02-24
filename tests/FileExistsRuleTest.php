<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\rules\FileRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\rules\FileRule
 * @covers \ilyaplot\pulse\rules\AbstractRule
 */
class FileExistsRuleTest extends TestCase
{
    public function testSuccess(): void
    {
        $rule = new FileRule(__FILE__);
        $rule->run();
        self::assertTrue($rule->getStatus());
    }

    public function testFail(): void
    {
        $rule = new FileRule(__FILE__ . '.not_exists');
        $rule->run();
        self::assertFalse($rule->getStatus());
    }

    public function testChangeLevel(): void
    {
        $rule = new FileRule(
            __FILE__,
            'Test rule',
            LevelEnum::critical
        );
        $rule->setLevel(LevelEnum::warning);
        $rule->run();
        self::assertTrue($rule->getStatus());
        self::assertEquals(LevelEnum::warning, $rule->getLevel());
    }

    public function testDescription(): void
    {
        $rule = new FileRule(__FILE__, 'Test rule', LevelEnum::critical);
        self::assertEquals('Test rule', $rule->getDescription());
    }

    public function testBadClosure(): void
    {
        $rule = new FileRule(__FILE__, 'Test rule', LevelEnum::critical);
        self::assertEquals('Test rule', $rule->getDescription());
    }
}
