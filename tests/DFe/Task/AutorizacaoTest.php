<?php

namespace DFe\Task;

use DOMDocument;
use DFe\Core\NFCe;

class AutorizacaoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function processaPostFunction($test, $soap_curl, $url, $data, $xml_name, $resp_name)
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $xml_name;
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($data);

        // idLote auto gerado, copia para testar
        $node_cmp = \DFe\Common\Util::findNode($dom_cmp->documentElement, 'idLote');
        $node = \DFe\Common\Util::findNode($dom->documentElement, 'idLote');
        $node_cmp->nodeValue = $node->nodeValue;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $xml_cmp = $dom_cmp->saveXML();
        $test->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML());

        $xml_resp_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $resp_name;
        $dom_resp = new \DOMDocument();
        $dom_resp->preserveWhiteSpace = false;
        $dom_resp->load($xml_resp_file);

        $soap_curl->response = $dom_resp;
    }

    public static function processaCfePostFunction($test, $soap_curl, $url, $data, $xml_name, $resp_name)
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $xml_name;
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($data);

        if (getenv('TEST_MODE') === 'override') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML($dom->documentElement));
        }

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $test->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($dom->documentElement));

        $xml_resp_file = dirname(dirname(__DIR__)) . '/resources/xml/task/' . $resp_name;
        $dom_resp = new \DOMDocument();
        $dom_resp->preserveWhiteSpace = false;
        $dom_resp->load($xml_resp_file);

        $soap_curl->response = $dom_resp;
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaAutorizadoResponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaRejeicaoResponseSOAP.xml'
        );
    }

    public function processamentoPostFunction($soap_curl, $url, $data)
    {
        self::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaProcessamentoResponseSOAP.xml'
        );
    }

    public function testAutorizaAutorizado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $autorizacao = new Autorizacao($nota, $dom);
            $retorno = $autorizacao->envia();
            $autorizacao->fromArray($autorizacao);
            $autorizacao->fromArray($autorizacao->toArray());
            $autorizacao->fromArray(null);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Protocolo', $retorno);
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testAutorizaRejeitado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $autorizacao = new Autorizacao($nota, $dom);
            $retorno = $autorizacao->envia();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Autorizacao', $retorno);
        $this->assertEquals('785', $retorno->getStatus());
    }

    public function testAutorizaProcessamento()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $dom = $data['dom'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'processamentoPostFunction']);
        try {
            $autorizacao = new Autorizacao($nota, $dom);
            $retorno = $autorizacao->envia();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Recibo', $retorno);
        $this->assertEquals('103', $retorno->getStatus());
    }

    public function testNaoValidado()
    {
        $dom = new DOMDocument();
        $dom->appendChild($dom->createElement('schema'));
        $autorizacao = new Autorizacao(new NFCe(), $dom);
        $autorizacao->setVersao(NFCe::VERSAO);
        $this->expectException('\DFe\Exception\ValidationException');
        $autorizacao->getLoteLoader()->getNode();
    }
}
