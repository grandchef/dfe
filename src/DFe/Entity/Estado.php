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

use DFe\Core\SEFAZ;

class Estado
{
    private $codigo;
    private $nome;
    private $uf;

    public function __construct($estado = [])
    {
        $this->fromArray($estado);
    }

    /**
     * Código do estado (utilizar a tabela do IBGE)
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
     * Nome do estado (Opcional)
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

    /**
     * Sigla do estado
     */
    public function getUF($normalize = false)
    {
        if (!$normalize) {
            return $this->uf;
        }
        return $this->uf;
    }

    public function setUF($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    public function checkCodigos()
    {
        if (is_numeric($this->getCodigo())) {
            return;
        }
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $this->setCodigo($db->getCodigoEstado($this->getUF()));
    }

    public function toArray($recursive = false)
    {
        $estado = [];
        $estado['codigo'] = $this->getCodigo();
        $estado['nome'] = $this->getNome();
        $estado['uf'] = $this->getUF();
        return $estado;
    }

    public function fromArray($estado = [])
    {
        if ($estado instanceof Estado) {
            $estado = $estado->toArray();
        } elseif (!is_array($estado)) {
            return $this;
        }
        $this->setCodigo($estado['codigo'] ?? null);
        $this->setNome($estado['nome'] ?? null);
        $this->setUF($estado['uf'] ?? null);
        return $this;
    }
}
