<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Task;

use DOMDocument;
use DFe\Core\Nota;
use DFe\Core\SEFAZ;
use DFe\Common\CurlSoap;
use DFe\Core\CFe;
use DFe\Core\NFCe;
use DFe\Core\NFe;
use DFe\Loader\NFe\Task\EnvioLoader;
use DFe\Loader\CFe\Task\EnvioLoader as CFeEnvioLoader;

/**
 * Envia requisições para os servidores da SEFAZ
 */
class Envio
{
    /**
     * Tipo de serviço a ser executado
     */
    public const SERVICO_INUTILIZACAO = 'inutilizacao';
    public const SERVICO_PROTOCOLO = 'protocolo';
    public const SERVICO_STATUS = 'status';
    public const SERVICO_CADASTRO = 'cadastro';
    public const SERVICO_AUTORIZACAO = 'autorizacao';
    public const SERVICO_RETORNO = 'retorno';
    public const SERVICO_RECEPCAO = 'recepcao';
    public const SERVICO_CONFIRMACAO = 'confirmacao';
    public const SERVICO_EVENTO = 'evento';
    public const SERVICO_DESTINADAS = 'destinadas';
    public const SERVICO_DOWNLOAD = 'download';
    public const SERVICO_DISTRIBUICAO = 'distribuicao';

    /**
     * Tipo de serviço a ser executado
     */
    private $servico;

    /**
     * Identificação do Ambiente:
     * 1 - Produção
     * 2 - Homologação
     */
    private $ambiente;

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    private $modelo;

    /**
     * Forma de emissão da NF-e
     */
    private $emissao;

    /**
     * Conteudo a ser enviado
     */
    private $conteudo;

    /**
     * Constroi uma instância de Envio vazia
     * @param  array $envio Array contendo dados do Envio
     */
    public function __construct($envio = [])
    {
        $this->fromArray($envio);
    }

    /**
     * Tipo de serviço a ser executado
     *
     * @return mixed servico do Envio
     */
    public function getServico()
    {
        return $this->servico;
    }

    /**
     * Altera o valor do Servico para o informado no parâmetro
     * @param mixed $servico novo valor para Servico
     * @return self A própria instância da classe
     */
    public function setServico($servico)
    {
        $this->servico = $servico;
        return $this;
    }

    /**
     * Identificação do Ambiente
     *
     * @return mixed ambiente do Envio
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    /**
     * Altera o valor do Ambiente para o informado no parâmetro
     *
     * @param mixed $ambiente novo valor para Ambiente
     *
     * @return self A própria instância da classe
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Modelo do Documento Fiscal
     *
     * @return mixed modelo do Envio
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     *
     * @param mixed $modelo novo valor para Modelo
     *
     * @return self A própria instância da classe
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Forma de emissão da NF-e
     *
     * @return mixed emissao do Envio
     */
    public function getEmissao()
    {
        return $this->emissao;
    }

    /**
     * Altera o valor do Emissao para o informado no parâmetro
     *
     * @param mixed $emissao novo valor para Emissao
     *
     * @return self A própria instância da classe
     */
    public function setEmissao($emissao)
    {
        $this->emissao = $emissao;
        return $this;
    }

    /**
     * Conteudo a ser enviado
     * @return mixed conteudo do Envio
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }

    /**
     * Altera o valor do Conteudo para o informado no parâmetro
     * @param mixed $conteudo novo valor para Conteudo
     * @return self A própria instância da classe
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
        return $this;
    }

    /**
     * Obtém a versão do serviço a ser utilizado
     * @return string Versão do serviço
     */
    public function getVersao()
    {
        $url = $this->getServiceInfo();
        if (is_array($url) && isset($url['versao'])) {
            return $url['versao'];
        }
        if ($this->getModelo() === Nota::MODELO_CFE) {
            return CFe::VERSAO;
        }
        if ($this->getModelo() === Nota::MODELO_NFCE) {
            return NFCe::VERSAO;
        }
        return NFe::VERSAO;
    }

    /**
     * Devolve um array com as informações de serviço (URL, Versão, Serviço)
     * @return array|string Informações de serviço
     */
    public function getServiceInfo()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        if ($this->getModelo() === Nota::MODELO_CFE && $this->getServico() == self::SERVICO_EVENTO) {
            return $config->getUrlSat() . '/cancelamento';
        }
        if ($this->getModelo() === Nota::MODELO_CFE) {
            return $config->getUrlSat() . '/' . $this->getServico();
        }
        $db = $config->getBanco();
        $estado = $config->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $info = $db->getInformacaoServico(
            $this->getEmissao(),
            $estado->getUF(),
            $this->getModelo(),
            $this->getAmbiente()
        );
        if (!isset($info[$this->getServico()])) {
            throw new \Exception(
                sprintf(
                    'O serviço "%s" não está disponível para o estado "%s"',
                    $this->getServico(),
                    $estado->getUF()
                ),
                404
            );
        }
        return $info[$this->getServico()];
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $envio = [];
        $envio['servico'] = $this->getServico();
        $envio['ambiente'] = $this->getAmbiente();
        $envio['modelo'] = $this->getModelo();
        $envio['emissao'] = $this->getEmissao();
        $envio['conteudo'] = $this->getConteudo();
        return $envio;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $envio Array ou instância de Envio, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($envio = [])
    {
        if ($envio instanceof Envio) {
            $envio = $envio->toArray();
        } elseif (!is_array($envio)) {
            return $this;
        }
        $this->setServico($envio['servico'] ?? null);
        $this->setAmbiente($envio['ambiente'] ?? null);
        $this->setModelo($envio['modelo'] ?? null);
        $this->setEmissao($envio['emissao'] ?? null);
        $this->setConteudo($envio['conteudo'] ?? null);
        return $this;
    }

    public function getLoader()
    {
        if ($this->getModelo() === Nota::MODELO_CFE) {
            return new CFeEnvioLoader($this);
        }
        return new EnvioLoader($this);
    }

    /**
     * Envia o conteúdo para o serviço da SEFAZ informado
     * @return DOMDocument Documento XML da resposta da SEFAZ
     */
    public function envia()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $url = $this->getServiceInfo();
        if (is_array($url)) {
            $url = $url['url'];
        }
        if ($config->isOffline() && $this->getModelo() !== Nota::MODELO_CFE) {
            throw new \DFe\Exception\NetworkException('Operação offline, sem conexão com a internet', 7);
        }
        $config->verificaValidadeCertificado();
        $soap = new CurlSoap();
        $soap->setConnectTimeout(intval($config->getTempoLimite()));
        $soap->setTimeout(ceil($config->getTempoLimite() * 1.5));
        $soap->setCertificate($config->getCertificado()->getArquivoChavePublica());
        $soap->setPrivateKey($config->getCertificado()->getArquivoChavePrivada());
        $loader = $this->getLoader();
        $dom = $loader->getNode()->ownerDocument;
        try {
            $response = $soap->send($url, $dom, $this->getModelo() === Nota::MODELO_CFE);
            return $response;
        } catch (\DFe\Exception\NetworkException $e) {
            $config->setOffline(time());
            throw $e;
        }
    }
}
