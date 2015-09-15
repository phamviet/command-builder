<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Which
 * @package Phamviet\CommandBuilder\Command
 */
class Which extends Command
{
    /**
     * @param string $command
     */
    public function __construct($command)
    {
        parent::__construct();

        $this->arguments[0] = $command;
    }
}