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

use DFe\Core\Nota;
use DFe\Common\Util;
use DFe\Exception\ValidationException;

class Situacao extends Retorno
{
    private $chave;
    private $modelo;

    public const TAG_RETORNO = 'retConsSitNFe';

    public function __construct($situacao = [])
    {
        parent::__construct($situacao);
    }

    /**
     * Chaves de acesso da NF-e, compostas por: UF do emitente, AAMM da emissão
     * da NFe, CNPJ do emitente, modelo, série e número da NF-e e código
     * numérico+DV.
     */
    public function getChave($normalize = false)
    {
        if (!$normalize) {
            return $this->chave;
        }
        return $this->chave;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     * @param boolean $normalize informa se o modelo deve estar no formato do XML
     * @return mixed modelo do Envio
     */
    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        switch ($this->modelo) {
            case Nota::MODELO_NFE:
                return '55';
            case Nota::MODELO_NFCE:
                return '65';
        }
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     * @param mixed $modelo novo valor para Modelo
     *
     * @return self A própria instância da classe
     */
    public function setModelo($modelo)
    {
        switch ($modelo) {
            case '55':
                $modelo = Nota::MODELO_NFE;
                break;
            case '65':
                $modelo = Nota::MODELO_NFCE;
                break;
        }
        $this->modelo = $modelo;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $situacao = parent::toArray($recursive);
        $situacao['chave'] = $this->getChave();
        $situacao['modelo'] = $this->getModelo();
        return $situacao;
    }

    public function fromArray($situacao = [])
    {
        if ($situacao instanceof Situacao) {
            $situacao = $situacao->toArray();
        } elseif (!is_array($situacao)) {
            return $this;
        }
        parent::fromArray($situacao);
        $this->setChave($situacao['chave'] ?? null);
        $this->setModelo($situacao['modelo'] ?? null);
        return $this;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_PROTOCOLO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $this->setVersao($envio->getVersao());
        $dom = $this->validar($dom);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp->documentElement);
        if ($this->isAutorizado()) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($resp->documentElement);
            return $protocolo;
        } elseif ($this->isCancelado()) {
            $evento = new Evento();
            $evento->loadStatusNode($resp->documentElement, self::TAG_RETORNO);
            $evento->loadNode($resp->documentElement);
            return $evento;
        }
        return $this;
    }

    public function consulta($nota = null)
    {
        if (!is_null($nota)) {
            $this->setChave($nota->getID());
            $this->setAmbiente($nota->getAmbiente());
            $this->setModelo($nota->getModelo());
        }
        $dom = $this->getNode()->ownerDocument;
        $retorno = $this->envia($dom);
        if ($retorno instanceof Protocolo && $retorno->isAutorizado() && !is_null($nota)) {
            $nota->setProtocolo($retorno);
        }
        return $retorno;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'consSitNFe');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        Util::appendNode($element, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($element, 'xServ', 'CONSULTAR');
        Util::appendNode($element, 'chNFe', $this->getChave(true));
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name = is_null($name) ? self::TAG_RETORNO : $name;
        $element = parent::loadNode($element, $name);
        $this->setChave(Util::loadNode($element, 'chNFe'));
        return $element;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/NFe/v4.0.0/consSitNFe_v' . $this->getVersao() . '.xsd';
        if (!file_exists($xsd_file)) {
            throw new \Exception(sprintf('O arquivo "%s" de esquema XSD não existe!', $xsd_file), 404);
        }
        // Enable user error handling
        $save = libxml_use_internal_errors(true);
        if ($dom->schemaValidate($xsd_file)) {
            libxml_use_internal_errors($save);
            return $dom;
        }
        $msg = [];
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $msg[] = 'Não foi possível validar o XML: ' . $error->message;
        }
        libxml_clear_errors();
        libxml_use_internal_errors($save);
        throw new ValidationException($msg);
    }
}
