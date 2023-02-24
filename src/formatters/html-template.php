<?php //phpcs:disable

declare(strict_types=1);

namespace ilyaplot\pulse\formatters\Html;

use ilyaplot\pulse\formatters\HtmlFormatter;
use ilyaplot\pulse\ResultDto;

/**
 * @var ResultDto $resultDto
 * @var HtmlFormatter $formatter
 */
?>
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

        ul {
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

        #footer > p:first-of-type {
            font-size: .9em;
        }

        p {
            color: #777;
            margin: 2px 0;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <ul>
        <?php foreach ($resultDto->rules as $result) : ?>
            <li class="healthcheck <?= $formatter->getLevelClass($result->level) ?> <?= $result->isSuccess ? 'pass' : 'fail' ?>">
                <?= htmlentities($result->description) ?>
                (<?= $formatter->getLevelString($result->level) ?>):
                <strong><?= $result->isSuccess ? 'PASS' : 'FAIL' ?></strong>
                <?= $result->errorMessage ? 'with error: ' . htmlentities($result->errorMessage) : '' ?>
            </li>
        <?php endforeach; ?>
        <li class="summary <?= $resultDto->isSuccess ? 'pass' : 'fail' ?>">
            Healthcheck summary: <?= $resultDto->isSuccess ? 'PASS' : 'FAIL' ?>
        </li>
    </ul>
    <div id="footer">
        <p>Created at: <?=date('d.m.Y h:i:s')?>.</p>
    </div>
</div>
</body>
</html>
