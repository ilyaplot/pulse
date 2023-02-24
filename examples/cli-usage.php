<?php

declare(strict_types=1);

use ilyaplot\pulse\formatters\PlainTextFormatter;
use ilyaplot\pulse\Pulse;
use ilyaplot\pulse\rules\ClosureRule;
use ilyaplot\pulse\rules\RuleInterface;

ini_set('display_errors', '1');

require __DIR__ . '/../vendor/autoload.php';

$pulse = new Pulse();

$databaseRule = new ClosureRule(
    function (RuleInterface $rule) {
        try {
            $connection = new PDO('mysql://localhost:port/my_db', 'root', 'root');
            $connectionStatus = $connection->getAttribute(PDO::ATTR_CONNECTION_STATUS);

            if ('Connection OK; waiting to send.' !== $connection->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
                $rule->setErrorMessage(json_encode($connectionStatus));
                return false;
            }
        } catch (PDOException $exception) {
            $rule->setErrorMessage($exception->getMessage());
            return false;
        }

        return true;
    },
    'Connection to database',
);

$pulse->add($databaseRule);

$result = $pulse->run();
$plainText = (new PlainTextFormatter())->format($result);

echo $plainText;

return $result->isSuccess ? 0 : 1;

/**
 * Output:
Connection to database (CRITICAL): FAIL with message: SQLSTATE[HY000] [2002] No such file or directory

Healthcheck summary: FAIL

 */
