<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\V4;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Imposto;
use DFe\Entity\Produto;

/**
 * Produto ou serviço que está sendo vendido ou prestado e será adicionado
 * na nota fiscal
 */
class ProdutoLoader implements Loader
{
    public function __construct(private Produto $produto)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'det');
        $attr = $dom->createAttribute('nItem');
        $attr->value = $this->produto->getItem(true);
        $element->appendChild($attr);

        $produto = $dom->createElement('prod');
        Util::appendNode($produto, 'cProd', $this->produto->getCodigo(true));
        Util::appendNode($produto, 'cEAN', $this->produto->getCodigoBarras(true));
        Util::appendNode($produto, 'xProd', $this->produto->getDescricao(true));
        Util::appendNode($produto, 'NCM', $this->produto->getNCM(true));
        if (!is_null($this->produto->getCEST())) {
            Util::appendNode($produto, 'CEST', $this->produto->getCEST(true));
        }
        if (!is_null($this->produto->getExcecao())) {
            Util::appendNode($produto, 'EXTIPI', $this->produto->getExcecao(true));
        }
        Util::appendNode($produto, 'CFOP', $this->produto->getCFOP(true));
        Util::appendNode($produto, 'uCom', $this->produto->getUnidade(true));
        Util::appendNode($produto, 'qCom', $this->produto->getQuantidade(true));
        Util::appendNode($produto, 'vUnCom', $this->produto->getPrecoUnitario(true));
        Util::appendNode($produto, 'vProd', $this->produto->getPreco(true));
        Util::appendNode($produto, 'cEANTrib', $this->produto->getCodigoTributario(true));
        Util::appendNode($produto, 'uTrib', $this->produto->getUnidade(true));
        Util::appendNode($produto, 'qTrib', $this->produto->getTributada(true));
        Util::appendNode($produto, 'vUnTrib', $this->produto->getPrecoTributavel(true));
        if (Util::isGreater($this->produto->getFrete(), 0.00)) {
            Util::appendNode($produto, 'vFrete', $this->produto->getFrete(true));
        }
        if (Util::isGreater($this->produto->getSeguro(), 0.00)) {
            Util::appendNode($produto, 'vSeg', $this->produto->getSeguro(true));
        }
        if (Util::isGreater($this->produto->getDesconto(), 0.00)) {
            Util::appendNode($produto, 'vDesc', $this->produto->getDesconto(true));
        }
        if (Util::isGreater($this->produto->getDespesas(), 0.00)) {
            Util::appendNode($produto, 'vOutro', $this->produto->getDespesas(true));
        }
        Util::appendNode($produto, 'indTot', $this->produto->getMultiplicador(true));
        if (!is_null($this->produto->getPedido())) {
            Util::appendNode($produto, 'xPed', $this->produto->getPedido(true));
        }
        Util::appendNode($produto, 'nItemPed', $this->produto->getItem(true));
        $element->appendChild($produto);

        $imposto = $dom->createElement('imposto');
        $grupos = [];
        $_impostos = $this->produto->getImpostos();
        foreach ($_impostos as $_imposto) {
            if (is_null($_imposto->getBase())) {
                $_imposto->setBase($this->produto->getBase());
            }
            $grupos[$_imposto->getGrupo(true)][] = $_imposto;
        }
        $imposto_info = $this->produto->getImpostoInfo();
        $this->produto->setTributos($imposto_info['total']);
        Util::appendNode($imposto, 'vTotTrib', Util::toCurrency($imposto_info['total']));
        foreach ($grupos as $tag => $_grupo) {
            $grupo = $dom->createElement($tag);
            foreach ($_grupo as $_imposto) {
                $node = $_imposto->getNode($version);
                $node = $dom->importNode($node, true);
                $grupo->appendChild($node);
            }
            $imposto->appendChild($grupo);
        }
        $element->appendChild($imposto);
        // TODO: verificar se é obrigatório a informação adicional abaixo
        $complemento = Produto::addNodeInformacoes($imposto_info, $element);
        $this->produto->setComplemento($complemento);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'det';
        $element = Util::findNode($element, $name);
        $root = $element;
        $element = (new TotalLoader($this->produto))->loadNode($element, $name, $version);
        $this->produto->setItem(Util::loadNode($element, 'nItemPed'));
        $this->produto->setPedido(Util::loadNode($element, 'xPed'));
        $this->produto->setCodigo(
            Util::loadNode(
                $element,
                'cProd',
                'Tag "cProd" do campo "Codigo" não encontrada no Produto'
            )
        );
        $this->produto->setCodigoTributario(
            Util::loadNode(
                $element,
                'cEANTrib',
                'Tag "cEANTrib" do campo "CodigoTributario" não encontrada no Produto'
            )
        );
        $this->produto->setCodigoBarras(
            Util::loadNode(
                $element,
                'cEAN',
                'Tag "cEAN" do campo "CodigoBarras" não encontrada no Produto'
            )
        );
        $this->produto->setDescricao(
            Util::loadNode(
                $element,
                'xProd',
                'Tag "xProd" do campo "Descricao" não encontrada no Produto'
            )
        );
        $this->produto->setUnidade(
            Util::loadNode(
                $element,
                'uCom',
                'Tag "uCom" do campo "Unidade" não encontrada no Produto'
            )
        );
        $this->produto->setMultiplicador(
            Util::loadNode(
                $element,
                'indTot',
                'Tag "indTot" do campo "Multiplicador" não encontrada no Produto'
            )
        );
        $this->produto->setQuantidade(
            Util::loadNode(
                $element,
                'qCom',
                'Tag "qCom" do campo "Quantidade" não encontrada no Produto'
            )
        );
        $this->produto->setTributada(
            Util::loadNode(
                $element,
                'qTrib',
                'Tag "qTrib" do campo "Tributada" não encontrada no Produto'
            )
        );
        $this->produto->setExcecao(Util::loadNode($element, 'EXTIPI'));
        $this->produto->setCFOP(
            Util::loadNode(
                $element,
                'CFOP',
                'Tag "CFOP" do campo "CFOP" não encontrada no Produto'
            )
        );
        $this->produto->setNCM(
            Util::loadNode(
                $element,
                'NCM',
                'Tag "NCM" do campo "NCM" não encontrada no Produto'
            )
        );
        $this->produto->setCEST(Util::loadNode($element, 'CEST'));
        $impostos = [];
        $_fields = $root->getElementsByTagName('imposto');
        if ($_fields->length == 0) {
            throw new \Exception('Tag "imposto" da lista de "Impostos" não encontrada no Produto', 404);
        }
        $imposto_node = $_fields->item(0);
        $this->produto->setTributos(Util::loadNode($imposto_node, 'vTotTrib'));
        $_items = $imposto_node->childNodes;
        $total = new \DFe\Entity\Imposto\Total();
        foreach ($_items as $_item) {
            if (!$_item->hasChildNodes() || $_item->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $total->setGrupo($_item->nodeName);
            foreach ($_item->childNodes as $_subitem) {
                if ($_subitem->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }
                $imposto = Imposto::loadImposto($_subitem);
                if ($imposto === false) {
                    continue;
                }
                $imposto->setGrupo($total->getGrupo());
                $impostos[] = $imposto;
            }
        }
        $this->produto->setImpostos($impostos);
        $this->produto->setComplemento(Util::loadNode($root, 'infAdProd'));
        return $element;
    }
}
