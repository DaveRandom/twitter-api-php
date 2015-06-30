<?php

namespace TwitterAPI\HTTP;

class CurlClient implements Client
{
    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @param array $curlOpts
     * @return \TwitterAPI\HTTP\Response
     */
    public function sendRequest(Request $request, array $curlOpts = array())
    {
        // TODO: Implement sendRequest() method.
    }
}
