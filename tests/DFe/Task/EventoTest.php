<?php

namespace DFe\Task;

use Exception;

class EventoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createEvento($nota)
    {
        $evento = new Evento();
        $evento->setData(strtotime('2017-03-18T16:12:12+00:00'));
        $evento->setOrgao(
            $nota->getEmitente()->getEndereco()->getMunicipio()->getEstado()->getUF()
        );
        $evento->setJustificativa('CANCELAMENTO DE PEDIDO');
        $evento->setAmbiente($nota->getAmbiente());
        $evento->setModelo($nota->getModelo());
        $evento->setIdentificador($nota->getEmitente()->getCNPJ());
        $evento->setNumero('141170000157685');
        $evento->setChave($nota->getID());
        return $evento;
    }

    public static function loadEventoRegistradoXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml';
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->load($xml_file);
        return $dom;
    }

    public function registradoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRegistradoReponseSOAP.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRejeitadoReponseSOAP.xml'
        );
    }

    public function testEventoRegistrado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'registradoPostFunction']);
        try {
            $evento = self::createEvento($nota);
            $evento->envia();
            $retorno = $evento->getInformacao();
            $evento->fromArray($evento);
            $evento->fromArray(null);
            $evento->fromArray($evento->toArray());
            $dom = $evento->getDocumento();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());

        if (getenv('TEST_MODE') == 'external') {
            $dom->formatOutput = true;
            $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml';
            file_put_contents($xml_file, $dom->saveXML());
        }

        $dom_cmp = self::loadEventoRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testEventoRejeitado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);
        try {
            $evento = self::createEvento($nota);
            $evento->envia();
            $retorno = $evento->getInformacao();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $this->assertEquals('573', $retorno->getStatus());
    }

    public function testEventoInvalido()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $evento = self::createEvento($nota);
        $evento->setAmbiente('Produção');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Falha ao obter o serviço da SEFAZ para o ambiente "Produção"');
        $evento->envia();
    }
}
