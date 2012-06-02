<?php

/**
 * Abstraction of a Twilio resource.
 *
 * @category Services
 * @package  Services_Twilio
 * @author   Neuman Vong <neuman@twilio.com>
 * @license  http://creativecommons.org/licenses/MIT/ MIT
 * @link     http://pear.php.net/package/Services_Twilio
 */ 
abstract class Services_Twilio_Resource
{
    protected $name;
    protected $proxy;
    protected $subresources;

    public function __construct($resource, $uri)
    {
        $this->subresources = array();
        $this->name = get_class($this);
        $this->client = $resource;
        $this->uri = $uri;
        $this->init();
    }

    protected function init()
    {
        // Left empty for derived classes to implement
    }

    public function retrieveData($path, array $params = array())
    {
        return $this->client->retrieveData($path, $params);
    }

    public function deleteData($path, array $params = array())
    {
        return $this->client->deleteData($path, $params);
    }

    public function createData($path, array $params = array())
    {
        return $this->client->createData($path, $params);
    }

    public function getSubresources($name = null)
    {
        if (isset($name)) {
            return isset($this->subresources[$name])
                ? $this->subresources[$name]
                : null;
        }
        return $this->subresources;
    }

    public function addSubresource($name, Services_Twilio_Resource $res)
    {
        $this->subresources[$name] = $res;
    }

    protected function setupSubresources()
    {
        foreach (func_get_args() as $name) {
            $constantized = ucfirst(Services_Twilio_Resource::camelize($name));
            $type = "Services_Twilio_Rest_" . $constantized;
            $this->addSubresource($name, new $type($this, $this->uri . "/$constantized"));
        }
    }

    public static function decamelize($word)
    {
        return preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strtolower(strlen("\\1") ? "\\1_\\2" : "\\2")',
            $word
        );
    }

    public static function camelize($word)
    {
        return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $word);
    }
}

