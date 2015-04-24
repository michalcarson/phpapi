<?php
namespace Salestax\PhpApi\Test;

/**
 * Description of RequestTest
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use Salestax\PhpApi\Test\Fixture\MockRequest;

class RequestTest extends \PHPUnit_Framework_TestCase {

    public function testConstructor() {

        $req = new MockRequest(array('thing1' => 'thing2'));
        $this->assertEquals('thing2', $req->thing1);

    }

    public function testSetLogging() {

        $req = new MockRequest();

        $req->setLogging(true);
        $this->assertTrue($req->logging);

        $req->setLogging(false);
        $this->assertFalse($req->logging);

        $req->setLogging('true');
        $this->assertTrue($req->logging);

        $req->setLogging(1);
        $this->assertTrue($req->logging);

        $req->setLogging(0);
        $this->assertFalse($req->logging);

        $req->setLogging(null);
        $this->assertFalse($req->logging);

        $req->setLogging();
        $this->assertTrue($req->logging);

    }

    public function testSetLogFile() {

        $req = new MockRequest();
        $req->setLogFile('thisfile.log');
        $this->assertEquals('thisfile.log', $req->log_file);

    }

    public function testSetGet() {

        $req = new MockRequest();
        $req->oldfish = 'newfish';
        $this->assertEquals('newfish', $req->oldfish);
        $this->assertEquals('newfish', $req->__get('oldfish'));

    }

    public function testErrorCallback() {

        $req = new MockRequest();
        $req->errorCallback('the sun did not shine');
        $req->errorCallback('it was too wet to play');
        $this->assertEquals('the sun did not shine', $req->errors[0]);
        $this->assertEquals('it was too wet to play', $req->errors[1]);

    }

    public function testLogResponse() {

        $req = new MockRequest();
        $req->setLogging();
        $req->setLogFile('mock');

        $hreq = \Httpful\Request::init();
        $hresp = new \Httpful\Response(
                json_encode(array('redfish' => 'bluefish')),
                'HTTP/1.1 500 Connection Error',
                $hreq
        );
        $req->mockLogResponse($hresp);

        $log = $req->mock_log[0];
        $this->assertContains('{"redfish":"bluefish"}', $log);

    }

    /**
     * only run in dev environment
     * @requires OS WIN32|WINNT
     */
    public function testSend() {

        $req = new MockRequest();
        $req->onefish = 'twofish';
        $resp = $req->send();

        $this->assertEquals(200, $resp->code);
        $this->assertEquals('twofish', $resp->body->onefish);

    }

}
