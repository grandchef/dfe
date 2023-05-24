<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\COFINS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Quantidade extends Imposto
{
    public function __construct($cofins = [])
    {
        parent::__construct($cofins);
    }

    public function getQuantidade($normalize = false)
    {
        if (!$normalize) {
            return $this->getBase();
        }
        return Util::toFloat($this->getBase());
    }

    public function setQuantidade($quantidade)
    {
        return $this->setBase($quantidade);
    }

    /**
     * Calcula o valor do imposto com base na quantidade e no valor da aliquota
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getQuantidade() * $this->getAliquota();
        }
        $valor = $this->getValor();
        return Util::toCurrency($valor);
    }

    public function toArray($recursive = false)
    {
        $cofins = parent::toArray($recursive);
        return $cofins;
    }

    public function fromArray($cofins = [])
    {
        if ($cofins instanceof Quantidade) {
            $cofins = $cofins->toArray();
        } elseif (!is_array($cofins)) {
            return $this;
        }
        parent::fromArray($cofins);
        $this->setGrupo(self::GRUPO_COFINS);
        if (!isset($cofins['tributacao'])) {
            $this->setTributacao('03');
        }
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'COFINSQtde');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'qBCProd', $this->getQuantidade(true));
        Util::appendNode($element, 'vAliqProd', $this->getAliquota(true));
        Util::appendNode($element, 'vCOFINS', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'COFINSQtde';
        $element = Util::findNode($element, $name);
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setQuantidade(
            Util::loadNode(
                $element,
                'qBCProd',
                'Tag "qBCProd" do campo "Quantidade" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'vAliqProd',
                'Tag "vAliqProd" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
