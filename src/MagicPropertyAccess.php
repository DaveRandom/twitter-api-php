<?php

namespace TwitterAPI;

trait MagicPropertyAccess
{
    /**
     * @var bool[]
     */
    private $MagicPropertyAccess_writableProperties = [];

    /**
     * @var bool[]
     */
    private $MagicPropertyAccess_readableProperties = [];

    /**
     * @param string[] $public
     * @param string[] $readOnly
     */
    final protected function defineMagicProperties(array $public = null, array $readOnly = null)
    {
        foreach ((array)$public as $propName) {
            $this->MagicPropertyAccess_readableProperties[$propName] = true;
            $this->MagicPropertyAccess_writableProperties[$propName] = true;
        }

        foreach ((array)$readOnly as $propName) {
            $this->MagicPropertyAccess_readableProperties[$propName] = true;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $accessor = [$this, 'get' . $name];

        if (in_array($name, $this->MagicPropertyAccess_readableProperties) && is_callable($accessor)) {
            return call_user_func($accessor);
        }

        throw new \LogicException('Undefined property: ' . __CLASS__ . '::' . $name);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $accessor = [$this, 'get' . $name];

        if (in_array($name, $this->MagicPropertyAccess_writableProperties) && is_callable($accessor)) {
            call_user_func($accessor, $value);
        }

        if (in_array($name, $this->MagicPropertyAccess_readableProperties) && is_callable($accessor)) {
            throw new \LogicException('Cannot write to read-only property: ' . __CLASS__ . '::' . $name);
        }

        throw new \LogicException('Undefined property: ' . __CLASS__ . '::' . $name);
    }
}
