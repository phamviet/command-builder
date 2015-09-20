<?php
/**
 */

require_once 'vendor/autoload.php';

// $which = new \Phamviet\CommandBuilder\Command\Which('git');
// $which->setEnvironment('PATH', '/usr/local/bin:/usr/bin');

// echo $which->run();
echo \Phamviet\CommandBuilder\Command\Composer::install('/Users/viet/bin');