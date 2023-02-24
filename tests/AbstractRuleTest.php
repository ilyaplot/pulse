<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\rules\AbstractRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\rules\AbstractRule
 */
class AbstractRuleTest extends TestCase
{
    public function testInvalidRun(): void
    {
        $this->expectException(AssertionError::class);
        $ruleClass = new class extends AbstractRule {
            public function __construct()
            {
                $this->description = 'Test description';
            }

            public function run(): bool
            {
                throw new AssertionError('Invalid callable');
            }
        };

        $ruleClass->getStatus();
    }

    public function testRunException(): void
    {
        $rule = new class extends AbstractRule {
            public function __construct()
            {
                $this->description = 'Test description';
            }

            public function run(): bool
            {
                throw new Exception('Test error');
            }
        };

        $status = $rule->getStatus();
        self::assertEquals('Test error', $rule->getErrorMessage());
        self::assertFalse($status);
    }
}
