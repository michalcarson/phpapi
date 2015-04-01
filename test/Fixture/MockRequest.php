<?php
namespace Salestax\PhpApi\Test\Fixture;

/**
 * Description of MockRequest
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use Salestax\PhpApi\Request;
use Httpful\Response as HttpResponse;

class MockRequest extends Request {

    protected $mock_log = array();

    protected function getUrl() {
        return 'http://apidev.salestax.solutions/version';
    }

    protected function makeResponse(HttpResponse $response, $errors) {
        return $response;
    }

    public function mockLogResponse(HttpResponse $response) {
        $this->logResponse($response);
    }

    protected function writeLog($content) {
        $this->mock_log[] = $content;
    }

    public function __get($name) {

        $ret = parent::__get($name);

        // if the parent did not provide a value, look for one ourselves.
        // this may pick up a protected or private attribute.
        if (is_null($ret)) {
            if (isset($this->$name)) {
                $ret = $this->$name;
            }
        }
        return $ret;

    }

}
