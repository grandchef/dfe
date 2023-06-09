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
 * Tributada pelo Simples Nacional com permissão de crédito
 */
class Normal extends \DFe\Entity\Imposto\ICMS\Normal
{
    public function __construct($normal = [])
    {
        parent::__construct($normal);
    }

    public function toArray($recursive = false)
    {
        $normal = parent::toArray($recursive);
        return $normal;
    }

    public function fromArray($normal = [])
    {
        if ($normal instanceof Normal) {
            $normal = $normal->toArray();
        } elseif (!is_array($normal)) {
            return $this;
        }
        parent::fromArray($normal);
        if (!isset($normal['tributacao'])) {
            $this->setTributacao('101');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'ICMSSN101');
        Util::appendNode($element, strpos($version, 'CFe') !== false ? 'Orig' : 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CSOSN', $this->getTributacao(true));
        Util::appendNode($element, 'pCredSN', $this->getAliquota(true));
        Util::appendNode($element, 'vCredICMSSN', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'ICMSSN101';
        $element = Util::findNode($element, $name);
        $this->setOrigem(
            Util::loadNode(
                $element,
                strpos($version, 'CFe') !== false ? 'Orig' : 'orig',
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
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pCredSN',
                'Tag "pCredSN" do campo "Aliquota" não encontrada'
            )
        );
        $valor = floatval(
            Util::loadNode(
                $element,
                'vCredICMSSN',
                'Tag "vCredICMSSN" do campo "Valor" não encontrada'
            )
        );
        $this->setBase($valor * 100.0 / $this->getAliquota());
        return $element;
    }
}
