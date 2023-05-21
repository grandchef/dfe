<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity;

use DFe\Common\Util;

/**
 * Peso de um produto, utilizado no cálculo do frete
 */
class Peso
{
    private $liquido;
    private $bruto;

    public function __construct($peso = [])
    {
        $this->fromArray($peso);
    }

    /**
     * Peso liquido
     */
    public function getLiquido($normalize = false)
    {
        if (!$normalize) {
            return $this->liquido;
        }
        return Util::toFloat($this->liquido, 3);
    }

    public function setLiquido($liquido)
    {
        $this->liquido = $liquido;
        return $this;
    }

    /**
     * Peso bruto
     */
    public function getBruto($normalize = false)
    {
        if (!$normalize) {
            return $this->bruto;
        }
        return Util::toFloat($this->bruto, 3);
    }

    public function setBruto($bruto)
    {
        $this->bruto = $bruto;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $peso = [];
        $peso['liquido'] = $this->getLiquido();
        $peso['bruto'] = $this->getBruto();
        return $peso;
    }

    public function fromArray($peso = [])
    {
        if ($peso instanceof Peso) {
            $peso = $peso->toArray();
        } elseif (!is_array($peso)) {
            return $this;
        }
        if (isset($peso['liquido'])) {
            $this->setLiquido($peso['liquido']);
        } else {
            $this->setLiquido(null);
        }
        if (isset($peso['bruto'])) {
            $this->setBruto($peso['bruto']);
        } else {
            $this->setBruto(null);
        }
        return $this;
    }
}
