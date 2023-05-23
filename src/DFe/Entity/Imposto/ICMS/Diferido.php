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
 * Tributção pelo ICMS
 * 51 - Diferimento
 * A exigência do preenchimento das
 * informações do ICMS diferido fica à critério de cada UF, estende de
 * Reducao
 */
class Diferido extends Reducao
{
    private $diferimento;

    public function __construct($diferido = [])
    {
        parent::__construct($diferido);
    }

    /**
     * Percentual do diferemento
     */
    public function getDiferimento($normalize = false)
    {
        if (!$normalize) {
            return $this->diferimento;
        }
        return Util::toFloat($this->diferimento);
    }

    public function setDiferimento($diferimento)
    {
        $this->diferimento = $diferimento;
        return $this;
    }

    /**
     * Valor do ICMS da Operação
     */
    public function getOperacao($normalize = false)
    {
        if (!$normalize) {
            return $this->getReduzido() * $this->getAliquota() / 100.0;
        }
        return Util::toCurrency($this->getOperacao());
    }

    /**
     * Valor do ICMS do diferimento
     */
    public function getDiferido($normalize = false)
    {
        if (!$normalize) {
            return $this->getDiferimento() * $this->getOperacao() / 100.0;
        }
        return Util::toCurrency($this->getDiferido());
    }

    /**
     * Calcula o valor do imposto
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->getOperacao() - $this->getDiferido();
        }
        return Util::toCurrency($this->getValor());
    }

    public function toArray($recursive = false)
    {
        $diferido = parent::toArray($recursive);
        $diferido['diferimento'] = $this->getDiferimento();
        return $diferido;
    }

    public function fromArray($diferido = [])
    {
        if ($diferido instanceof Diferido) {
            $diferido = $diferido->toArray();
        } elseif (!is_array($diferido)) {
            return $this;
        }
        parent::fromArray($diferido);
        $this->setDiferimento($diferido['diferimento'] ?? null);
        if (!isset($diferido['tributacao'])) {
            $this->setTributacao('51');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        if (is_null($this->getDiferimento())) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $element = $dom->createElement($name ?? 'ICMS51');
            Util::appendNode($element, 'orig', $this->getOrigem(true));
            Util::appendNode($element, 'CST', $this->getTributacao(true));
            return $element;
        }
        $element = parent::getNode($name ?? 'ICMS51');
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vICMSOp', $this->getOperacao(true));
        Util::appendNode($element, 'pDif', $this->getDiferimento(true));
        Util::appendNode($element, 'vICMSDif', $this->getDiferido(true));
        if (Util::isEqual(floatval($this->getReducao()), 0.0)) {
            $item = $element->getElementsByTagName('pRedBC')->item(0);
            $element->removeChild($item);
        }
        if (Util::isEqual(floatval($this->getDiferimento()), 100.0)) {
            $item = $element->getElementsByTagName('vICMS')->item(0);
            $element->removeChild($item);
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'ICMS51';
        $element = Util::findNode($element, $name);
        $dom = $element->ownerDocument;
        /** @var \DOMElement */
        $element = $dom->importNode($element, true);
        $_dif = $element->getElementsByTagName('pDif');
        if ($_dif->length > 0) {
            $node_added = false;
            $save_element = $element;
            $_fields = $element->getElementsByTagName('pRedBC');
            if ($_fields->length == 0) {
                Util::appendNode($element, 'pRedBC', '0.0000');
                $node_added = true;
            }
            $element = parent::loadNode($element, $name);
            if ($node_added) {
                $item = $save_element->getElementsByTagName('pRedBC')->item(0);
                $save_element->removeChild($item);
            }
            $diferimento = $_dif->item(0)->nodeValue;
            $this->setDiferimento($diferimento);
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
        $this->setDiferimento(null);
        return $element;
    }
}
