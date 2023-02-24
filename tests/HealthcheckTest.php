<?php

declare(strict_types=1);

use ilyaplot\pulse\Healthcheck;

/**
 * @covers \ilyaplot\pulse\Healthcheck
 */
class HealthcheckTest extends PHPUnit\Framework\TestCase
{
    public function testGetDescription()
    {
        $description = 'My description';
        $check = new Healthcheck($description, function () {
        });
        self::assertEquals($description, $check->getDescription());
    }

    public function testGetStatus()
    {
        $check = new Healthcheck('testing!', function () {
            return true;
        });
        self::assertTrue($check->getStatus(), 'Verify truthy return value');
        self::assertEquals(Healthcheck::CRITICAL, $check->getType());

        $test = new stdClass();
        $test->blah = 1;

        $check2 = new Healthcheck('testing!', function () use ($test) {
            $test->blah++;
            return false;
        });

        $check2->getStatus();
        self::assertEquals(2, $test->blah, 'Test using closure and `use`');

        $check2->getStatus();
        self::assertEquals(2, $test->blah, 'Test is only executed once');
    }

    public function testWarning()
    {
        $check = new Healthcheck('This is a warning', function () {
            return true;
        }, Healthcheck::WARNING);

        self::assertEquals(Healthcheck::WARNING, $check->getType());
    }

    public function testInfo()
    {
        $check = new Healthcheck(
            'This is a info-level check',
            fn() => 'This is a message!',
            Healthcheck::INFO
        );

        self::assertEquals(Healthcheck::INFO, $check->getType());
        self::assertEquals('This is a message!', $check->getStatus());
    }

    public function testCritical()
    {
        $check = new Healthcheck('This is a critical check', fn() => true, Healthcheck::CRITICAL);

        self::assertEquals(Healthcheck::CRITICAL, $check->getType());
    }
}
