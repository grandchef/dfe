<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe\V008;

use DOMElement;
use DFe\Common\Util;
use DFe\Entity\Total;
use DFe\Common\Loader;

/**
 * Dados dos totais da NF-e e do produto
 */
class TotalLoader implements Loader
{
    public function __construct(private Total $total)
    {
    }

    /**
     * Cria um nó XML do total de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'prod');
        Util::appendNode($element, 'vProd', $this->total->getProdutos(true));
        if (!is_null($this->total->getDesconto())) {
            Util::appendNode($element, 'vDesc', $this->total->getDesconto(true));
        }
        if (!is_null($this->total->getDespesas())) {
            Util::appendNode($element, 'vOutro', $this->total->getDespesas(true));
        }
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = Util::findNode($element,  $name ?? 'prod');
        $this->total->setProdutos(
            Util::loadNode(
                $element,
                'vProd',
                'Tag "vProd" não encontrada no Total ou Produto'
            )
        );
        $this->total->setDesconto(Util::loadNode($element, 'vDesc'));
        $this->total->setDespesas(Util::loadNode($element, 'vOutro'));
        $this->total->setTributos(Util::loadNode($element, 'vICMS'));
        return $element;
    }
}
