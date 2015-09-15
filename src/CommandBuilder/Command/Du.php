<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Du
 * @package Phamviet\CommandBuilder\Command
 */
class Du extends Command
{
    /**
     * @param string $dir
     */
    public function __construct($dir = '')
    {
        parent::__construct();

        if ($dir) {
            $this->arguments[0] = $dir;
        }
    }

    /**
     * @param $dir
     *
     * @return string
     */
    public static function getSize($dir)
    {
        $du = new self($dir);
        $du->setOption('sh');

        return $du->run();
    }
}