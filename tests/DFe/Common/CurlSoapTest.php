<?php

namespace DFe\Common;

class CurlSoapTest extends \PHPUnit\Framework\TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = \DFe\Core\SEFAZ::getInstance(true)->getConfiguracao();
    }

    public static function assertPostFunction($test, $soap, $data, $xml_name, $resp_name)
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/' . $xml_name;
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($data);

        // idLote auto gerado, copia para testar
        if (\DFe\Common\Util::nodeExists($dom_cmp, 'idLote')) {
            $node_cmp = \DFe\Common\Util::findNode($dom_cmp->documentElement, 'idLote');
            $node = \DFe\Common\Util::findNode($dom->documentElement, 'idLote');
            $node_cmp->nodeValue = $node->nodeValue;
        }

        // dhRegEvento auto gerado, copia para testar
        if (\DFe\Common\Util::nodeExists($dom_cmp, 'dhEvento')) {
            $node_cmp = \DFe\Common\Util::findNode($dom_cmp->documentElement, 'dhEvento');
            $node = \DFe\Common\Util::findNode($dom->documentElement, 'dhEvento');
            $node_cmp->nodeValue = $node->nodeValue;
            // quando a data do evento muda, a assinatura muda também
            $node_cmp = \DFe\Common\Util::findNode($dom_cmp->documentElement, 'DigestValue');
            $node = \DFe\Common\Util::findNode($dom->documentElement, 'DigestValue');
            $node_cmp->nodeValue = $node->nodeValue;
            // quando a data do evento muda, a assinatura muda também
            $node_cmp = \DFe\Common\Util::findNode($dom_cmp->documentElement, 'SignatureValue');
            $node = \DFe\Common\Util::findNode($dom->documentElement, 'SignatureValue');
            $node_cmp->nodeValue = $node->nodeValue;
        }

        if (getenv('TEST_MODE') == 'external') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $test->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());

        $xml_resp_file = dirname(dirname(__DIR__)) . '/resources/xml/' . $resp_name;
        $dom_resp = new \DOMDocument();
        $dom_resp->preserveWhiteSpace = false;
        $dom_resp->load($xml_resp_file);

        $soap->response = $dom_resp;
    }

    public function errorPostFunction($soap, $url, $data)
    {
        $soap->errorMessage = 'Not found';
        $soap->errorCode = '404';
        $soap->error = true;
    }

    public function testHookSendFunction()
    {
        CurlSoap::setPostFunction([$this, 'errorPostFunction']);
        $this->expectException('\DFe\Exception\NetworkException');
        $soap = new CurlSoap();
        $soap->send('invalid URL', new \DOMDocument());
        CurlSoap::setPostFunction(null);
    }
}
