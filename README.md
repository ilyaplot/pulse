# Pulse

Pulse allows you to easily write healthchecks for your application and display a simple, aggregated report so you can quickly diagnose whether and why your app is having trouble (or whether you can blame someone else). You can also monitor your healthchecks with [nagios](http://www.nagios.org/), [zabbix](http://www.zabbix.com/), etc.

![Packagist Version](https://img.shields.io/packagist/v/ilyaplot/pulse?label=release&style=plastic)
![GitHub last commit](https://img.shields.io/github/last-commit/ilyaplot/pulse?style=plastic)
[![Code Coverage](https://codecov.io/gh/ilyaplot/pulse/branch/master/graph/badge.svg)](https://codecov.io/gh/ilyaplot/pulse)
[![Psalm Level](https://shepherd.dev/github/ilyaplot/pulse/level.svg)](https://shepherd.dev/github/ilyaplot/pulse)
[![Type Coverage](https://shepherd.dev/github/ilyaplot/pulse/coverage.svg)](https://shepherd.dev/github/ilyaplot/pulse)
[![Static Analysis](https://github.com/ilyaplot/pulse/workflows/static%20analysis/badge.svg)](https://github.com/ilyaplot/pulse/actions?query=workflow%3A%22static+analysis%22)
[![Unit Tests](https://github.com/ilyaplot/pulse/workflows/tests/badge.svg)](https://github.com/ilyaplot/pulse/actions?query=workflow%3A%22tests%22)
[![Style CI](https://github.styleci.io/repos/605941101/shield?style=plastic)](https://github.styleci.io/repos/605941101)
![PHP Version](https://img.shields.io/packagist/dependency-v/ilyaplot/pulse/php?style=plastic)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/ilyaplot/pulse?style=plastic)

#### Wait, what's a healthcheck?

Healthchecks are a great way to test system health and connectivity to other services. For example, you can verify connectivity to memcache or mysql, that your app can read / write to certain files, or that your API key for a third-party service is still working.

## Installation

You can install this into your project using [composer](http://getcomposer.org/doc/00-intro.md#installation-nix). Create a `composer.json` file in the root of your project and add the following:

```
composer require ilyaplot/pulse
```

Include `vendor/autoload.php`, and you're off to the races!

#### Warnings

For non-critical checks you can use a warning and you'll get status 200 even if these fail. Use these to see when your app is experiencing service degredation but is still available. Warning checks must return boolean `true` or `false`.

```php
$pulse->addWarning(new ClosureRule(
    fn() => (new YoutubeClient())->->isUp(),
    "Verify connectivity to youtube",
    LevelEnum::warning,
));
```

#### Information

```php
$pulse->addInfo(new ClosureRule(
    fn() => (new YoutubeClient())->->isUp(),
    "Verify connectivity to youtube", 
));

$pulse->addInfo(new ClosureRule(
    function(ClosureRule $closureRule) {
        $this->setErrorMessage( date('l'));
        return false;
    }, 
    "Today is",
));

$result = $pulse->run();
```

#### Custom Rules

You can also create your own custom rules by extending the `ilyaplot\pulse\rules\AbstractRule` class or implementing `ilyaplot\pulse\rules\RuleInterface`.
For example, you could create a rule that checks that your app can connect to a third-party service.

```php
class YoutubeRule extends AbstractRule
{
    public function __construct(
        private readonly string $apiKey,
    ) {
        $this->description = 'Verify connectivity to youtube';
        $this->level = LevelEnum::warning;
    }
    
    public function run(): bool
    {
        $youtubeClient = new YoutubeClient($this->apiKey);
        try {
            return $youtubeClient->isUp(); // bool
        } catch (AuthenticationException) {
            $this->setErrorMessage('Invalid API key');
            return false;
        }
    }
}
```

Then you can add it to your healthcheck:

```php
$pulse->add(new YoutubeRule('your-api-key-1'));
$pulse->add(new YoutubeRule('your-api-key-2'));
```

#### Examples

You can see some very basic example healthchecks in `examples/cli-usage.php` and `examples/http-usage.php`.

```php
$pulse = new ilyaplot\pulse\Pulse();

$pulse->add(new FileRule(
    '/path/to/your/config/file',
    description: "Check that config file is readable",
    checkIsReadable: true,
));

include '/path/to/your/config/file';

$pulse->addCritical(new ClosureRule(
    function() use ($config) {
        $memcache = new Memcache();
        if(!$memcache->connect($config['memcache_host'], $config['memcache_port'])){
            return false;
        }
        $key = 'healthcheck_test_key'
        $msg = 'memcache is working';
        $memcache->set($key, $msg);
        return $memcache->get($key) === $msg;
    }, 
    "Check memcache connectivity"
));
```

## Does Pulse Work With X?

Yep. Pulse is designed to be self-contained and is very simple, so it doesn't require you to use any particular framework. You are free to include other things like yml parsers, etc. if you choose, but we recommend NOT including a full framework stack on top of it. If the framework fails to load for some reason, your healthchecks won't be displayed, meaning they're not useful for diagnosing whatever problem you've encountered.

## Won't This Expose Information About My App?

Potentially. You *probably* don't want to display the healthcheck results to the public. Instead, you could [whitelist certain IPs](http://httpd.apache.org/docs/2.2/howto/access.html).
