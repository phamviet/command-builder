<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Mysql
 * @package Phamviet\CommandBuilder\Command
 */
class Mysql extends Command
{
    /**
     * @param $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        return $this->setOption('u', $user);
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->arguments[0] = '-p' . $password;

        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setDatabase($name)
    {
        $this->arguments[1] = $name;

        return $this;
    }
}