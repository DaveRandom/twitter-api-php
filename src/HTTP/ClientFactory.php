<?php

namespace TwitterAPI\HTTP;

class ClientFactory
{
    /**
     * @return Client
     */
    public function select()
    {
        return $this->createCurlClient();
    }

    /**
     * @return CurlClient
     */
    public function createCurlClient()
    {
        return new CurlClient;
    }
}
