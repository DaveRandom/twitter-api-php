<?php

namespace TwitterAPI\HTTP;

use TwitterAPI\MagicPropertyAccess;

/**
 * @property-read string $name
 * @property string[]|\ArrayObject $values
 */
class Header implements \IteratorAggregate, \Countable
{
    use MagicPropertyAccess;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \ArrayObject
     */
    private $values;

    /**
     * @param $name
     * @param string|string[] $values
     */
    public function __construct($name, $values = [])
    {
        $this->defineMagicProperties($public = ['values'], $readOnly = ['name']);

        $this->name = trim((string)$name);
        $this->setValues($values);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = '';

        foreach ($this->values as $value) {
            $str .= "{$this->name}: {$value}\r\n";
        }

        return $str;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \ArrayObject
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string|string[] $values
     */
    public function setValues($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $this->values = new \ArrayObject;
        foreach ($values as $value) {
            $this->values[] = trim((string)$value);
        }
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function hasValue($needle)
    {
        foreach ($this->values as $value) {
            if ($value === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $value
     */
    public function addValue($value)
    {
        $this->values[] = trim((string)$value);
    }

    /**
     * @param string $needle
     */
    public function removeValue($needle)
    {
        foreach ($this->values as $key => $value) {
            if ($value === $needle) {
                unset($this->values[$needle]);
                return;
            }
        }

        throw new \LogicException('Cannot remove non-existent value: ' . $needle);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->values->getIterator();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->values->count();
    }
}
