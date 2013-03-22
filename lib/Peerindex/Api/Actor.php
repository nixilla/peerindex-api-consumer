<?php

namespace Peerindex\Api;

class Actor implements \ArrayAccess
{
    private $container = array();

    const TWITTER_SCREEN_NAME = 'twitter_screen_name';
    const PEERINDEX_ID = 'peerindex_id';
    const TWITTER_ID = 'twitter_id';

    public static function getInstance(array $input)
    {
        $instance = new self;

        $instance->container = $input;

        if(! isset($instance->container[self::TWITTER_ID]))
            if(isset($input['twitter']['id']))
                $instance->container[self::TWITTER_ID] = $input['twitter']['id'];

        return $instance;
    }

    public function getPeerindex()
    {
        return isset($this->container['peerindex']) ? $this->container['peerindex'] : null;
    }

    public function getTopics()
    {
        return isset($this->container['topics']) ? $this->container['topics'] : null;
    }

    public function getGraph()
    {
        return isset($this->container['graph']) ? $this->container['graph'] : null;
    }

    public function getId($type = self::PEERINDEX_ID)
    {
        return isset($this->container[$type]) ? $this->container[$type] : null;
    }

    public function isLoaded()
    {
        return
            isset($this->container['peerindex']) &&
            isset($this->container['topics']) &&
            isset($this->container['graph']);
    }

    public function toArray()
    {
        return $this->container;
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Read-only object');
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Read-only object');
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
