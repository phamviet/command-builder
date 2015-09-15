<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Git
 * @package Phamviet\CommandBuilder\Command
 */
class Git extends Command
{
    /**
     * @param string $task
     * @param string $path
     */
    public function __construct($task, $path = '')
    {
        parent::__construct($path);

        $this->arguments[0] = $task;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setRepository($url)
    {
        $this->arguments[1] = $url;

        return $this;
    }

    /**
     * @param $path
     *
     * @return $this
     */
    public function cloneTo($path)
    {
        $this->arguments[3] = $path;

        return $this;
    }
}