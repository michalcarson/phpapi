<?php

namespace Salestax\PhpApi;

/**
 * Base request class for the Salestax Solutions PHP API.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */
use Httpful\Request as HttpRequest;
use Httpful\Response as HttpResponse;

abstract class Request {

    const VERSION = '1.0.0';

    /*
     * Location of the service we will be calling.
     * This value is provided by the getUrl method which must be implemented
     * in the child class.
     */
    protected $url;

    /*
     * Storage for the URLs to the API. Values should be set by the child class.
     */
    protected $production_url;
    protected $development_url;

    /*
     * Data we will be sending to the service. This array is built by the __set()
     * magic method. Push data here with $request->property_name syntax where
     * "property_name" is the name of the field you want to save into the array.
     */
    protected $data;

    /*
     * Controls whether we write a log record. $log_file must also be supplied
     * by calling setLogFile.
     */
    protected $logging = true;
    protected $log_file;

    /*
     * Flag to indicate if we should call the Production API URL or the Dev URL
     */
    protected $production = true;

    /* error messages returned by Httpful */
    protected $errors = array();

    /**
     * Return the URL to the web service we will be calling.
     * This method will be called from the send() method.
     * @return string
     */
    protected function getUrl() {
        if ($this->production) {
            return $this->production_url;
        }
        return $this->development_url;

    }

    /**
     * Create an appropriate response object using the content just returned
     * by curl_exec. This method is called from the send() method. The response
     * object is then returned to the client.
     * @param string $content
     * @return Salestax\PhpApi\Response
     */
    abstract protected function makeResponse(HttpResponse $response, $errors);

    /**
     * @param array $data optional array of attributes for this instance
     */
    public function __construct(array $data = array()) {
        $this->data = $data;

    }

    /**
     * Designate which URL we should be using for the API, production or dev.
     * @param boolean $prod     true to call production
     */
    public function setProduction($prod = true) {
        $this->production = ($prod == true);

    }

    /**
     * Turn logging on or off. Supply a falsey parameter to turn logging off.
     * @param boolean $logging
     */
    public function setLogging($logging = true) {
        $this->logging = ($logging == true);

    }

    /**
     * Set the path and filename for the log file. Must be a file the web server
     * user can write. Usually there is a log directory already established for
     * this purpose.
     * @param string $log_file
     */
    public function setLogFile($log_file) {
        $this->log_file = $log_file;

    }

    /**
     * Send the data and receive the response. Response is passed through the
     * makeResponse() function and then returned to the client.
     * @return Salestax\PhpApi\Response
     */
    public function send() {
        $this->url = $this->getUrl();
        $this->salestax_php_api = self::VERSION;
        $this->php_version = PHP_VERSION;

        $request = HttpRequest::post($this->url)
            ->sendsJson()
            ->expectsJson()
            ->body(json_encode($this->data))
            ->followRedirects()
            ->withStrictSSL()
            ->whenError(function($error) {
                $this->errorCallback($error);
            }
        );

        try {

            $response = $request->send();

        } catch (\Httpful\Exception\ConnectionErrorException $e) {
            $response = new HttpResponse(json_encode($this->errors), 'HTTP/1.1 500 Connection Error', $request);
        }

        $this->logResponse($response);

        return $this->makeResponse($response, $this->errors);

    }

    /**
     * Collects the errors reported by Httpful so we can log them.
     * @param string $error
     */
    public function errorCallback($error) {

        // TODO: use monolog
        if (class_exists('\FirePHP', true)) {
            $fb = \FirePHP::getInstance(true);
            $fb->error($error, 'error');
        }
        $this->errors[] = $error;

    }

    /**
     * Format and write a log record for the last call to curl_exec.
     * @param \Httpful\Response $response
     */
    protected function logResponse(HttpResponse $response) {
        if ($this->logging && strlen($this->log_file)) {

            $reqheaders = explode("\n", trim($response->request->raw_headers, "\n "));

            $headers = array();
            foreach ($response->headers->toArray() as $key => $val) {
                $headers[] = "$key => $val";
            }

            $data = array();
            foreach ($this->data as $key => $val) {
                $data[] = "$key => $val";
            }

            $log = date('Y/m/d H:i:s') . " ====================================================\n"
                . 'Request class: ' . get_class($response->request) . "\n"
                . 'Response class: ' . get_class($response) . "\n"
                . "URL: $this->url\n"
                . "Request Headers:\n " . implode("\n ", $reqheaders) . "\n"
                . "Data: \n " . implode("\n ", $data) . "\n";

            if ($response->hasErrors()) {
                $log .= "Errors:\n " . implode("\n ", $this->errors) . "\n";
            }

            $log .= "Response Headers:\n " . implode("\n ", $headers) . "\n"
                . "Response:\n" . var_export($response->body, true) . "\n\n";

            $this->writeLog($log);
        }

    }

    /**
     * Append content to the log file.
     * @param string $content
     */
    protected function writeLog($content) {
        if ($this->logging && strlen($this->log_file) && strlen($content)) {
            // TODO: use monolog
            file_put_contents($this->log_file, $content, FILE_APPEND);
        }

    }

    public function __set($name, $value) {
        $this->data[$name] = $value;

    }

    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

    }

}
