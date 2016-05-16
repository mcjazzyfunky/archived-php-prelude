<?php

namespace prelude\cfg;

require_once dirname(__FILE__) . '/ConfigException.class.php';

class Config {
    const REQ = '#MConfig::REQ#';

    private $data;

    public function __construct($data = null) {
        $this->data = $data;
    }

    public static function fromString($value) {
        $ret = null;
        $yaml = Yaml::decode($value);
        $ret = new self($yaml);
        return $ret;
    }
    
    public static function fromYmlFile($fileName) {
        $ret = null;
        $s = file_get_contents($fileName);
        
        if (strlen($s) == 0) {
            throw new ConfigException('Could not read configuration!');
        }

        if (!class_exists('SfYaml')) {
            require_once dirname(__FILE__) . '/../../third-party/sfyaml/sfYaml.php';
        }
        
        $yaml = sfYaml::load($s);
        $ret = new self($yaml);
        return $ret;
    }

    public static function fromPhpFile($fileName, $cfgArrayName = null) {
        $ret = null;

        if (!empty($cfgArrayName)) {
            $$cfgArrayName = null;
        }

        $cfg = include($fileName);

        if (!empty($cfgArrayName)) {
            $cfg = $$cfgArrayName;
        }

        if (!is_array($cfg)) {
            throw new ConfigException("Could not read configuration from PHP file '$fileName'!");
        }
        
        $ret = new self($cfg);
        return $ret;
    }

    function get($path, $default = self::REQ) {
        $ret = null;
        $tokens = explode('.', $path);
        $tokenCount = count($tokens);
        $isSet = false;
        $parent = $this->data;

        for ($i = 0; $i < $tokenCount && is_array($parent); ++$i) {
             $token = $tokens[$i];
             if (isset($parent[$token])) {
                    if ($i == $tokenCount - 1) {
                        $ret = $parent[$token];
                        $isSet = true;
                    } else {
                        $parent = $parent[$token];
                    }
                } else {
                    $parent = null;
                }
        }

        if (!$isSet && $default === self::REQ) {
            throw new ConfigException("Mandatory configuration parameter '" . $path . "' is not set!");
        } else if (!$isSet) {
            $ret = $default;
        }


        return $ret;
    }

    function getString($path, $default = self::REQ) {
        $ret = $this->get($path, $default);

        if ($ret === false) {
            $ret = '0';
        } elseif (is_scalar($ret)) {
            $ret = (string)$ret;
        } elseif ($ret !== null) {
            throw new ConfigException("Configuration parameter '" . $path . "' should be a string!");
        }

        return $ret;
    }

    function getStringMatchingRegex($path, $regex, $default = self::REQ) {
        $ret = $this->get($path, $default);
        
        if (is_scalar($ret)) {
            $ret = (string)$ret;
        }
        
        if (!is_scalar($ret) || !preg_match($regex, $ret)) {
            throw new ConfigException("Configuration parameter '$path' does not match the regular expression $regex!");            
        }
        
        return $ret;
    }

    function getTrimmedString($path, $default = self::REQ) {
        $ret = trim($this->getString($path, $default));
        return $ret;
    }
    
    
    function getNumber($path, $default = self::REQ) {
        $ret = $this->get($path, $default);

        if ($default !== self::REQ && ($ret === null || trim($ret) === '')) {
            $ret = $default;
        }
        
        if (!is_numeric($ret) && $ret !== $default) {
            throw new ConfigException("Configuration parameter '" . $path . "' should be a number!");
        }

         return $ret;
    }

    function getInteger($path, $default = self::REQ) {
        $ret = $this->getNumber($path, $default);

        if ($ret !== null) {
            $ret = (integer)$ret;
        }

        return $ret;
    }

    function getFloat($path, $default = self::REQ) {
        $ret = $this->getNumber($path, $default);

        if ($ret !== null) {
            $ret = (float)$ret;
        }

        return $ret;
    }

    function getBoolean($path, $default = self::REQ) {
        $ret = $this->get($path, $default);

        if (is_string($ret)) {
            $lower = strtolower($ret);

            if ($lower === 'true' || $lower === 'yes' || $lower = 'on') {
                $ret = true;
            } else {
                 $ret = (boolean)$ret;
            }
        }

        return $ret;
    }
											 
    function getArray($path, $default = self::REQ) {
        $path = trim($path);

        if ($path === '') {
                $ret = $this->data;
        } else {
            $ret = $this->get($path, $default);

            if ($ret !== null && !is_array($ret)) {
                throw new ConfigException("Configuration parameter '" . $path . "' should be an array!");
            }
        }
	
        return $ret;
    }

    function getDir($path, $default = self::REQ) {
        $ret = $this->getString($path, $default);

        if (!is_dir($ret)) {
            throw new CongifException("Configuration parameter '" . $path . "' should be an existing directory!");
        }

        return $ret;
    }

    function getConfig($path) {
        $ret = new self($this->getArray($path));
        return $ret;
    }
}
