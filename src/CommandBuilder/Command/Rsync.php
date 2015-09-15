<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Rsync
 * @package Phamviet\CommandBuilder\Command
 */
class Rsync extends Command
{
    /**
     * @param Ssh $ssh
     *
     * @return $this
     */
    public function setSsh(Ssh $ssh)
    {
        return $this->setOption('e', $ssh);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setSource($value)
    {
        $this->arguments[0] = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTarget($value)
    {
        $this->arguments[1] = $value;

        return $this;
    }
}