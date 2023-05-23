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

/**
 * Município de um endereço
 */
class Municipio
{
    private $estado;
    private $codigo;
    private $nome;

    public function __construct($municipio = [])
    {
        $this->fromArray($municipio);
    }

    /**
     * Estado do município
     * 
     * @return Estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * Código do município (utilizar a tabela do IBGE), informar 9999999 para
     * operações com o exterior.
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
     * Nome do munícipio
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
     * Verifica se o código do municipio foi preenchido,
     * caso contrário realiza uma busca usando o nome e a UF e preenche
     * @return void
     */
    public function checkCodigos()
    {
        if (is_numeric($this->getCodigo())) {
            return;
        }
        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        $this->setCodigo($db->getCodigoMunicipio(
            $this->getNome(),
            $this->getEstado()->getUF()
        ));
    }

    public function toArray($recursive = false)
    {
        $municipio = [];
        if (!is_null($this->getEstado()) && $recursive) {
            $municipio['estado'] = $this->getEstado()->toArray($recursive);
        } else {
            $municipio['estado'] = $this->getEstado();
        }
        $municipio['codigo'] = $this->getCodigo();
        $municipio['nome'] = $this->getNome();
        return $municipio;
    }

    public function fromArray($municipio = [])
    {
        if ($municipio instanceof Municipio) {
            $municipio = $municipio->toArray();
        } elseif (!is_array($municipio)) {
            return $this;
        }
        $this->setEstado(new Estado(isset($municipio['estado']) ? $municipio['estado'] : []));
        $this->setCodigo($municipio['codigo'] ?? null);
        $this->setNome($municipio['nome'] ?? null);
        return $this;
    }
}
