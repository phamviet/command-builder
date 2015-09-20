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
    protected $pipe;

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
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->path = Command\Which::where($this->getName());
        }

        return $this->path;
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
     * @param string|Command $pipe
     */
    public function pipe($pipe)
    {
        $this->pipe = $pipe;
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
        $environments = [];
        foreach ($this->environments as $name => $value) {
            $environments[] = $name . '=' . $this->escapeValue($value);
        }

        return $environments;
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

        if ($this->pipe) {
            $command .= ' | ' . (string)$this->pipe;
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
     * @param null|[] $env
     * @param null|int $timeout
     *
     * @return string
     */
    public function run($cwd = null, $env = null, $timeout = null)
    {
        $env = array_merge($this->environments, (array)$env);
        $this->environments = [];

        $process = new Process($this->toString(), $cwd);
        $process->setTimeout($timeout);

        if ($env) {
            $environments = [];
            foreach ($env as $name => $value) {
                $environments[$name] = $this->escapeValue($value);
            }

            $process->setEnv($environments);
        }

        $process->mustRun();

        return $process->getOutput();
    }
}