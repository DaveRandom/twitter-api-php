<?php

namespace TwitterAPI\HTTP;

class HeaderCollection implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var \TwitterAPI\HTTP\Header[]
     */
    private $headers = [];

    /**
     * @param \TwitterAPI\HTTP\Header $header
     * @return bool
     */
    public function addHeader(Header $header)
    {
        $nameLower = strtolower($header->getName());

        if (isset($this->headers[$nameLower])) {
            throw new \LogicException('Cannot add header ' . $header->getName() . ': already exists');
        }

        $this->headers[$nameLower] = $header;
    }

    /**
     * @param string $name
     * @return \TwitterAPI\HTTP\Header
     */
    public function getHeader($name)
    {
        $nameLower = strtolower($name);

        if (!isset($this->headers[$nameLower])) {
            throw new \LogicException('Cannot get header ' . $name . ': does not exist');
        }

        return $this->headers[$nameLower];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @param string $name
     */
    public function removeHeader($name)
    {
        $nameLower = strtolower($name);

        if (!isset($this->headers[$nameLower])) {
            throw new \LogicException('Cannot remove header ' . $name . ': does not exist');
        }

        unset($this->headers[$nameLower]);
    }

    /**
     * @return \TwitterAPI\HTTP\Header
     */
    public function current()
    {
        return current($this->headers);
    }

    public function next()
    {
        next($this->headers);
    }

    /**
     * @return string
     */
    public function key()
    {
        return current($this->headers)->getName();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return key($this->headers) !== null;
    }

    public function rewind()
    {
        reset($this->headers);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->headers);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->hasHeader($name);
    }

    /**
     * @param string $name
     * @return \TwitterAPI\HTTP\Header
     */
    public function offsetGet($name)
    {
        return $this->getHeader($name);
    }

    /**
     * @param string $name
     * @param \TwitterAPI\HTTP\Header|string $header
     */
    public function offsetSet($name, $header)
    {
        if ($name === null && !$header instanceof Header) {
            throw new \LogicException('Cannot add header without name');
        }

        $name = $name === null ? $header->getName() : $name;
        $header = $header instanceof Header ? $header : new Header($name, is_array($header) ? $header : (string)$header);

        $this->addHeader($name, $header);
    }

    /**
     * @param string $name
     */
    public function offsetUnset($name)
    {
        $this->removeHeader($name);
    }
}
