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
 * Tributação pelo ICMS
 * 90 - Outras, estende de Normal
 */
class Generico extends Mista
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
            $this->setTributacao('90');
        }
        $this->getNormal()->setTributacao('90');
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        if (is_null($this->getModalidade()) && is_null($this->getNormal()->getModalidade())) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $element = $dom->createElement($name ?? 'ICMS90');
            Util::appendNode($element, 'orig', $this->getOrigem(true));
            Util::appendNode($element, 'CST', $this->getTributacao(true));
            return $element;
        }
        $element = parent::getNode($name ?? 'ICMS90');
        $dom = $element->ownerDocument;
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'ICMS90';
        $element = Util::findNode($element, $name);
        $_mod = $element->getElementsByTagName('modBC');
        $_mod_st = $element->getElementsByTagName('modBCST');
        if ($_mod->length > 0 || $_mod_st->length > 0) {
            $element = parent::loadNode($element, $name);
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
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        return $element;
    }
}
