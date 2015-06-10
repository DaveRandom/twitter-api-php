<?php

namespace TwitterAPI\HTTP;

use TwitterAPI\MagicPropertyAccess;

/**
 * @property string $method
 * @property string $url
 * @property \TwitterAPI\HTTP\QueryString $urlParams
 * @property \TwitterAPI\HTTP\QueryString $bodyParams
 * @property \TwitterAPI\HTTP\HeaderCollection $headers
 */
class Request
{
    use MagicPropertyAccess;

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
     * @var \TwitterAPI\HTTP\HeaderCollection
     */
    private $headers;

    /**
     * @param string $method
     * @param string $url
     * @param array $urlParams
     * @param array $bodyParams
     * @param \TwitterAPI\HTTP\HeaderCollection $headers
     */
    public function __construct(
        $url = null,
        $method = 'GET',
        array $urlParams = [],
        array $bodyParams = [],
        HeaderCollection $headers = null
    ) {
        $this->defineMagicProperties($public = ['method', 'url', 'urlParams', 'bodyParams', 'headers']);

        if ($url !== null) {
            $this->setURL($url);
        }

        $this->setMethod($method);
        $this->setURLParams($urlParams);
        $this->setBodyParams($bodyParams);
        $this->setHeaders($headers ?: new HeaderCollection);
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
     * @return \TwitterAPI\HTTP\HeaderCollection
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param \TwitterAPI\HTTP\HeaderCollection $headers
     */
    public function setHeaders(HeaderCollection $headers)
    {
        $this->headers = $headers;
    }
}
