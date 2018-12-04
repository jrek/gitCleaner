<?php

namespace App\Helpers;

use RuntimeException;

class ConfigHelper
{
    /** @var array */
    protected $defaultConfig = [];

    /** @var array */
    protected $config = [];

    /** @var string */
    private $defaultConfigFilePath;

    /** @var string */
    private $configFilePath;

    public function __construct(string $defaultConfigFilePath, string $configFilePath)
    {
        $this->defaultConfigFilePath = $defaultConfigFilePath;
        $this->configFilePath = $configFilePath;
    }

    public function loadConfigs()
    {
        if (empty($this->defaultConfigFilePath)
            || !is_readable($this->defaultConfigFilePath)
        ) {
            throw new RuntimeException('Cannot read default config');
        }


        $this->defaultConfig = include $this->defaultConfigFilePath;
        if (!empty($this->configFilePath)
            && is_readable($this->configFilePath)
        ) {
            $this->config = include $this->configFilePath;
        }

        if (!is_array($this->config)) {
            $this->config = [];
        }

        if (empty($this->defaultConfig) || !is_array($this->defaultConfig)) {
            throw new RuntimeException("Default config cannot be empty");
        }
    }

    public function get($key, $default = false)
    {
        if ($default === false && array_key_exists($key, $this->config)){
            return $this->config[$key];
        }
        if (array_key_exists($key, $this->defaultConfig)) {
            return $this->defaultConfig[$key];
        }

        return null;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function save()
    {
        $fh = fopen($this->configFilePath, 'w');
        fwrite($fh, '<?php'.PHP_EOL.'return ');
        fwrite($fh, var_export($this->config, true));
        fwrite($fh, ';');
        fclose($fh);
    }

    public function listKeys($defaultOnly = false) {
        $keys = array_keys($this->defaultConfig);
        if ($defaultOnly === false) {
            $keys = array_merge(array_keys($this->config));
        }

        return $keys;
    }
}