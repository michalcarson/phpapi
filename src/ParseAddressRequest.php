<?php

namespace Salestax\PhpApi;

/**
 * Interface to make a call to the Parse Address API.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */
use Httpful\Response as HttpResponse;

class ParseAddressRequest extends Request {

    protected $development_url = 'http://apidev.salestax.solutions/parseAddress';
    protected $production_url = 'http://api.salestax.solutions/parseAddress';

    protected function makeResponse(HttpResponse $response, $errors) {
        return new ParseAddressResponse($response, $errors);

    }

}
