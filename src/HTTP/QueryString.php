<?php

namespace TwitterAPI\HTTP;

class QueryString extends \ArrayObject
{
    /**
     * @return string
     */
    public function __toString()
    {
        return http_build_query($this->getArrayCopy());
    }
}
