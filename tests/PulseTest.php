<?php

declare(strict_types=1);

use ilyaplot\pulse\Healthcheck;
use ilyaplot\pulse\Pulse;

/**
 * @covers \ilyaplot\pulse\Pulse
 */
class PulseTest extends PHPUnit\Framework\TestCase
{
    private Pulse $pulse;

    protected function setUp(): void
    {
        $this->pulse = new Pulse();
    }

    public function testBasicUsage()
    {
        // Dynamically add a new healthcheck
        $this->pulse->add('Test that this file exists', fn() => file_exists(__FILE__));

        // Add a healtcheck we created manually
        $healthcheck = new Healthcheck('description', fn() => true);
        $this->pulse->addHealthcheck($healthcheck);

        // Verify healthcheck aggregate is passing up to this point
        self::assertTrue($this->pulse->getStatus());

        // Add a failing healthcheck
        $this->pulse->add('falsy', function () {
            return false;
        });

        // Verify healthcheck aggregate is failing now
        self::assertFalse($this->pulse->getStatus());
    }

    /**
     * @uses \ilyaplot\pulse\Pulse
     */
    public function testTypes()
    {
        $this->pulse->addWarning('Test explicit warning', fn() => false);

        $this->pulse->addInfo('Output some info', fn() => 'Testing!');

        self::assertEquals(
            true,
            $this->pulse->getStatus(),
            'No critical failures, summary should pass'
        );

        $this->pulse->add('Test default (warning)', fn() => false);

        $this->pulse->addCritical('Test critical failure', fn() => false);

        // At this point we have one critical failure so the check should fail
        self::assertEquals(
            false,
            $this->pulse->getStatus(),
            'One critical failure, summary should fail'
        );


        $array = $this->pulse->getHealthChecks();

        self::assertEquals(Healthcheck::WARNING, $array[0]->getType());
        self::assertEquals(Healthcheck::INFO, $array[1]->getType());
        self::assertEquals(Healthcheck::CRITICAL, $array[2]->getType());
        self::assertEquals(Healthcheck::CRITICAL, $array[3]->getType());
    }
}
