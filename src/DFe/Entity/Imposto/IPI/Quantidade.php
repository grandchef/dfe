<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\IPI;

use DFe\Common\Util;
use DFe\Entity\Imposto;

/**
 * Quantidade x valor Unidade de Produto
 */
class Quantidade extends Imposto
{
    public function __construct($quantidade = [])
    {
        parent::__construct($quantidade);
        $this->setGrupo(self::GRUPO_IPI);
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

    public function getPreco($normalize = false)
    {
        if (!$normalize) {
            return $this->getAliquota();
        }
        return Util::toCurrency($this->getPreco(), 4);
    }

    public function setPreco($preco)
    {
        return $this->setAliquota($preco);
    }

    /**
     * Calcula o valor do imposto com base na quantidade e no preço
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getQuantidade() * $this->getPreco();
        }
        return Util::toCurrency($this->getValor());
    }

    public function toArray($recursive = false)
    {
        $quantidade = parent::toArray($recursive);
        return $quantidade;
    }

    public function fromArray($quantidade = [])
    {
        if ($quantidade instanceof Quantidade) {
            $quantidade = $quantidade->toArray();
        } elseif (!is_array($quantidade)) {
            return $this;
        }
        parent::fromArray($quantidade);
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'IPITrib' : $name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'qUnid', $this->getQuantidade(true));
        Util::appendNode($element, 'vUnid', $this->getPreco(true));
        Util::appendNode($element, 'vIPI', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'IPITrib';
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
                'qUnid',
                'Tag "qUnid" do campo "Quantidade" não encontrada'
            )
        );
        $this->setPreco(
            Util::loadNode(
                $element,
                'vUnid',
                'Tag "vUnid" do campo "Preco" não encontrada'
            )
        );
        return $element;
    }
}
