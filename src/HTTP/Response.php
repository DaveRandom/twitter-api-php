<?php

namespace TwitterAPI\HTTP;

class Response extends Message
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $statusMessage;

    /**
     * @param string $protocolVersion
     * @param int $statusCode
     * @param string $statusMessage
     * @param HeaderCollection $headers
     * @param string $body
     */
    public function __construct($protocolVersion, $statusCode, $statusMessage, HeaderCollection $headers, $body)
    {
        parent::__construct($headers, $protocolVersion);

        $this->body = (string)$body;
        $this->statusCode = (int)$statusCode;
        $this->statusMessage = (string)$statusMessage;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}
