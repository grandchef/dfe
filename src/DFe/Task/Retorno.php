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

use DFe\Common\Util;

class Retorno extends Status
{
    private $data_recebimento;

    public function __construct($retorno = [])
    {
        parent::__construct($retorno);
    }

    public function getDataRecebimento($normalize = false)
    {
        if (!$normalize || is_null($this->data_recebimento)) {
            return $this->data_recebimento;
        }
        return Util::toDateTime($this->data_recebimento);
    }

    public function setDataRecebimento($data_recebimento)
    {
        if (!is_null($data_recebimento) && !is_numeric($data_recebimento)) {
            $data_recebimento = strtotime($data_recebimento);
        }
        $this->data_recebimento = $data_recebimento;
        return $this;
    }

    /**
     * Informa se a nota foi autorizada no prazo ou fora do prazo
     */
    public function isAutorizado()
    {
        return in_array($this->getStatus(), ['100', '150']);
    }

    /**
     * Informa se a nota está cancelada
     */
    public function isCancelado()
    {
        return in_array($this->getStatus(), ['101', '151']);
    }

    /**
     * Informa se o lote já foi processado e já tem um protocolo
     */
    public function isProcessado()
    {
        return $this->getStatus() == '104';
    }

    /**
     * Informa se o lote foi recebido com sucesso
     */
    public function isRecebido()
    {
        return in_array($this->getStatus(), ['103', '105']);
    }

    /**
     * Informa se a nota foi denegada
     */
    public function isDenegada()
    {
        return in_array($this->getStatus(), ['110', '301', '302', '303']);
    }

    /**
     * Informa se a nota da consulta não foi autorizada ou se não existe
     */
    public function isInexistente()
    {
        return $this->getStatus() == '217';
    }

    /**
     * Informa se os serviços da SEFAZ estão paralisados ou em manutenção
     */
    public function isParalisado()
    {
        return in_array($this->getStatus(), ['108', '109']);
    }

    public function toArray($recursive = false)
    {
        $retorno = parent::toArray($recursive);
        $retorno['data_recebimento'] = $this->getDataRecebimento($recursive);
        return $retorno;
    }

    public function fromArray($retorno = [])
    {
        if ($retorno instanceof Retorno) {
            $retorno = $retorno->toArray();
        } elseif (!is_array($retorno)) {
            return $this;
        }
        parent::fromArray($retorno);
        $this->setDataRecebimento($retorno['data_recebimento'] ?? null);
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $element = parent::getNode(is_null($name) ? '' : $name);
        $status = $element->getElementsByTagName('cStat')->item(0);
        if (!is_null($this->getDataRecebimento())) {
            Util::appendNode($element, 'dhRecbto', $this->getDataRecebimento(true), $status);
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'Retorno';
        $retorno = parent::loadNode($element, $name);
        $this->setDataRecebimento(Util::loadNode($retorno, 'dhRecbto'));
        return $retorno;
    }
}
