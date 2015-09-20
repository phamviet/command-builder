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

    /**
     * @param string $command
     * @param null|[] $env
     *
     * @return string
     */
    public static function where($command, $env = null)
    {
        $which = new self($command);

        return $which->run(null, $env);
    }
}