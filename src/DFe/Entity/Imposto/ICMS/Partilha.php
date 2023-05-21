<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS;

use DFe\Common\Util;

/**
 * Partilha do ICMS entre a UF de origem e UF de destino ou a UF definida
 * na legislação
 * Operação interestadual para consumidor final com partilha
 * do ICMS  devido na operação entre a UF de origem e a UF do destinatário
 * ou ou a UF definida na legislação. (Ex. UF da concessionária de entrega
 * do  veículos)
 */
class Partilha extends Mista
{
    private $operacao;
    private $uf;

    public function __construct($partilha = [])
    {
        parent::__construct($partilha);
    }

    /**
     * Percentual para determinação do valor  da Base de Cálculo da operação
     * própria.
     */
    public function getOperacao($normalize = false)
    {
        if (!$normalize) {
            return $this->operacao;
        }
        return Util::toFloat($this->operacao);
    }

    public function setOperacao($operacao)
    {
        $this->operacao = $operacao;
        return $this;
    }

    /**
     * Sigla da UF para qual é devido o ICMS ST da operação.
     */
    public function getUF($normalize = false)
    {
        if (!$normalize) {
            return $this->uf;
        }
        return $this->uf;
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $partilha = parent::toArray($recursive);
        $partilha['operacao'] = $this->getOperacao();
        $partilha['uf'] = $this->getUF();
        return $partilha;
    }

    public function fromArray($partilha = [])
    {
        if ($partilha instanceof Partilha) {
            $partilha = $partilha->toArray();
        } elseif (!is_array($partilha)) {
            return $this;
        }
        parent::fromArray($partilha);
        if (isset($partilha['operacao'])) {
            $this->setOperacao($partilha['operacao']);
        } else {
            $this->setOperacao(null);
        }
        if (isset($partilha['uf'])) {
            $this->setUF($partilha['uf']);
        } else {
            $this->setUF(null);
        }
        if (!isset($partilha['tributacao'])) {
            $this->setTributacao('10');
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name) ? 'ICMSPart' : $name);
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'pBCOp', $this->getOperacao(true));
        Util::appendNode($element, 'UFST', $this->getUF(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMSPart' : $name;
        $element = parent::loadNode($element, $name);
        $this->setOperacao(
            Util::loadNode(
                $element,
                'pBCOp',
                'Tag "pBCOp" do campo "Operacao" não encontrada na Partilha'
            )
        );
        $this->setUF(
            Util::loadNode(
                $element,
                'UFST',
                'Tag "UFST" do campo "UF" não encontrada na Partilha'
            )
        );
        return $element;
    }
}
