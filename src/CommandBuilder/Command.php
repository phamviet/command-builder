<?php
namespace Phamviet\CommandBuilder;

use Symfony\Component\Process\Process;

/**
 * Class Command
 * @package Phamviet\CommandBuilder
 */
abstract class Command
{
    protected $options      = [];
    protected $arguments    = [];
    protected $environments = [];

    protected $fromFile;
    protected $outputToFile;

    protected $path;

    /**
     * @param $path
     */
    public function __construct($path = '')
    {
        if ($path) {
            $this->setPath($path);
        }
    }

    /**
     * @param $path
     *
     * @return $this
     * @throws \Exception
     */
    public function setPath($path)
    {
        if (file_exists($path)) {
            $this->path = $path;
        } else {
            throw new \Exception(sprintf("Command '%s' does not exist", $path));
        }

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function addArgument($value)
    {
        $this->arguments[] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return Command
     */
    public function setEnvironment($name, $value)
    {
        $this->environments[strtoupper($name)] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return Command
     */
    public function setLongOption($name, $value)
    {
        return $this->setOption($name, $value, '--');
    }

    /**
     * @param $name
     * @param $value
     * @param string $optionPrefix
     *
     * @return $this
     */
    public function setOption($name, $value = '', $optionPrefix = '-')
    {
        $name                 = $optionPrefix . $name;
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $fromFile
     */
    public function setFromFile($fromFile)
    {
        $this->fromFile = $fromFile;
    }

    /**
     * @param string $filename
     */
    public function outputToFile($filename)
    {
        $this->outputToFile = $filename;
    }


    /**
     * @param $name
     * @param $value
     * @param string $prefix
     *
     * @return $this
     */
    public function addOption($name, $value, $prefix = '-')
    {
        $name = $prefix . $name;

        if (isset($this->options[$name])) {
            $this->options[$name]   = [$this->options[$name]];
            $this->options[$name][] = $value;
        } else {
            $this->options[$name] = $value;
        }

        return $this;
    }

    /**
     * @param $options
     * @param null $optionName
     *
     * @return string
     */
    protected function buildOptions($options, $optionName = null)
    {
        $opts = [];
        foreach ($options as $name => $value) {

            if (is_array($value)) {
                $opts[] = $this->buildOptions($value, $name);
            } else {
                $value = $this->escapeValue($value);
                if (0 === strpos($name, '--')) {
                    $opts[] = $name . '=' . $value;
                } else {
                    $opts[] = ($optionName ? $optionName : $name) . ' ' . $value;
                }
            }
        }

        return implode(' ', $opts);
    }

    /**
     * @return string
     */
    protected function getEnvironments()
    {
        $ennvs = [];
        foreach ($this->environments as $name => $value) {
            $ennvs[] = $name . '=' . $this->escapeValue($value);
        }

        return $ennvs;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function escapeValue($value)
    {
        if ($value instanceof Command) {
            $value = "'{$value}'";
        }

        return (string)$value;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $command = implode(' ', array_merge(
                $this->getEnvironments(),
                [
                    $this->getName(),
                    $this->buildOptions($this->options)
                ],
                array_map("escapeshellarg", $this->arguments)
            )
        );

        if ($this->outputToFile) {
            $command .= ' > ' . $this->outputToFile;
        } elseif ($this->fromFile) {
            $command .= ' < ' . $this->fromFile;
        }

        return $command;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function getName()
    {
        $classPieces = explode('\\', get_called_class());

        return $this->path ?: strtolower(end($classPieces));
    }

    /**
     * @param string $cwd
     * @param null|int $timeout
     *
     * @return string
     */
    public function run($cwd = null, $timeout = null)
    {
        $process = new Process($this, $cwd);
        $process->setTimeout($timeout);

        if ($this->environments) {
            $ennvs = [];
            foreach ($this->environments as $name => $value) {
                $ennvs[$name] = $this->escapeValue($value);
            }

            $process->setEnv($ennvs);
        }

        $process->mustRun();

        return $process->getOutput();
    }
}