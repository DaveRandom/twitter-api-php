<?php

namespace TwitterAPI\HTTP;

abstract class Message
{
    /**
     * @var \TwitterAPI\HTTP\HeaderCollection
     */
    private $headers;

    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * @param \TwitterAPI\HTTP\HeaderCollection $headers
     * @param string $protocolVersion
     */
    public function __construct(HeaderCollection $headers, $protocolVersion)
    {
        $this->headers = $headers;
        $this->protocolVersion = (string)$protocolVersion;
    }

    /**
     * @return \TwitterAPI\HTTP\HeaderCollection
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param \TwitterAPI\HTTP\HeaderCollection $headers
     */
    protected function setHeaders(HeaderCollection $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $protocolVersion
     */
    protected function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = (string)$protocolVersion;
    }
}
