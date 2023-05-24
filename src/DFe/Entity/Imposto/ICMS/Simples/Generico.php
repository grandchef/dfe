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
 * Tributação do ICMS pelo SIMPLES NACIONAL, CRT=1 – Simples Nacional e
 * CSOSN=900 (v2.0)
 */
class Generico extends Cobranca
{
    public function __construct($generico = [])
    {
        parent::__construct($generico);
    }

    public function toArray($recursive = false)
    {
        $generico = parent::toArray($recursive);
        return $generico;
    }

    public function fromArray($generico = [])
    {
        if ($generico instanceof Generico) {
            $generico = $generico->toArray();
        } elseif (!is_array($generico)) {
            return $this;
        }
        parent::fromArray($generico);
        if (!isset($generico['tributacao'])) {
            $this->setTributacao('900');
        }
        $this->getNormal()->setTributacao('900');
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (is_null($this->getModalidade()) && is_null($this->getNormal()->getModalidade())) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $element = $dom->createElement($name ?? 'ICMSSN900');
            Util::appendNode($element, 'orig', $this->getOrigem(true));
            Util::appendNode($element, 'CSOSN', $this->getTributacao(true));
            return $element;
        }
        $element = parent::getNode($version, $name ?? 'ICMSSN900');
        $dom = $element->ownerDocument;
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'ICMSSN900';
        $element = Util::findNode($element, $name);
        $_cred = $element->getElementsByTagName('pCredSN');
        $_mod_st = $element->getElementsByTagName('modBCST');
        if ($_cred->length > 0 || $_mod_st->length > 0) {
            $element = parent::loadNode($element, $name, $version);
            return $element;
        }
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
        return $element;
    }
}
