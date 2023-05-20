<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
 *
 * @author Francimar Alves <mazinsw@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace DFe\Common;

use Curl\Curl;

/**
 * Faz requisições SOAP 1.2
 */
class CurlSoap extends Curl
{
    public const ENVELOPE = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap12:Envelope 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
    xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
    <soap12:Body/>
</soap12:Envelope>
XML;

    private $certificate;
    private $private_key;
    private static $post_fn;

    /**
     * Construct
     *
     * @access public
     * @param mixed $base_url
     * @throws \ErrorException
     */
    public function __construct($base_url = null)
    {
        parent::__construct($base_url);
        $this->setOpt(CURLOPT_CAINFO, dirname(dirname(dirname(__DIR__))) . '/docs/cacert/cacert.pem');
        $this->setHeader('Content-Type', 'application/soap+xml; charset=utf-8');
        $this->setConnectTimeout(4);
        $this->setTimeout(6);
        $this->setXmlDecoder(function ($response) {
            $dom = new \DOMDocument();
            $xml_obj = $dom->loadXML($response);
            if (!($xml_obj === false)) {
                $response = $dom;
            }
            return $response;
        });
    }

    public static function setPostFunction($post_fn)
    {
        return self::$post_fn = $post_fn;
    }

    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    public function getCertificate()
    {
        return $this->certificate;
    }

    public function setPrivateKey($private_key)
    {
        $this->private_key = $private_key;
    }

    public function getPrivateKey()
    {
        return $this->private_key;
    }

    public function send($url, $body)
    {
        $this->setOpt(CURLOPT_SSLCERT, $this->getCertificate());
        $this->setOpt(CURLOPT_SSLKEY, $this->getPrivateKey());
        if ($body instanceof \DOMDocument) {
            $body = $body->saveXML($body->documentElement);
        }
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML(self::ENVELOPE);
        $envelope = $dom->saveXML();
        $data = str_replace('<soap12:Body/>', '<soap12:Body>' . $body . '</soap12:Body>', $envelope);
        if (is_null(self::$post_fn)) {
            $this->post($url, $data);
        } else {
            call_user_func_array(self::$post_fn, [$this, $url, $data]);
        }
        if (!$this->error) {
            return $this->response;
        }
        if (!empty($this->rawResponse) && ($this->response instanceof \DOMDocument)) {
            $text = $this->response->getElementsByTagName('Text');
            if ($text->length == 1) {
                throw new \Exception($text->item(0)->nodeValue, $this->errorCode);
            }
        }
        $transfer = $this->getInfo(CURLINFO_PRETRANSFER_TIME);
        if ($transfer == 0) { // never started the transfer
            throw new \DFe\Exception\NetworkException($this->errorMessage, $this->errorCode);
        }
        throw new \DFe\Exception\IncompleteRequestException($this->errorMessage, $this->errorCode);
    }
}
