# Pulse

Pulse allows you to easily write healthchecks for your application and display a simple, aggregated report so you can quickly diagnose whether and why your app is having trouble (or whether you can blame someone else). You can also monitor your healthchecks with [nagios](http://www.nagios.org/), [zabbix](http://www.zabbix.com/), etc.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/validator/v/stable.png)](https://packagist.org/packages/ilyaplot/pulse)
[![Total Downloads](https://poser.pugx.org/ilyaplot/pulse/downloads.png)](https://packagist.org/packages/ilyaplot/pulse)
[![Code Coverage](https://codecov.io/gh/ilyaplot/pulse/branch/master/graph/badge.svg)](https://codecov.io/gh/ilyaplot/pulse)
[![type-coverage](https://shepherd.dev/github/ilyaplot/pulse/coverage.svg)](https://shepherd.dev/github/ilyaplot/pulse)
[![static analysis](https://github.com/ilyaplot/pulse/workflows/static%20analysis/badge.svg)](https://github.com/ilyaplot/pulse/actions?query=workflow%3A%22static+analysis%22)
[![tests](https://github.com/ilyaplot/pulse/workflows/tests/badge.svg)](https://github.com/ilyaplot/pulse/actions?query=workflow%3A%22tests%22)
[![psalm-level](https://shepherd.dev/github/ilyaplot/pulse/level.svg)](https://shepherd.dev/github/ilyaplot/pulse)
[![StyleCI](https://github.styleci.io/repos/605941101/shield?style=plastic)](https://github.styleci.io/repos/605941101)
#### Wait, what's a healthcheck?

Healthchecks are a great way to test system health and connectivity to other services. For example, you can verify connectivity to memcache or mysql, that your app can read / write to certain files, or that your API key for a third-party service is still working.

## Installation

You can install this into your project using [composer](http://getcomposer.org/doc/00-intro.md#installation-nix). Create a `composer.json` file in the root of your project and add the following:

```
composer require ilyaplot/pulse
```

Include `vendor/autoload.php`, and you're off to the races!

See examples in `examples`.

#### Examples

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

#### Warnings

For non-critical checks you can use a warning and you'll get status 200 even if these fail. Use these to see when your app is experiencing service degredation but is still available. Warning checks must return boolean `true` or `false`.

```php
$pulse->addWarning(new ClosureRule(
    fn() => (new YoutubeClient())->->isUp(),
    "Verify connectivity to youtube",
    LevelEnum::warning,
);
```

#### Information

```php
$pulse->addInfo(new ClosureRule(
    fn() => (new YoutubeClient())->->isUp(),
    "Verify connectivity to youtube", 
);

$pulse->addInfo(new ClosureRule(
    function(ClosureRule $closureRule) {
        $this->setErrorMessage( date('l'));
        return false;
    }, 
    "Today is",
));

$result = $pulse->run();
```

## Examples

You can see some very basic example healthchecks in `examples/cli-usage.php` and `examples/http-usage.php`.

## Does Pulse Work With X?

Yep. Pulse is designed to be self-contained and is very simple, so it doesn't require you to use any particular framework. You are free to include other things like yml parsers, etc. if you choose, but we recommend NOT including a full framework stack on top of it. If the framework fails to load for some reason, your healthchecks won't be displayed, meaning they're not useful for diagnosing whatever problem you've encountered.

## Won't This Expose Information About My App?

Potentially. You *probably* don't want to display the healthcheck results to the public. Instead, you could [whitelist certain IPs](http://httpd.apache.org/docs/2.2/howto/access.html).
