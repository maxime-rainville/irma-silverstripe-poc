<?php
use SilverStripe\Control\HTTPResponse;


class JSONResponse extends HTTPResponse
{
    public function __construct($body, $statusCode = null, $statusDescription = null, $protocolVersion = null)
    {
        parent::__construct(json_encode($body), $statusCode, $statusDescription, $protocolVersion);

        $this->addHeader('content-type', 'application/json');
    }
}
