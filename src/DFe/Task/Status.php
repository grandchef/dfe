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
use DFe\Common\Node;
use DFe\Entity\Estado;

/**
 * Status das respostas de envios para os servidores da SEFAZ
 */
class Status implements Node
{
    private $ambiente;
    private $versao;
    private $status;
    private $motivo;
    private $uf;

    public function __construct($status = [])
    {
        $this->fromArray($status);
    }

    /**
     * Identificação do Ambiente:
     * 1 - Produção
     * 2 - Homologação
     */
    public function getAmbiente($normalize = false)
    {
        if (!$normalize) {
            return $this->ambiente;
        }
        switch ($this->ambiente) {
            case Nota::AMBIENTE_PRODUCAO:
                return '1';
            case Nota::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->ambiente;
    }

    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Versão do Aplicativo que processou a NF-e
     */
    public function getVersao($normalize = false)
    {
        if (!$normalize) {
            return $this->versao;
        }
        return $this->versao;
    }

    public function setVersao($versao)
    {
        $this->versao = $versao;
        return $this;
    }

    /**
     * Código do status da mensagem enviada.
     */
    public function getStatus($normalize = false)
    {
        if (!$normalize) {
            return $this->status;
        }
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Descrição literal do status do serviço solicitado.
     */
    public function getMotivo($normalize = false)
    {
        if (!$normalize) {
            return $this->motivo;
        }
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
        return $this;
    }

    /**
     * código da UF de atendimento
     */
    public function getUF($normalize = false)
    {
        if (!$normalize || is_numeric($this->uf)) {
            return $this->uf;
        }

        $estado = new Estado();
        $estado->setUF($this->uf);
        $estado->checkCodigos();
        return $estado->getCodigo();
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    /**
     * Gera um número único com 15 dígitos
     * @return string Número com 15 dígitos
     */
    public static function genLote()
    {
        return substr(Util::padText(number_format(microtime(true) * 1000000, 0, '', ''), 15), 0, 15);
    }

    public function toArray($recursive = false)
    {
        $status = [];
        $status['ambiente'] = $this->getAmbiente();
        $status['versao'] = $this->getVersao();
        $status['status'] = $this->getStatus();
        $status['motivo'] = $this->getMotivo();
        $status['uf'] = $this->getUF();
        return $status;
    }

    public function fromArray($status = [])
    {
        if ($status instanceof Status) {
            $status = $status->toArray();
        } elseif (!is_array($status)) {
            return $this;
        }
        $this->setAmbiente($status['ambiente'] ?? null);
        $this->setVersao($status['versao'] ?? null);
        $this->setStatus($status['status'] ?? null);
        $this->setMotivo($status['motivo'] ?? null);
        $this->setUF($status['uf'] ?? null);
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'Status');
        Util::appendNode($element, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($element, 'verAplic', $this->getVersao(true));
        Util::appendNode($element, 'cStat', $this->getStatus(true));
        Util::appendNode($element, 'xMotivo', $this->getMotivo(true));
        if (!is_null($this->getUF())) {
            Util::appendNode($element, 'cUF', $this->getUF(true));
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'Status';
        $element = Util::findNode($element, $name);
        $this->setAmbiente(
            Util::loadNode(
                $element,
                'tpAmb',
                'Tag "tpAmb" não encontrada no Status'
            )
        );
        $this->setVersao(
            Util::loadNode(
                $element,
                'verAplic',
                'Tag "verAplic" não encontrada no Status'
            )
        );
        $this->setStatus(
            Util::loadNode(
                $element,
                'cStat',
                'Tag "cStat" não encontrada no Status'
            )
        );
        $this->setMotivo(
            Util::loadNode(
                $element,
                'xMotivo',
                'Tag "xMotivo" não encontrada no Status'
            )
        );
        $this->setUF(Util::loadNode($element, 'cUF'));
        return $element;
    }
}
