<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS\Simples;

use DFe\Common\Util;

/**
 * Tributada pelo Simples Nacional sem permissão de crédito e com cobrança
 * do ICMS por substituição tributária
 */
class Parcial extends \DFe\Entity\Imposto\ICMS\Parcial
{
    public function __construct($parcial = [])
    {
        parent::__construct($parcial);
    }

    public function toArray($recursive = false)
    {
        $parcial = parent::toArray($recursive);
        return $parcial;
    }

    public function fromArray($parcial = [])
    {
        if ($parcial instanceof Parcial) {
            $parcial = $parcial->toArray();
        } elseif (!is_array($parcial)) {
            return $this;
        }
        parent::fromArray($parcial);
        if (!isset($parcial['tributacao'])) {
            $this->setTributacao('202');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'ICMSSN202');
        Util::appendNode($element, 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CSOSN', $this->getTributacao(true));
        Util::appendNode($element, 'modBCST', $this->getModalidade(true));
        Util::appendNode($element, 'pMVAST', $this->getMargem(true));
        Util::appendNode($element, 'pRedBCST', $this->getReducao(true));
        Util::appendNode($element, 'vBCST', $this->getBase(true));
        Util::appendNode($element, 'pICMSST', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSST', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'ICMSSN202';
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
                'CSOSN',
                'Tag "CSOSN" do campo "Tributacao" não encontrada'
            )
        );
        $this->setModalidade(
            Util::loadNode(
                $element,
                'modBCST',
                'Tag "modBCST" do campo "Modalidade" não encontrada'
            )
        );
        $this->setMargem(
            Util::loadNode(
                $element,
                'pMVAST',
                'Tag "pMVAST" do campo "Margem" não encontrada'
            )
        );
        $this->setReducao(
            Util::loadNode(
                $element,
                'pRedBCST',
                'Tag "pRedBCST" do campo "Reducao" não encontrada'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCST',
                'Tag "vBCST" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pICMSST',
                'Tag "pICMSST" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
