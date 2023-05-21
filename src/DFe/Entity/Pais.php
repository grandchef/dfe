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

class Pais
{
    private $codigo;
    private $nome;

    public function __construct($pais = [])
    {
        $this->fromArray($pais);
    }

    /**
     * Código do país
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Nome do país
     */
    public function getNome($normalize = false)
    {
        if (!$normalize) {
            return $this->nome;
        }
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $pais = [];
        $pais['codigo'] = $this->getCodigo();
        $pais['nome'] = $this->getNome();
        return $pais;
    }

    public function fromArray($pais = [])
    {
        if ($pais instanceof Pais) {
            $pais = $pais->toArray();
        } elseif (!is_array($pais)) {
            return $this;
        }
        if (isset($pais['codigo'])) {
            $this->setCodigo($pais['codigo']);
        } else {
            $this->setCodigo(null);
        }
        if (isset($pais['nome'])) {
            $this->setNome($pais['nome']);
        } else {
            $this->setNome(null);
        }
        return $this;
    }
}
