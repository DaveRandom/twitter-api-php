<?php

namespace TwitterAPI\HTTP;

class CurlClient implements Client
{
    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @return array
     */
    private function createOptionsFromRequest(Request $request)
    {
        $opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
        );

        $opts[CURLOPT_URL] = $request->getURL();
        $urlParams = $request->getURLParams();
        if (count($urlParams) > 0) {
            $opts[CURLOPT_URL] .= '?' . http_build_query($urlParams->getArrayCopy());
        }

        $requestHeaders = $request->getHeaders();
        if ($request->getMethod() === 'POST') {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($request->getBodyParams()->getArrayCopy());
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
            $requestHeaders['Content-Length'] = strlen($opts[CURLOPT_POSTFIELDS]);
        }

        $headers = array();
        foreach ($requestHeaders as $name => $header) {
            foreach ($header as $value) {
                $headers[] = $name . ': ' . $value;
            }
        }

        if ($headers) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }

        return $opts;
    }

    /**
     * @param array $opts
     * @return string
     * @throws \RuntimeException
     */
    private function doRequest(array $opts)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        $errNo = curl_errno($ch);
        $errStr = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('cURL error: ' . $errStr, $errNo);
        }

        return $response;
    }

    /**
     * @param string $rawResponse
     * @return \TwitterAPI\HTTP\Response
     */
    private function createResponseFromString($rawResponse)
    {
        $parts = preg_split('/\r?\n\r?\n/', $rawResponse, 2);

        if (count($parts) !== 2) {
            throw new \RuntimeException('Invalid response (missing headers or entity-body)');
        }

        list($rawHeaders, $body) = $parts;

        $parts = preg_split('/\r?\n/', $rawHeaders);

        if (!preg_match('#^HTTP/(1\.[01])\h+(\d{3})\h+([^\r\n]+)$#', array_shift($parts), $match)) {
            throw new \RuntimeException('Invalid response (invalid response line)');
        }
        list(, $protocolVersion, $statusCode, $statusMessage) = $match;

        /** @var \TwitterAPI\HTTP\Header[] $headers */
        /** @var \TwitterAPI\HTTP\Header $lastHeader */
        $headers = array();
        $lastHeader = $lastValue = null;
        foreach ($parts as $part) {
            if (preg_match('/^\h/', $part)) { // multi-line continuation
                if ($lastHeader === null) {
                    throw new \RuntimeException('Invalid response (malformed headers, first header is continuation)');
                }

                $lastHeader->removeValue($lastValue);
                $lastValue = rtrim($lastValue) . ' ' . ltrim($part);
                $lastHeader->addValue($lastValue);
            } else if (preg_match('/^([^:]+):\h*(\S*)$/', $part, $match)) {
                list(, $name, $value) = $match;
                $nameLower = strtolower($name);

                if (isset($headers[$nameLower])) {
                    $headers[$nameLower]->addValue($value);
                } else {
                    $headers[$nameLower] = new Header($name, array($value));
                }

                $lastHeader = $headers[$nameLower];
                $lastValue = $value;
            } else {
                throw new \RuntimeException('Invalid response (malformed headers, first header is continuation)');
            }
        }

        return new Response($protocolVersion, $statusCode, $statusMessage, new HeaderCollection($headers), $body);
    }

    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param array $opts
     * @return \TwitterAPI\HTTP\Response
     * @throws \RuntimeException
     */
    public function sendRequest(Request $request, array $opts = array())
    {
        $opts = $this->createOptionsFromRequest($request) + $opts;
        $rawResponse = $this->doRequest($opts);
        return $this->createResponseFromString($rawResponse);
    }
}
