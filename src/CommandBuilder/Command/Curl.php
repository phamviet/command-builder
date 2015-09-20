<?php

namespace Phamviet\CommandBuilder\Command;

use Phamviet\CommandBuilder\Command;

/**
 * Class Curl
 * @package Phamviet\CommandBuilder\Command
 */
class Curl extends Command
{
    /**
     * @param string $url
     */
    public function __construct($url = '', $path = '')
    {
        parent::__construct($path);

        if ($url) {
            $this->setUrl($url);
        }
    }

    public function setUrl($url)
    {
        $this->arguments[0] = $url;

        return $this;
    }
}