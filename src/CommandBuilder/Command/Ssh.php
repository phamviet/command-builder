<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Ssh
 * @package Phamviet\CommandBuilder\Command
 */
class Ssh extends Command
{
    /**
     * @param $value
     *
     * @return $this
     */
    public function setHost($value)
    {
        $this->arguments[0] = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCommand($value)
    {
        $this->arguments[1] = $value;

        return $this;
    }

    /**
     * @param $value
     */
    public function setIdentity($value)
    {
        $this->setOption('i', $value);
    }

    /**
     * @param $values
     */
    public function setOptions($values)
    {
        foreach ($values as $name => $value) {
            $this->addOption('o', $name . '=' . $value);
        }
    }
}