<?php

namespace DFe\Task;

use DFe\Core\Nota;

class EnvioTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createEnvio()
    {
        $dom = new \DOMDocument();
        $dom->loadXML('<a/>');
        $envio = new Envio();
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setModelo(Nota::MODELO_NFCE);
        $envio->setAmbiente(Nota::AMBIENTE_HOMOLOGACAO);
        $envio->setServico(Envio::SERVICO_INUTILIZACAO);
        $envio->setConteudo($dom);
        return $envio;
    }

    public function nulPostFunction($soap_curl, $url, $data)
    {
    }

    public function errorPostFunction($soap, $url, $data)
    {
        $soap->errorMessage = 'Not found';
        $soap->errorCode = '404';
        $soap->error = true;
    }

    public function testVersao()
    {
        $envio = new Envio();
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $envio->setModelo(Nota::MODELO_NFE);
        $envio->setAmbiente(Nota::AMBIENTE_HOMOLOGACAO);
        $envio->setServico(Envio::SERVICO_CONFIRMACAO);
        $this->assertEquals('2.00', $envio->getVersao());
    }

    public function testEnvioOffline()
    {
        $old_envio = self::createEnvio();
        $envio = new Envio($old_envio);
        $envio->fromArray($old_envio->toArray());
        $envio->fromArray(null);
        $this->sefaz->getConfiguracao()->setOffline(time());
        \DFe\Common\CurlSoap::setPostFunction([$this, 'nulPostFunction']);
        $this->expectException('\DFe\Exception\NetworkException');
        try {
            $envio->envia();
        } catch (\Exception $e) {
            \DFe\Common\CurlSoap::setPostFunction(null);
            $this->sefaz->getConfiguracao()->setOffline(null);
            throw $e;
        }
        \DFe\Common\CurlSoap::setPostFunction(null);
        $this->sefaz->getConfiguracao()->setOffline(null);
    }

    public function testEnvioErro()
    {
        $envio = self::createEnvio();
        \DFe\Common\CurlSoap::setPostFunction([$this, 'errorPostFunction']);
        $this->expectException('\DFe\Exception\NetworkException');
        try {
            $envio->envia();
        } catch (\Exception $e) {
            \DFe\Common\CurlSoap::setPostFunction(null);
            $this->sefaz->getConfiguracao()->setOffline(null);
            throw $e;
        }
        \DFe\Common\CurlSoap::setPostFunction(null);
        $this->sefaz->getConfiguracao()->setOffline(null);
    }

    public function testEnvioAcaoInvalida()
    {
        $envio = self::createEnvio();
        $envio->setServico('qrcode');
        $this->expectException('\Exception');
        $envio->getLoader()->getServico();
    }

    public function testEnvioServicoInvalido()
    {
        $envio = self::createEnvio();
        $envio->setServico('cancelar');
        $this->expectException('\Exception');
        $envio->getLoader()->getServico();
    }
}
