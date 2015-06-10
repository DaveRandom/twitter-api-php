<?php

namespace TwitterAPI\HTTP;

interface Client
{
    /**
     * @param \TwitterAPI\HTTP\Request $request
     * @return \TwitterAPI\HTTP\Response
     */
    public function sendRequest(Request $request);
}
