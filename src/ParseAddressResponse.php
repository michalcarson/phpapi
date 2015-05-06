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

    /**
     * Errors that occurred when trying to call the API endpoint.
     * @var array
     */
    public $errors;

    /**
     * The data actually found in the address that was passed.
     * @var array
     */
    public $data = array();

    public function __construct(HttpResponse $response, array $errors = array()) {
        $this->response = $response;
        $this->errors = $errors;
        $this->parseResponse($response);

    }

    protected function parseResponse(HttpResponse $response) {

        $address = $this->objectsToArrays($response->body);
        $this->data = $address['address'];

    }

    protected function objectsToArrays($subject) {

        if (is_object($subject)) {
            // turn the object into an array
            $subject = get_object_vars($subject);
        }

        if (is_array($subject)) {
            // check every member of the array for objects
            foreach ($subject as $key => $val) {
                $subject[$key] = $this->objectsToArrays($val);
            }
        }

        return $subject;

    }

}
