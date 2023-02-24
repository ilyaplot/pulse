<?php

declare(strict_types=1);

use ilyaplot\pulse\formatters\HtmlFormatter;
use ilyaplot\pulse\Pulse;
use ilyaplot\pulse\rules\ClosureRule;
use ilyaplot\pulse\rules\FileRule;

ini_set('display_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

$pulse = new Pulse();

$directoryExistsRule = new FileRule(
    fileName: __FILE__,
    description: 'Directory exists',
    checkIsDirectory: true,
);

$fileExistsRule = new FileRule(
    fileName: __FILE__,
    description: 'File exists',
);

$customRule = new ClosureRule(
    fn() => false,
    'Custom rule',
);

$pulse->add($fileExistsRule);
$pulse->addWarning($directoryExistsRule);
$pulse->addCritical($customRule);

$result = $pulse->run();
$html = (new HtmlFormatter())->format($result);

http_response_code($result->isSuccess ? 200 : 503);

/**
 * @see example-output.html
 */
echo $html . PHP_EOL;

// Or you may return the result as JSON:

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;

/*
 *  Output:
{
    "isSuccess": false,
    "rules": [
        {
            "isSuccess": true,
            "description": "File exists",
            "level": "critical",
            "errorMessage": null
        },
        {
            "isSuccess": false,
            "description": "Directory exists",
            "level": "warning",
            "errorMessage": "\/Users\/plotnikov\/workspace\/pulse\/examples\/http-usage.php is not a directory"
        },
        {
            "isSuccess": false,
            "description": "Custom rule",
            "level": "critical",
            "errorMessage": null
        }
    ]
}
 */
