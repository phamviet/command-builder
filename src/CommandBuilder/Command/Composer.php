<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Composer
 * @package Phamviet\CommandBuilder\Command
 */
class Composer extends Command
{
    /**
     * @param $pwd
     *
     * @return string
     */
    public static function install($pwd)
    {
        $curl = new Curl('https://getcomposer.org/installer');
        $curl
            ->setOption('sS')
            ->setEnvironment('COMPOSER_HOME', $pwd);

        $curl->pipe('php');

        return $curl->run($pwd);
    }
}