<?php

namespace DFe\Task;

use DFe\Core\Nota;
use Exception;

class EventoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \DFe\Core\SEFAZTest::createSEFAZ();
    }

    protected function tearDown(): void
    {
        \DFe\Common\CurlSoap::setPostFunction(null);
    }

    public static function createEvento(Nota $nota)
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
        $evento->setCaixa($nota->getCaixa());
        $evento->setResponsavel($nota->getResponsavel());
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

    public static function loadEventoCFeRegistradoXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testCancelamentoCFeResponse.xml';
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
            'task/testEventoRegistradoResponseSOAP.xml'
        );
    }

    public function registradoCFePostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testCancelamentoCFe.xml',
            'task/testCancelamentoCFeResponse.xml'
        );
    }

    public function rejeitadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRejeitadoResponseSOAP.xml'
        );
    }

    public function testEventoRegistrado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'registradoPostFunction']);

        $evento = self::createEvento($nota);
        $evento->envia();
        $retorno = $evento->getInformacao();
        $evento->fromArray($evento);
        $evento->fromArray(null);
        $evento->fromArray($evento->toArray());
        $dom = $evento->getDocumento();

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

    public function testEventoRegistradoCFe()
    {
        \DFe\Common\CurlSoap::setPostFunction([$this, 'registradoCFePostFunction']);

        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testCFeResponse.xml';
        $cfe = new \DFe\Core\CFe();
        $cfe->load($xml_file);

        $evento = self::createEvento($cfe);
        $evento->envia();
        $retorno = $evento->getInformacao();
        $evento->fromArray($evento);
        $evento->fromArray(null);
        $evento->fromArray($evento->toArray());
        $dom = $evento->getDocumento();

        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertEquals($cfe->getID(), $retorno->getChave());

        $dom_cmp = self::loadEventoCFeRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testEventoRejeitado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'rejeitadoPostFunction']);

        $evento = self::createEvento($nota);
        $evento->envia();
        $retorno = $evento->getInformacao();

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
