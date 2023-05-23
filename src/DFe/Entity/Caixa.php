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

class Caixa
{
    private $numero;

    public function __construct($attributes = [])
    {
        $this->fromArray($attributes);
    }

    /**
     * Número do caixa
     */
    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $attributes = [];
        $attributes['numero'] = $this->getNumero();
        return $attributes;
    }

    public function fromArray($attributes = [])
    {
        if ($attributes instanceof self) {
            $attributes = $attributes->toArray();
        } elseif (!is_array($attributes)) {
            return $this;
        }
        $this->setNumero($attributes['numero'] ?? null);
        return $this;
    }
}
