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
 * 00 - Tributada integralmente, estende de Normal
 */
class Integral extends Normal
{
    public function __construct($integral = [])
    {
        parent::__construct($integral);
    }

    /**
     * Altera o valor do Fundo para o informado no parâmetro
     * interceptando a alteração do fundo para aplicar a base integral
     * @param mixed $fundo novo valor para Fundo
     * @return self A própria instância da classe
     */
    public function setFundo($fundo)
    {
        parent::setFundo($fundo);
        if (!is_null($this->getFundo())) {
            $this->getFundo()->setBase($this->getBase());
        }
        return $this;
    }

    /**
     * Altera o valor do Base para o informado no parâmetro
     * interceptando a alteração do base para aplicar a base integral no fundo
     * @param mixed $base novo valor para Base
     * @return self A própria instância da classe
     */
    public function setBase($base)
    {
        parent::setBase($base);
        if (!is_null($this->getFundo())) {
            $this->getFundo()->setBase($this->getBase());
        }
        return $this;
    }

    public function toArray($recursive = false)
    {
        $integral = parent::toArray($recursive);
        return $integral;
    }

    public function fromArray($integral = [])
    {
        if ($integral instanceof Integral) {
            $integral = $integral->toArray();
        } elseif (!is_array($integral)) {
            return $this;
        }
        parent::fromArray($integral);
        if (!isset($integral['tributacao'])) {
            $this->setTributacao('00');
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name) ? 'ICMS00' : $name);
        if (Util::nodeExists($element, 'vBCFCP')) {
            $node = Util::findNode($element, 'vBCFCP');
            $node->parentNode->removeChild($node);
        }
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMS00' : $name;
        $element = parent::loadNode($element, $name);
        return $element;
    }
}
