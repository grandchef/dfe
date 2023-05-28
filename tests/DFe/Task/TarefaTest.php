<?php

namespace DFe\Task;

class TarefaTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public function emptyPostFunction($soap_curl)
    {
        $dom = new \DOMDocument();
        $dom->appendChild($dom->createElement('empty'));
        $soap_curl->response = $dom;
    }

    public function inutilizadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testInutilizaSOAP.xml',
            'task/testInutilizaInutilizadoResponseSOAP.xml'
        );
    }

    public function situacaoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoAutorizadoResponseSOAP.xml'
        );
    }

    public function canceladoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testSituacaoSOAP.xml',
            'task/testSituacaoCanceladoResponseSOAP.xml'
        );
    }

    public function reciboPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testReciboSOAP.xml',
            'task/testReciboAutorizadoResponseSOAP.xml'
        );
    }

    public function cancelarPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testEventoSOAP.xml',
            'task/testEventoRegistradoResponseSOAP.xml'
        );
    }

    public function testTarefaInutilizacao()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setJustificativa('TESTE DO SISTEMA');

        $inutilizacao = new Inutilizacao();
        $inutilizacao->setAno(2017);
        $inutilizacao->setJustificativa($nota->getJustificativa());

        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setNota($nota);
        $tarefa->setAgente($inutilizacao);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $retorno = $tarefa->executa();
            $tarefa->fromArray($tarefa);
            $tarefa->fromArray($tarefa->toArray());
            $tarefa->fromArray(null);
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $inutilizacao = $tarefa->getAgente();
        $this->assertEquals('102', $inutilizacao->getStatus());
        $this->assertEquals('141170000156683', $inutilizacao->getNumero());

        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = $tarefa->getDocumento();

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testRecursiveToArray()
    {
        $dom = new \DOMDocument();
        $tarefa = new Tarefa();
        $tarefa->setDocumento($dom);
        $expected = $tarefa->toArray();
        $expected['documento'] = $dom->saveXML();
        $this->assertEquals($expected, $tarefa->toArray(true));
    }

    public function testTarefaSemInutilizacao()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setJustificativa('TESTE DO SISTEMA');

        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'emptyPostFunction']);
        $this->expectException('\Exception');
        try {
            $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaInutilizacaoSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $this->expectException('\Exception');
            $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaInutilizacaoInvalida()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setAgente(new Recibo());

        \DFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $this->expectException('\Exception');
            $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaConsultaSituacao()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'situacaoPostFunction']);
        try {
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $tarefa->toArray(true);
        $this->assertInstanceOf('\\DFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testTarefaConsultaSituacaoCancelado()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'canceladoPostFunction']);
        try {
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertTrue($retorno->isCancelado());
        $dom = $tarefa->getDocumento();

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

    public function testTarefaConsultaRecibo()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $recibo = new Recibo();
        $recibo->setNumero('411000002567074');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setAgente($recibo);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'reciboPostFunction']);
        try {
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Protocolo', $nota->getProtocolo());
        $this->assertEquals('100', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());
    }

    public function testTarefaConsultaSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'situacaoPostFunction']);
        try {
            $this->expectException('\Exception');
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaConsultaInvalida()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CONSULTAR);
        $tarefa->setAgente(new Inutilizacao());

        \DFe\Common\CurlSoap::setPostFunction([$this, 'situacaoPostFunction']);
        try {
            $this->expectException('\Exception');
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaCancelar()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeAutorizada();
        $nota = $data['nota'];
        $nota->setJustificativa('CANCELAMENTO DE PEDIDO');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'cancelarPostFunction']);
        try {
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
        $this->assertInstanceOf('\\DFe\\Task\\Evento', $retorno);
        $this->assertEquals('135', $retorno->getStatus());
        $this->assertEquals($nota->getID(), $retorno->getChave());


        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testEventoRegistrado.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $dom = $tarefa->getDocumento();

        // dhRegEvento auto gerado, copia para testar
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

        if (getenv('TEST_MODE') == 'external') {
            $dom->formatOutput = true;
            file_put_contents($xml_file, $dom->saveXML());
        }

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testTarefaCancelarSemNota()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'cancelarPostFunction']);
        try {
            $this->expectException('\Exception');
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaCancelarNotaNaoAutorizada()
    {
        $data = \DFe\Core\NFCeTest::loadNFCeValidada();
        $nota = $data['nota'];
        $nota->setJustificativa('CANCELAMENTO DE PEDIDO');
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setNota($nota);

        \DFe\Common\CurlSoap::setPostFunction([$this, 'cancelarPostFunction']);
        try {
            $this->expectException('\Exception');
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testTarefaCancelarInvalido()
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_CANCELAR);
        $tarefa->setAgente(new Recibo());

        \DFe\Common\CurlSoap::setPostFunction([$this, 'cancelarPostFunction']);
        try {
            $this->expectException('\Exception');
            $retorno = $tarefa->executa();
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }
}
