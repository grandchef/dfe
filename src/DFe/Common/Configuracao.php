<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Common;

use DFe\Entity\Emitente;
use DFe\Database\Estatico;

/**
 * Fornece informações importante para a geração e envio das notas fiscais
 */
class Configuracao
{
    /**
     * @var \DFe\Database\Banco
     */
    private $banco;

    /**
     * @var \DFe\Entity\Emitente
     */
    private $emitente;

    /**
     * @var Evento
     */
    private $evento;

    /**
     * @var Certificado
     */
    private $certificado;

    /**
     * @var string
     */
    private $url_sat;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $csc;

    /**
     * @var string
     */
    private $token_ibpt;

    /**
     * @var int
     */
    private $tempo_limite;

    /**
     * @var string
     */
    private $sincrono;

    /**
     * @var int|null
     */
    private $offline;

    /**
     * @param mixed $configuracao array ou instância
     */
    public function __construct($configuracao = [])
    {
        $this->fromArray($configuracao);
    }

    /**
     * Banco que fornece informações sobre items da nota como: Códigos e Taxas
     * @return \DFe\Database\Banco
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Banco que fornece informações sobre items da nota como: Códigos e Taxas
     * @param \DFe\Database\Banco $banco
     * @return self
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;
        return $this;
    }

    /**
     * Emitente da nota fiscal
     * @return \DFe\Entity\Emitente
     */
    public function getEmitente()
    {
        return $this->emitente;
    }

    /**
     * Emitente da nota fiscal
     * @param \DFe\Entity\Emitente $emitente
     * @return self
     */
    public function setEmitente($emitente)
    {
        $this->emitente = $emitente;
        return $this;
    }

    /**
     * Informa a instancia que receberá os eventos do processamento das notas
     * @return Evento
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Informa a instancia que receberá os eventos do processamento das notas
     * @param Evento|null $evento
     * @return self
     */
    public function setEvento($evento)
    {
        $this->evento = $evento;
        return $this;
    }

    /**
     * Certificado para assinar os XMLs
     * @return Certificado
     */
    public function getCertificado()
    {
        return $this->certificado;
    }

    /**
     * Informa o certificado para assinar os XMLs
     * @param Certificado $certificado
     * @return self
     */
    public function setCertificado($certificado)
    {
        $this->certificado = $certificado;
        return $this;
    }

    /**
     * Conteúdo da chave pública ou certificado no formato PEM
     * @return string
     * @deprecated Use getCertificado()->getChavePublica
     */
    public function getChavePublica()
    {
        return $this->getCertificado()->getChavePublica();
    }

    /**
     * Conteúdo da chave pública ou certificado no formato PEM
     * @param string $chave_publica
     * @return self
     * @deprecated Use getCertificado()->setChavePublica
     */
    public function setChavePublica($chave_publica)
    {
        $this->getCertificado()->setChavePublica($chave_publica);
        return $this;
    }

    /**
     * Conteúdo da chave privada do certificado no formato PEM
     * @return string
     * @deprecated Use getCertificado()->getChavePrivada
     */
    public function getChavePrivada()
    {
        return $this->getCertificado()->getChavePrivada();
    }

    /**
     * Conteúdo da chave privada do certificado no formato PEM
     * @param string $chave_privada
     * @return self
     * @deprecated Use getCertificado()->setChavePrivada
     */
    public function setChavePrivada($chave_privada)
    {
        $this->getCertificado()->setChavePrivada($chave_privada);
        return $this;
    }

    /**
     * Informa o caminho do arquivo da chave pública ou certificado no formato
     * PEM
     * @return string
     * @deprecated Use getCertificado()->getArquivoChavePublica
     */
    public function getArquivoChavePublica()
    {
        return $this->getCertificado()->getArquivoChavePublica();
    }

    /**
     * Informa o caminho do arquivo da chave pública ou certificado no formato
     * PEM
     * @param string $arquivo_chave_publica
     * @return self
     * @deprecated Use getCertificado()->setArquivoChavePublica
     */
    public function setArquivoChavePublica($arquivo_chave_publica)
    {
        $this->getCertificado()->setArquivoChavePublica($arquivo_chave_publica);
        return $this;
    }

    /**
     * Caminho do arquivo da chave privada do certificado no formato PEM
     * @return string
     * @deprecated Use getCertificado()->getArquivoChavePrivada
     */
    public function getArquivoChavePrivada()
    {
        return $this->getCertificado()->getArquivoChavePrivada();
    }

    /**
     * Altera o caminho do arquivo da chave privada do certificado no formato PEM
     * @param string $arquivo_chave_privada
     * @return self
     * @deprecated Use getCertificado()->setArquivoChavePrivada
     */
    public function setArquivoChavePrivada($arquivo_chave_privada)
    {
        $this->getCertificado()->setArquivoChavePrivada($arquivo_chave_privada);
        return $this;
    }

    /**
     * Data de expiração do certificado em timestamp
     * @return int
     * @deprecated Use getCertificado()->getExpiracao
     */
    public function getExpiracao()
    {
        return $this->getCertificado()->getExpiracao();
    }

    /**
     * Url para envio da nota em contingência
     *
     * @return string
     */
    public function getUrlSat()
    {
        return $this->url_sat;
    }

    /**
     * Informa a url para envio da nota em contingência
     *
     * @param string|null $url_sat
     *
     * @return self
     */
    public function setUrlSat($url_sat)
    {
        $this->url_sat = $url_sat;
        return $this;
    }

    /**
     * Token do CSC
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Informa o token do CSC, geralmente 000001
     * @param string|null $token
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Código do contribuinte para emissão de nota fiscal
     * @return string
     */
    public function getCSC()
    {
        return $this->csc;
    }

    /**
     * Informa o código do contribuinte para emissão de nota fiscal
     * @param string|null $csc
     * @return self
     */
    public function setCSC($csc)
    {
        $this->csc = $csc;
        return $this;
    }

    /**
     * Token IBPT para consulta de impostos online
     */
    public function getTokenIBPT()
    {
        return $this->token_ibpt;
    }

    public function setTokenIBPT($token_ibpt)
    {
        $this->token_ibpt = $token_ibpt;
        return $this;
    }

    /**
     * Tempo limite em segundos nas conexões com os Web services, 0 para sem tempo limite
     */
    public function getTempoLimite()
    {
        return $this->tempo_limite;
    }

    public function setTempoLimite($tempo_limite)
    {
        $this->tempo_limite = intval($tempo_limite);
        return $this;
    }

    /**
     * Informa se o processo de autorização da nota é síncrono ou assíncrono
     */
    public function getSincrono($normalize = false)
    {
        if (!$normalize) {
            return $this->sincrono;
        }
        return $this->isSincrono() ? '1' : '0';
    }

    /**
     * Informa se o processo de autorização da nota é síncrono ou assíncrono
     */
    public function isSincrono()
    {
        return $this->sincrono == 'Y';
    }

    public function setSincrono($sincrono)
    {
        if (is_bool($sincrono)) {
            $sincrono = $sincrono ? 'Y' : 'N';
        }
        $this->sincrono = $sincrono;
        return $this;
    }

    /**
     * Informa se está operando offline
     * @return mixed offline da Configuracao
     */
    public function getOffline($normalize = false)
    {
        if (!$normalize || is_null($this->offline)) {
            return $this->offline;
        }
        return Util::toDateTime($this->getOffline());
    }

    /**
     * Informa se está operando offline
     */
    public function isOffline()
    {
        return $this->offline + 180 > time();
    }

    /**
     * Entra no modo offline e sai automaticamente após 3 minutos
     */
    public function setOffline($offline)
    {
        if (!is_null($offline) && !is_numeric($offline)) {
            $offline = strtotime($offline);
        }
        $this->offline = $offline;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $configuracao = [];
        $configuracao['banco'] = $this->getBanco();
        $configuracao['emitente'] = $this->getEmitente();
        $configuracao['evento'] = $this->getEvento();
        $configuracao['certificado'] = $this->getCertificado();
        $configuracao['url_sat'] = $this->getUrlSat();
        $configuracao['token'] = $this->getToken();
        $configuracao['csc'] = $this->getCSC();
        $configuracao['token_ibpt'] = $this->getTokenIBPT();
        $configuracao['tempo_limite'] = $this->getTempoLimite();
        $configuracao['sincrono'] = $this->getSincrono();
        $configuracao['offline'] = $this->getOffline($recursive);
        return $configuracao;
    }

    public function fromArray($configuracao = [])
    {
        if ($configuracao instanceof Configuracao) {
            $configuracao = $configuracao->toArray();
        } elseif (!is_array($configuracao)) {
            return $this;
        }
        $this->setBanco(new Estatico($configuracao['banco'] ?? []));
        $this->setEmitente(new Emitente($configuracao['emitente'] ?? []));
        $this->setCertificado(new Certificado($configuracao['certificado'] ?? []));
        $this->setEvento($configuracao['evento'] ?? null);
        $this->setUrlSat($configuracao['token'] ?? null);
        $this->setToken($configuracao['token'] ?? null);
        $this->setCSC($configuracao['csc'] ?? null);
        $this->setTokenIBPT($configuracao['token_ibpt'] ?? null);
        $this->setTempoLimite($configuracao['tempo_limite'] ?? 4);
        $this->setSincrono($configuracao['sincrono'] ?? 'Y');
        return $this;
    }

    /**
     * Certifica que o certificado está informado e é válido
     * @throws \Exception quando o certificado estiver expirado ou não informado
     */
    public function verificaValidadeCertificado()
    {
        if (getenv('APP_ENV') != 'testing') {
            $this->getCertificado()->requerValido();
        }
    }
}
