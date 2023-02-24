<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

use Closure;

class Pulse
{
    private array $healthChecks = [];

    /**
     * Convenience function for adding simple healthchecks. Note: These default to
     * type Healthcheck::CRITICAL.
     *
     * @param string  $description A description of this check
     * @param Closure $healthcheck A callable that returns true when the check passes, false on failure
     */
    public function add(string $description, Closure $healthcheck): void
    {
        $this->addCritical($description, $healthcheck);
    }

    /**
     * Add a warning. If this healthcheck fails Pulse will respond with a 200, but will indicate errors.
     */
    public function addWarning(string $description, Closure $healthcheck): void
    {
        $this->healthChecks[] = new Healthcheck($description, $healthcheck, Healthcheck::WARNING);
    }

    /**
     * Add a critical healthcheck. If this healthcheck fails Pulse will respond with a 503.
     */
    public function addCritical(string $description, Closure $healthcheck): void
    {
        $this->healthChecks[] = new Healthcheck($description, $healthcheck, Healthcheck::CRITICAL);
    }

    /**
     * Add an informational message to the healthcheck list. The return value will be displayed vertabim.
     */
    public function addInfo(string $description, Closure $healthcheck): void
    {
        $this->healthChecks[] = new Healthcheck($description, $healthcheck, Healthcheck::INFO);
    }

    /**
     * Add an instance of healthcheck, useful if you want to subclass
     * the healthcheck class and add custom behavior.
     *
     * @param Healthcheck $healthcheck
     */
    public function addHealthcheck(Healthcheck $healthcheck): void
    {
        $this->healthChecks[] = $healthcheck;
    }

    /**
     * Evaluate all healthchecks and return a boolean based on the aggregate.
     *
     * @return bool true if all tests pass, false otherwise
     */
    public function getStatus(): bool
    {
        $status = true;

        foreach ($this->healthChecks as $healthcheck) {
            // Shortcut the rest if any check fails
            if ($status && $healthcheck->getType() === Healthcheck::CRITICAL) {
                $status = $status && $healthcheck->getStatus();
            }
        }

        return $status;
    }

    /**
     * @return array List of all healthchecks currently registered
     */
    public function getHealthChecks(): array
    {
        return $this->healthChecks;
    }

    /**
     * Evaluate all healthchecks and output a summary, using Formatter->autoexec()
     */
    public function check(): void
    {
        Formatter::autoexec($this);
    }
}
