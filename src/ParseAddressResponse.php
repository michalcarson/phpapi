<?php

namespace Salestax\PhpApi;

/**
 * Response returned by the Parse Address Request.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */
use Httpful\Response as HttpResponse;

class ParseAddressResponse extends Response {

    /** @var \Httpful\Response */
    protected $response;

    /** @var array */
    public $errors;

    /** @var array */
    public $data = array();

    public function __construct(HttpResponse $response, array $errors = array()) {
        $this->response = $response;
        $this->errors = $errors;
        $this->parseResponse($response);

    }

    protected function parseResponse(HttpResponse $response) {

        if (is_object($response->body)) {
            $address = get_object_vars($response->body);
            $this->data = get_object_vars($address['address']);

        } elseif (is_array($response->body)) {
            $this->data = $response->body['address'];
            
        }

    }

}
