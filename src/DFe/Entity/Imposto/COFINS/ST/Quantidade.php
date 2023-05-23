<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\COFINS\ST;

use DFe\Common\Util;

/**
 * Quantidade Vendida x Alíquota por Unidade de Produto
 */
class Quantidade extends \DFe\Entity\Imposto\COFINS\Quantidade
{
    public function __construct($quantidade = [])
    {
        parent::__construct($quantidade);
        $this->setGrupo(self::GRUPO_COFINSST);
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
        $element = parent::getNode($name ?? 'COFINSST');
        $item = $element->getElementsByTagName('CST')->item(0);
        $element->removeChild($item);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'COFINSST';
        $element = Util::findNode($element, $name);
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
