<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\PIS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Quantidade extends Imposto
{
    public function __construct($pis = [])
    {
        parent::__construct($pis);
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
        $pis = parent::toArray($recursive);
        return $pis;
    }

    public function fromArray($pis = [])
    {
        if ($pis instanceof Quantidade) {
            $pis = $pis->toArray();
        } elseif (!is_array($pis)) {
            return $this;
        }
        parent::fromArray($pis);
        $this->setGrupo(self::GRUPO_PIS);
        if (!isset($pis['tributacao'])) {
            $this->setTributacao('03');
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'PISQtde' : $name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'qBCProd', $this->getQuantidade(true));
        Util::appendNode($element, 'vAliqProd', $this->getAliquota(true));
        Util::appendNode($element, 'vPIS', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'PISQtde' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
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
