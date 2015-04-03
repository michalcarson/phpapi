<?php

namespace SymfonyXmlResponse\Responses;

/**
 * Simple XML response writer in the Symfony Response model. It is simple in that it does not allow for attrtibutes
 * on the XML tags.
 *
 * For the origin of much of this code, @see http://php.net/manual/en/ref.xmlwriter.php. Proper credit should go
 * to massimo71, Alexandre Arica and others.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use Symfony\Component\HttpFoundation\Response;

class XmlResponse extends Response {

    protected $data = '';

    /**
     * instance of the built-in PHP XMLWriter.
     * @var \XMLWriter
     */
    protected $xml_writer;

    /**
     * Name for the root element of the XML document.
     * @var string
     */
    public $root_element_name = 'document';

    /**
     * Constructor.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new \ArrayObject();
        }

        $this->xml_writer = new \XMLWriter();

        if (!is_null($data)) {
            $this->setData($data);
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function create($data = null, $status = 200, $headers = array())
    {
        return new static($data, $status, $headers);

    }

    /**
     * Sets the data to be sent as XML.
     *
     * @param mixed $data
     *
     * @return XmlResponse
     *
     * @throws \InvalidArgumentException
     */
    public function setData($data = array())
    {

        try {

            $this->startDocument($this->root_element_name);
            $this->fromArray($data);
            $this->data = $this->getDocument();

        } catch (\Exception $exception) {
            throw $exception;
        }

        return $this->update();

    }

    /**
     * Updates the content and headers
     *
     * @return XmlResponse
     */
    protected function update()
    {

        // Only set the header when there is none
        // in order to not overwrite a custom definition.
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'application/xml');
        }

        return $this->setContent($this->data);

    }

    /**
     * Constructor.
     * @author Alexandre Arica
     * @param string $prm_rootElementName A root element's name of a current xml document
     * @param string $prm_xsltFilePath Path of a XSLT file.
     * @access public
     * @param null
     */
    protected function startDocument($prm_rootElementName, $prm_xsltFilePath = '')
    {
        $this->xml_writer->openMemory();
        $this->xml_writer->setIndent(true);
        $this->xml_writer->setIndentString(' ');
        $this->xml_writer->startDocument('1.0', 'UTF-8');

        if ($prm_xsltFilePath) {
            $this->xml_writer->writePi('xml-stylesheet', 'type="text/xsl" href="' . $prm_xsltFilePath . '"');
        }

        $this->xml_writer->startElement($prm_rootElementName);

    }

    /**
     * Set an element with a text to a current xml document.
     * @author Alexandre Arica
     * @access public
     * @param string $prm_elementName An element's name
     * @param string $prm_ElementText An element's text
     * @return null
     */
    protected function setElement($prm_elementName, $prm_ElementText)
    {
        $this->xml_writer->startElement($prm_elementName);
        $this->xml_writer->text($prm_ElementText);
        $this->xml_writer->endElement();

    }

    /**
     * Construct elements and texts from an array.
     * The array should contain an attribute's name in index part
     * and a attribute's text in value part.
     * @author Alexandre Arica
     * @author massimo71
     * @access public
     * @param array $prm_array Contains attributes and texts
     * @return null
     */
    protected function fromArray($prm_array)
    {

        if (is_array($prm_array)) {

            foreach ($prm_array as $index => $element) {
                if (is_array($element)) {
                    $this->xml_writer->startElement($index);
                    $this->fromArray($element);
                    $this->xml_writer->endElement();
                } else {
                    $this->setElement($index, $element);
                }
            }

        }

    }

    /**
     * Return the content of a current xml document.
     * @author Alexandre Arica
     * @access public
     * @param null
     * @return string Xml document
     */
    protected function getDocument()
    {
        $this->xml_writer->endElement();
        $this->xml_writer->endDocument();
        return $this->xml_writer->outputMemory();

    }

}
