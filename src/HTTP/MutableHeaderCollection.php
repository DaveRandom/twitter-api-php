<?php

namespace TwitterAPI\HTTP;

class MutableHeaderCollection extends HeaderCollection
{
    /**
     * @param \TwitterAPI\HTTP\Header $header
     * @return bool
     */
    public function addHeader(Header $header)
    {
        parent::addHeader($header);
    }

    /**
     * @param string $name
     */
    public function removeHeader($name)
    {
        parent::removeHeader($name);
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
