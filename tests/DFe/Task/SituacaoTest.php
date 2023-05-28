<?php

namespace DFe\Task;

use DFe\Core\Nota;

class SituacaoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoAutorizadoReponseSOAP.xml'
        );
    }

    public function inexistentePostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoInexistenteReponseSOAP.xml'
        );
    }

    public function canceladoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoCanceladoReponseSOAP.xml'
        );
    }

    public function testNormalization()
    {
        $situacao = new Situacao();
        $situacao->setModelo('65');
        $this->assertEquals(Nota::MODELO_NFCE, $situacao->getModelo());
        $this->assertEquals('65', $situacao->getModelo(true));
        $situacao->setModelo('55');
        $this->assertEquals(Nota::MODELO_NFE, $situacao->getModelo());
        $this->assertEquals('55', $situacao->getModelo(true));
        $situacao->setModelo('50');
        $this->assertEquals('50', $situacao->getModelo(true));
    }

    public function testSituacaoAutorizado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
            $situacao->fromArray($situacao);
            $situacao->fromArray($situacao->toArray());
            $situacao->fromArray(null);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testSituacaoInexistente()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'inexistentePostFunction']);
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Situacao', $retorno);
        $this->assertEquals('785', $retorno->getStatus());
    }

    public function testSituacaoCancelado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        \DFe\Common\CurlSoap::setPostFunction([$this, 'canceladoPostFunction']);
        try {
            $situacao = new Situacao();
            $retorno = $situacao->consulta($nota);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertTrue($situacao->isCancelado());
        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $dom = $retorno->getDocumento();

        if (getenv('TEST_MODE') == 'external') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml',
                $dom->saveXML()
            );
        }

        $dom_cmp = EventoTest::loadEventoRegistradoXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testValidarEsquemaNotFound()
    {
        $situacao = new Situacao();
        $this->expectException('\Exception');
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->createElement('schema'));
        $situacao->validar($dom);
    }

    public function testSituacaoInvalida()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setID('1');
        $situacao = new Situacao();
        $situacao->setModelo('Invalido');
        \DFe\Common\CurlSoap::setPostFunction([$this, 'inexistentePostFunction']);
        $this->expectException('\Exception');
        try {
            $retorno = $situacao->consulta($nota);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }
}
