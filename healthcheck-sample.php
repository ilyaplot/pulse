<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

$pulse = new ilyaplot\pulse\Pulse();

$pulse->add('This is a critical check that is passing', function() {
    return true;
});

$pulse->addCritical('This is a critical-level failure', function() {
    return false;
});

$pulse->addWarning('This is a warning-level failure', function() {
    return false;
});

$pulse->addInfo('This is an info message', function() {
    return 'Some info!';
});

$pulse->check();
