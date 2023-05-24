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
 * 20 - Com redução de base de cálculo, estende de
 * Normal
 */
class Reducao extends Normal
{
    private $reducao;

    public function __construct($reducao = [])
    {
        parent::__construct($reducao);
    }

    public function getReducao($normalize = false)
    {
        if (!$normalize) {
            return $this->reducao;
        }
        return Util::toFloat($this->reducao);
    }

    public function setReducao($reducao)
    {
        if (!empty($reducao)) {
            $reducao = floatval($reducao);
        }
        $this->reducao = $reducao;
        return $this;
    }

    /**
     * Calcula o valor do reduzido da base de cálculo
     */
    public function getReduzido($normalize = false)
    {
        if ($normalize) {
            return Util::toCurrency($this->getReduzido());
        }
        return ($this->getBase() * (100.0 - $this->getReducao())) / 100.0;
    }

    public function toArray($recursive = false)
    {
        $reducao = parent::toArray($recursive);
        $reducao['reducao'] = $this->getReducao();
        return $reducao;
    }

    public function fromArray($reducao = [])
    {
        if ($reducao instanceof Reducao) {
            $reducao = $reducao->toArray();
        } elseif (!is_array($reducao)) {
            return $this;
        }
        parent::fromArray($reducao);
        $this->setReducao($reducao['reducao'] ?? null);
        if (!isset($reducao['tributacao'])) {
            $this->setTributacao('20');
        }
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $element = parent::getNode($name ?? 'ICMS20');
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'pRedBC', $this->getReducao(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'ICMS20';
        $element = parent::loadNode($element, $name);
        $this->setReducao(
            Util::loadNode(
                $element,
                'pRedBC',
                'Tag "pRedBC" do campo "Reducao" não encontrada na Reducao'
            )
        );
        return $element;
    }
}
