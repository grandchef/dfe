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
use DFe\Entity\Imposto\Fundo\Retido;

/**
 * Tributação pelo ICMS
 * 60 - ICMS cobrado anteriormente por substituição
 * tributária
 */
class Cobrado extends Generico
{
    private $valor;

    public function __construct($cobrado = [])
    {
        parent::__construct($cobrado);
    }

    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->valor;
        }
        return Util::toCurrency($this->valor);
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $cobrado = parent::toArray($recursive);
        $cobrado['valor'] = $this->getValor();
        return $cobrado;
    }

    public function fromArray($cobrado = [])
    {
        if ($cobrado instanceof Cobrado) {
            $cobrado = $cobrado->toArray();
        } elseif (!is_array($cobrado)) {
            return $this;
        }
        parent::fromArray($cobrado);
        $this->setValor($cobrado['valor'] ?? null);
        if (!isset($cobrado['fundo']) || !($this->getFundo() instanceof Retido)) {
            $this->setFundo(new Retido());
        }
        if (!isset($cobrado['tributacao'])) {
            $this->setTributacao('60');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $element = parent::getNode($name ?? 'ICMS60');
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vBCSTRet', $this->getBase(true));
        Util::appendNode($element, 'pST', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSSTRet', $this->getValor(true));
        return $this->exportFundo($element);
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'ICMS60';
        $element = Util::findNode($element, $name);
        $this->setOrigem(
            Util::loadNode(
                $element,
                'orig',
                'Tag "orig" do campo "Origem" não encontrada'
            )
        );
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCSTRet',
                'Tag "vBCSTRet" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pST'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vICMSSTRet',
                'Tag "vICMSSTRet" do campo "Valor" não encontrada'
            )
        );
        $this->importFundo($element);
        return $element;
    }
}
