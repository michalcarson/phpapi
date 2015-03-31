<?php

namespace Salestax\PhpApi;

/**
 * Interface to make a call to the Parse Address API.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */
use Httpful\Response as HttpResponse;

class ParseAddressRequest extends Request {

    const DEV_URL = 'http://apidev.salestax.solutions/parseAddress';
    const PROD_URL = 'http://api.salestax.solutions/parseAddress';

    protected function getUrl() {
        return self::DEV_URL;

    }

    protected function makeResponse(HttpResponse $response, $errors) {
        return new ParseAddressResponse($response, $errors);

    }

}
