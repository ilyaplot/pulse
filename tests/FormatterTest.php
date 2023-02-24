<?php

declare(strict_types=1);

use ilyaplot\pulse\Formatter as Formatter;
use ilyaplot\pulse\Pulse as Pulse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ilyaplot\pulse\Formatter
 * @uses   \ilyaplot\pulse\Pulse
 */
class FormatterTest extends TestCase
{
    private Pulse $pulseFail;
    private Pulse $pulseSuccess;

    protected function setUp(): void
    {
        $this->pulseFail = new Pulse();

        $this->pulseFail->addInfo('Description', function () {
            return 'this is some data';
        });
        $this->pulseFail->addWarning('This is a warning', function () {
            return false;
        });
        $this->pulseFail->add('This test should pass', function () {
            return true;
        });
        $this->pulseFail->add('This test should fail', function () {
            return false;
        });

        $this->pulseSuccess = new Pulse();

        $this->pulseSuccess->addWarning('This test should fail', function () {
            return false;
        });
        $this->pulseSuccess->add('This test should pass', function () {
            return true;
        });
        $this->pulseSuccess->add('This test should also pass', function () {
            return true;
        });
    }

    public function testToJsonFailure()
    {
        $expected = '{"all-passing":false,"healthchecks":[{"description":"Description","type":"info","data":"this is some data"},{"description":"This is a warning","type":"warning","passing":false},{"description":"This test should pass","type":"critical","passing":true},{"description":"This test should fail","type":"critical","passing":false}]}';

        self::assertEquals($expected, Formatter::toJson($this->pulseFail));
    }

    public function testToJsonSuccess()
    {
        $expected = '{"all-passing":true,"healthchecks":[{"description":"This test should fail","type":"warning","passing":false},{"description":"This test should pass","type":"critical","passing":true},{"description":"This test should also pass","type":"critical","passing":true}]}';

        self::assertEquals($expected, Formatter::toJson($this->pulseSuccess));
    }

    public function testToHtml()
    {
        $expected = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        margin: 0;
        background-color: #888;
        font-family: sans-serif;
        color: #000;
    }
    #wrapper {
        max-width: 40em;
        background-color: #fff;
        padding: 20px;
        margin: 20px auto;
        border-radius: 2px;
    }
    ul{
        margin: 0;
        list-style: none;
        padding: 0;
    }
    li {
        margin: 0 0 3px 0;
        padding: 8px;
        background-color: #ccc;
        border-radius: 2px;
    }
    .pass {
        background-color: #7dcd5f;
    }
    .critical.fail, .summary.fail {
        background-color: #ff65a8;
    }
    .warning.fail {
        background-color: #ffbbe2;
    }
    .summary {
        margin: 30px 0;
        font-weight: bold;
    }
    #footer>p:first-of-type {
        font-size: .9em;
    }
    p{
        color: #777;
        margin: 2px 0;
    }
    </style>
</head>
<body>
    <div id="wrapper">
        <ul>
            <li class="healthcheck info">Description: <b>this is some data</b></li>
            <li class="healthcheck warning fail">This is a warning: <b>fail</b></li>
            <li class="healthcheck critical pass">This test should pass: <b>pass</b></li>
            <li class="healthcheck critical fail">This test should fail: <b>fail</b></li>

            <li class="summary fail">Healthcheck summary: fail</li>
        </ul>
        <div id="footer">
            <p>This healthcheck page can also be accessed in machine-readable formats via CURL:</p>
            <p><code>$ curl http://example.com/healthcheck.php #plaintext</code></p>
            <p><code>$ curl -H "Accept: application/json" http://example.com/healthcheck.php</code></p>
        </div>
    </div>
</body>
HTML;

        self::assertEquals($expected, Formatter::toHtml($this->pulseFail));
    }

    public function testToPlainFailure()
    {
        $expected = <<<TEXT
Description (info): this is some data
This is a warning (warning): fail
This test should pass (critical): pass
This test should fail (critical): fail

Healthcheck summary: fail

TEXT;

        self::assertEquals($expected, Formatter::toPlain($this->pulseFail));
    }

    public function testToPlainSuccess()
    {
        $expected = <<<TEXT
This test should fail (warning): fail
This test should pass (critical): pass
This test should also pass (critical): pass

Healthcheck summary: pass

TEXT;

        self::assertEquals($expected, Formatter::toPlain($this->pulseSuccess));
    }

    public function testIsBrowser()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Android 4.0.4; Linux; Opera Mobi/ADR-1301080958) Presto/2.11.355 Version/12.10';
        self::assertTrue(Formatter::isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Java/1.6.0_22';
        self::assertFalse(Formatter::isBrowser());

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; nl-NL) AppleWebKit/533.19.4 (KHTML, like Gecko) AdobeAIR/3.1';
        self::assertTrue(Formatter::isBrowser());

        unset($_SERVER['HTTP_USER_AGENT']);
        self::assertFalse(Formatter::isBrowser());
    }

    public function testAcceptsJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        self::assertFalse(Formatter::acceptsJson());

        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        self::assertTrue(Formatter::acceptsJson());

        // Technically this is not compliant, but we'll accept it anyway.
        $_SERVER['HTTP_ACCEPT'] = 'text/html; Application/JSON';
        self::assertTrue(Formatter::acceptsJson());

        unset($_SERVER['HTTP_ACCEPT']);
        self::assertFalse(Formatter::acceptsJson());
    }
}
