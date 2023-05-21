<?php

namespace DFe\Core;

use DFe\Database\Estatico;
use DFe\Logger\Log;
use DFe\Task\Inutilizacao;
use DFe\Task\Tarefa;

class SEFAZTest extends \PHPUnit\Framework\TestCase implements \DFe\Common\Evento
{
    protected function setUp(): void
    {
        Log::getInstance()->setHandler(new \Monolog\Handler\NullHandler());
    }

    protected function tearDown(): void
    {
        Log::getInstance()->setHandler(null);
    }

    /**
     * @return \DFe\Core\SEFAZ default instance
     */
    public static function createSEFAZ()
    {
        $banco = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        if ($banco instanceof Estatico) {
            $banco->setIBPT(null);
        }
        SEFAZ::getInstance()->getConfiguracao()->setBanco(null);
        SEFAZ::getInstance()->getConfiguracao()->setCertificado(null);
        SEFAZ::getInstance()->setConfiguracao(null);
        gc_collect_cycles();
        $emitente = \DFe\Entity\EmitenteTest::createEmitente();
        $sefaz = SEFAZ::getInstance(true);
        $sefaz->getConfiguracao()
            ->getCertificado()
                ->setArquivoChavePublica(dirname(dirname(__DIR__)) . '/resources/certs/public.pem')
                ->setArquivoChavePrivada(dirname(dirname(__DIR__)) . '/resources/certs/private.pem');
        $sefaz->getConfiguracao()
            ->setEmitente($emitente);
        return $sefaz;
    }

    public function testInstancia()
    {
        $sefaz = SEFAZ::getInstance();
        $this->assertNotNull($sefaz);
        $this->assertNotNull($sefaz->getConfiguracao());
    }

    public function testNotas()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->addNota(new NFCe());
        $sefaz->addNota(new NFCe());
        $sefaz->fromArray($sefaz);
        $sefaz->fromArray($sefaz->toArray());
        $sefaz->fromArray(null);
        $sefaz->toArray(true);
        $this->assertCount(2, $sefaz->getNotas());
    }

    public function autorizadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Task\AutorizacaoTest::processaPostFunction(
            $this,
            $soap_curl,
            $url,
            $data,
            'testAutorizaSOAP.xml',
            'testAutorizaAutorizadoReponseSOAP.xml'
        );
    }

    public function testAutoriza()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $nota = \DFe\Core\NFCeTest::createNFCe($sefaz);
        \DFe\Common\CurlSoap::setPostFunction([$this, 'autorizadoPostFunction']);
        try {
            $sefaz->setNotas([]);
            $sefaz->addNota($nota);
            $this->assertEquals(1, $sefaz->autoriza());
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function networkErrorPostFunction($soap_curl, $url, $data)
    {
        throw new \DFe\Exception\IncompleteRequestException('Suposta falha parcial de rede', 500);
    }

    public function testAutorizaContingencia()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $nota = \DFe\Core\NFCeTest::createNFCe($sefaz);
        \DFe\Common\CurlSoap::setPostFunction([$this, 'networkErrorPostFunction']);
        try {
            $sefaz->setNotas([]);
            $sefaz->addNota($nota);
            $this->assertEquals(1, $sefaz->autoriza());
        } catch (\Exception $e) {
            $sefaz->getConfiguracao()->setOffline(null);
            \DFe\Common\CurlSoap::setPostFunction(null);
            throw $e;
        }
        $sefaz->getConfiguracao()->setOffline(null);
        \DFe\Common\CurlSoap::setPostFunction(null);
    }

    public function testConsulta()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $this->assertEquals(0, $sefaz->consulta([]));
    }

    public function testExecuta()
    {
        $sefaz = self::createSEFAZ();
        $this->assertEquals(0, $sefaz->executa([]));
    }

    public function inutilizadoPostFunction($soap_curl, $url, $data)
    {
        \DFe\Common\CurlSoapTest::assertPostFunction(
            $this,
            $soap_curl,
            $data,
            'task/testInutilizaSOAP.xml',
            'task/testInutilizaInutilizadoReponseSOAP.xml'
        );
    }

    public function testInutiliza()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $inutilizacao = \DFe\Task\InutilizacaoTest::criaInutilizacao();
        \DFe\Common\CurlSoap::setPostFunction([$this, 'inutilizadoPostFunction']);
        try {
            $this->assertTrue($sefaz->inutiliza($inutilizacao));
        } finally {
            \DFe\Common\CurlSoap::setPostFunction(null);
        }
    }

    public function testProcessa()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $this->assertEquals(0, $sefaz->processa());
    }

    public function testInutilizaFail()
    {
        $sefaz = self::createSEFAZ();
        $sefaz->getConfiguracao()->setEvento($this);
        $inutilizacao = new Inutilizacao();
        $this->assertFalse($sefaz->inutiliza($inutilizacao));
    }


    /**
     * Chamado quando o XML da nota foi gerado
     */
    public function onNotaGerada($nota, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaGerada($nota, $xml);
    }

    /**
     * Chamado após o XML da nota ser assinado
     */
    public function onNotaAssinada($nota, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaAssinada($nota, $xml);
    }

    /**
     * Chamado após o XML da nota ser validado com sucesso
     */
    public function onNotaValidada($nota, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaValidada($nota, $xml);
    }

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     */
    public function onNotaEnviando($nota, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaEnviando($nota, $xml);
    }

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contigência,
     * aqui deve ser decidido se o número da nota deverá ser pulado e se esse
     * número deve ser cancelado ou inutilizado
     */
    public function onNotaContingencia($nota, $offline, $exception)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaContingencia($nota, $offline, $exception);
    }

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     */
    public function onNotaAutorizada($nota, $xml, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaAutorizada($nota, $xml, $retorno);
    }

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     */
    public function onNotaCompleto($nota, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaCompleto($nota, $xml);
    }

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     */
    public function onNotaRejeitada($nota, $xml, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaRejeitada($nota, $xml, $retorno);
    }

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     */
    public function onNotaDenegada($nota, $xml, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaDenegada($nota, $xml, $retorno);
    }

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota
     */
    public function onNotaPendente($nota, $xml, $exception)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaPendente($nota, $xml, $exception);
    }

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     */
    public function onNotaProcessando($nota, $xml, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaProcessando($nota, $xml, $retorno);
    }

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     */
    public function onNotaCancelada($nota, $xml, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaCancelada($nota, $xml, $retorno);
    }

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota
     */
    public function onNotaErro($nota, $exception)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onNotaErro($nota, $exception);
    }

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     */
    public function onInutilizado($inutilizacao, $xml)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onInutilizado($inutilizacao, $xml);

        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $xml->saveXML());
    }

    /**
     * Chamado quando uma tarefa é executada
     */
    public function onTarefaExecutada($tarefa, $retorno)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onTarefaExecutada($tarefa, $retorno);

        if ($tarefa->getAcao() == Tarefa::ACAO_INUTILIZAR) {
            $xml = $tarefa->getDocumento();
            $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/task/testInutilizaInutilizadoProtocolo.xml';
            $dom_cmp = new \DOMDocument();
            $dom_cmp->preserveWhiteSpace = false;
            $dom_cmp->load($xml_file);
            $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $xml->saveXML());
        }
    }

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     */
    public function onTarefaErro($tarefa, $exception)
    {
        $ajuste = new \DFe\Common\Ajuste();
        $ajuste->onTarefaErro($tarefa, $exception);
        throw $exception;
    }
}
