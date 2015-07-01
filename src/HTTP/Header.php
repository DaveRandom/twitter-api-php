<?php

namespace TwitterAPI\HTTP;

class Header implements \Iterator, \Countable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \ArrayObject
     */
    private $values;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param string $name
     * @param string|string[] $values
     */
    public function __construct($name, $values = array())
    {
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
        return array_values($this->values->getArrayCopy());
    }

    /**
     * @param string|string[] $values
     */
    public function setValues($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $this->values = new \ArrayObject;
        foreach ($values as $value) {
            $value = trim((string)$value);
            $this->values[$value] = $value;
        }
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function hasValue($needle)
    {
        return isset($this->values[trim((string)$needle)]);
    }

    /**
     * @param string $value
     */
    public function addValue($value)
    {
        $value = trim((string)$value);
        $this->values[$value] = $value;
    }

    /**
     * @param string $needle
     */
    public function removeValue($needle)
    {
        $value = trim((string)$needle);

        if (!isset($this->values[$value])) {
            throw new \LogicException('Cannot remove non-existent value: ' . $needle);
        }

        unset($this->values[$value]);
    }

    /**
     * @return string
     */
    public function current()
    {
        $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->counter++;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->counter = 0;
        $this->iterator = $this->values->getIterator();
        $this->iterator->rewind();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->values->count();
    }
}
