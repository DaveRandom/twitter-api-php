<?php

namespace TwitterAPI\HTTP;

class HeaderCollection implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var \TwitterAPI\HTTP\Header[]
     */
    protected $headers = [];

    /**
     * @param \TwitterAPI\HTTP\Header[] $headers
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $header) {
            if (!$header instanceof Header) {
                throw new \LogicException('Headers must be instances of ' . __NAMESPACE__ . '\\Header');
            }

            $this->headers[] = $header;
        }
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
        throw new \LogicException(sprintf(
            '%s objects are immutable, use a %s\\MutableHeaderCollection instead',
            get_class($this), __NAMESPACE__
        ));
    }

    /**
     * @param string $name
     */
    public function offsetUnset($name)
    {
        throw new \LogicException(sprintf(
            '%s objects are immutable, use a %s\\MutableHeaderCollection instead',
            get_class($this), __NAMESPACE__
        ));
    }
}
