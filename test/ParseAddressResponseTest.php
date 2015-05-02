<?php
namespace Salestax\PhpApi\Test;

/**
 * Description of ParseAddressResponseTest
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use Httpful\Http;
use Httpful\Request as HttpRequest;
use Httpful\Response as HttpResponse;
use Salestax\PhpApi\ParseAddressResponse;

class ParseAddressResponseTest extends \PHPUnit_Framework_TestCase {

    public function testParse() {

        $headers = "HTTP 200\r\nContent-Type: application/json\r\n\r\n";
        $body = json_encode(array(
            'address' => array(
                'street_address' => '123 ROBERT S KERR',
                'number' => '123',
                'number_suffix' => '',
                'pre_direction' => '',
                'street_name' => 'ROBERT S KERR',
                'post_direction' => '',
                'street_suffix' => '',
                'unit_type' => '',
                'unit_type_id' => '',
                'pobox' => '',
                'route_id' => '',
                'city' => 'OKLAHOMA CITY',
                'city_minor' => '',
                'state_name' => 'OKLAHOMA',
                'state_code' => 'OK',
                'zip' => '73102',
                'zip_extension' => '',
                'country' => '',
                'latitude' => '',
                'longitude' => '',
                'info' => array(),
                'warning' => array(),
                'missing' => array(
                        'Missing something'
                    ),
                'conflict' => array()
            )
        ));
        $request = HttpRequest::init(Http::POST)->body($body);

        $hres = new HttpResponse($body, $headers, $request);
        $response = new ParseAddressResponse($hres, []);

        $this->assertEquals('ROBERT S KERR', $response->data['street_name']);

        $missing = $response->data['missing'];
        $this->assertEquals('Missing something', $missing[0]);

    }

}
