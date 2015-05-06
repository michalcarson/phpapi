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
        $rates = (object) array(
            40 => (object) array(
                'jurisdiction_type' => '45',
                'jusidiction_fips_code' => '40',
                'general_interstate' => 0.045,
                'general_intrastate' => 0.045,
                'food_interstate' => 0.045,
                'food_intrastate' => 0.045
            ),
            '079' => (object) array(
                'jurisdiction_type' => '00',
                'jusidiction_fips_code' => '079',
                'general_interstate' => 0.02,
                'general_intrastate' => 0.02,
                'food_interstate' => 0.02,
                'food_intrastate' => 0.02
            ),
            '07450' => (object) array(
                'jurisdiction_type' => '01',
                'jusidiction_fips_code' => '07450',
                'general_interstate' => 0.03,
                'general_intrastate' => 0.03,
                'food_interstate' => 0.03,
                'food_intrastate' => 0.03
            ),
            'total' => (object) array(
                'jurisdiction_type' => '',
                'jusidiction_fips_code' => '',
                'general_interstate' => 0.095,
                'general_intrastate' => 0.095,
                'food_interstate' => 0.095,
                'food_intrastate' => 0.095
            ),
            'basis' => 'address'
        );
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
                'conflict' => array(),
                'sales_tax_rates' => $rates
            )
        ));
        $request = HttpRequest::init(Http::POST)->body($body);

        $hres = new HttpResponse($body, $headers, $request);
        $this->assertEquals('stdClass', get_class($hres->body));
        $this->assertEquals(true, is_object($hres->body));

        $response = new ParseAddressResponse($hres, []);

        $this->assertEquals('ROBERT S KERR', $response->data['street_name']);

        $missing = $response->data['missing'];
        $this->assertEquals('Missing something', $missing[0]);

        $rates = $response->data['sales_tax_rates'];
        $this->assertEquals('address', $rates['basis']);
        $this->assertEquals(0.095, $rates['total']['food_intrastate']);

    }

}
