<?php

namespace TwitterAPI\HTTP;

class Request extends Message
{
    /**
     * @var string[]
     */
    private static $permittedMethods = ['GET', 'POST'];

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \TwitterAPI\HTTP\QueryString
     */
    private $urlParams;

    /**
     * @var \TwitterAPI\HTTP\QueryString
     */
    private $bodyParams;

    /**
     * @param string $method
     * @param string $url
     */
    public function __construct($url = null, $method = 'GET')
    {
        parent::__construct(new MutableHeaderCollection, '1.1');

        if ($url !== null) {
            $this->setURL($url);
        }
        $this->setMethod($method);
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setURL($url)
    {
        $this->url = (string)$url;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $methodUpper = strtoupper((string)$method);

        if (!in_array($methodUpper, self::$permittedMethods)) {
            throw new \LogicException('Invalid method: ' . $method);
        }

        $this->method = $methodUpper;
    }

    /**
     * @return \TwitterAPI\HTTP\QueryString
     */
    public function getURLParams()
    {
        return $this->urlParams;
    }

    /**
     * @param array $params
     */
    public function setURLParams(array $params)
    {
        $this->urlParams = new QueryString($params);
    }

    /**
     * @return \TwitterAPI\HTTP\QueryString
     */
    public function getBodyParams()
    {
        return $this->bodyParams;
    }

    /**
     * @param array $params
     */
    public function setBodyParams(array $params)
    {
        $this->bodyParams = new QueryString($params);
    }

    /**
     * @param \TwitterAPI\HTTP\MutableHeaderCollection $headers
     */
    public function setHeaders(MutableHeaderCollection $headers)
    {
        parent::setHeaders($headers);
    }

    /**
     * @return MutableHeaderCollection
     */
    public function getHeaders()
    {
        return parent::getHeaders();
    }

    /**
     * @param string $protocolVersion
     */
    public function setProtocolVersion($protocolVersion)
    {
        parent::setProtocolVersion($protocolVersion);
    }
}
