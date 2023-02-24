<?php

declare(strict_types=1);

use ilyaplot\pulse\LevelEnum;
use ilyaplot\pulse\rules\FileRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\rules\FileRule
 * @covers \ilyaplot\pulse\rules\AbstractRule
 */
class FileRuleTest extends TestCase
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private string $notWritableReadableFile;

    protected function setUp(): void
    {
        $this->notWritableReadableFile = tempnam(sys_get_temp_dir(), 'readonly')
            ?: throw new Exception('Cannot create temp file');
        chmod($this->notWritableReadableFile, 0111);
    }

    protected function tearDown(): void
    {
        unlink($this->notWritableReadableFile);
    }

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

    public function testFileIsDirectory(): void
    {
        $rule = new FileRule(
            __FILE__,
            checkIsDirectory: true,
        );
        self::assertFalse($rule->getStatus());
    }

    public function testDirectoryIsFile(): void
    {
        $rule = new FileRule(
            __DIR__,
            checkIsFile: true,
        );
        self::assertFalse($rule->getStatus());
    }

    public function testIsExecutable(): void
    {
        $rule = new FileRule(
            __FILE__,
            checkExecutable: true,
        );
        self::assertFalse($rule->getStatus());
    }

    public function testIsReadable(): void
    {
        $rule = new FileRule(
            $this->notWritableReadableFile,
            checkExists: false,
            checkReadable: true,
        );
        self::assertFalse($rule->getStatus());
    }

    public function testIsWriteable(): void
    {
        $rule = new FileRule(
            $this->notWritableReadableFile,
            checkWriteable: true,
        );
        self::assertFalse($rule->getStatus());
    }
}
