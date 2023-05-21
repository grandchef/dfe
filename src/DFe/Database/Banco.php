<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Database;

abstract class Banco
{
    public function __construct($banco = [])
    {
        $this->fromArray($banco);
    }

    /**
     * Obtém o código IBGE do estado
     */
    abstract public function getCodigoEstado($uf);

    /**
     * Obtém o código do orgão por estado
     */
    abstract public function getCodigoOrgao($uf);

    /**
     * Obtém a aliquota do imposto de acordo com o tipo
     */
    abstract public function getImpostoAliquota($ncm, $uf, $ex = null, $cnpj = null, $token = null);

    /**
     * Obtém o código IBGE do município
     */
    abstract public function getCodigoMunicipio($municipio, $uf);

    /**
     * Obtém as notas pendentes de envio, em contingência e corrigidas após
     * rejeitadas
     */
    abstract public function getNotasAbertas($inicio = null, $quantidade = null);

    /**
     * Obtém as notas em processamento para consulta e possível protocolação
     */
    abstract public function getNotasPendentes($inicio = null, $quantidade = null);

    /**
     * Obtém as tarefas de inutilização, cancelamento e consulta de notas
     * pendentes que entraram em contingência
     */
    abstract public function getNotasTarefas($inicio = null, $quantidade = null);

    /**
     * Obtém informações dos servidores da SEFAZ como URLs e versões
     */
    abstract public function getInformacaoServico($emissao, $uf, $modelo = null, $ambiente = null);

    public function toArray($recursive = false)
    {
        $banco = [];
        return $banco;
    }

    public function fromArray($banco = [])
    {
        if ($banco instanceof Banco) {
            $banco = $banco->toArray();
        } elseif (!is_array($banco)) {
            return $this;
        }
        return $this;
    }
}
