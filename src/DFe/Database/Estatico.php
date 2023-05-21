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

use DFe\Common\Util;
use DFe\Core\Nota;

class Estatico extends Banco
{
    private $ibpt;
    private $uf_codes;
    private $mun_codes;
    private $servicos;
    private $data_dir;

    public function __construct($estatico = [])
    {
        parent::__construct($estatico);
        $this->data_dir = __DIR__ . '/data';
        $this->load();
    }

    public function load()
    {
        $json = file_get_contents($this->data_dir . '/uf_ibge_code.json');
        $this->uf_codes = json_decode($json, true);
        if ($this->uf_codes === false || is_null($this->uf_codes)) {
            throw new \Exception('Falha ao carregar os códigos das unidades federadas', json_last_error());
        }
        $json = file_get_contents($this->data_dir . '/municipio_ibge_code.json');
        $this->mun_codes = json_decode($json, true);
        if ($this->mun_codes === false || is_null($this->mun_codes)) {
            throw new \Exception('Falha ao carregar os códigos dos municípios', json_last_error());
        }
        $json = file_get_contents($this->data_dir . '/servicos.json');
        $this->servicos = json_decode($json, true);
        if ($this->servicos === false || is_null($this->servicos)) {
            throw new \Exception('Falha ao carregar serviços da SEFAZ', json_last_error());
        }
    }

    public function getIBPT()
    {
        return $this->ibpt;
    }

    public function setIBPT($ibpt)
    {
        $this->ibpt = $ibpt;
        return $this;
    }

    /**
     * Obtém o código IBGE do estado
     */
    public function getCodigoEstado($uf)
    {
        if (!isset($this->uf_codes['estados'][strtoupper($uf ?? '')])) {
            throw new \Exception(
                sprintf('Não foi encontrado o código do IBGE para o estado "%s"', $uf),
                404
            );
        }
        $codigo = $this->uf_codes['estados'][strtoupper($uf)];
        return intval($codigo);
    }

    /**
     * Obtém o código do orgão por estado
     */
    public function getCodigoOrgao($uf)
    {
        if (!isset($this->uf_codes['orgaos'][strtoupper($uf)])) {
            throw new \Exception(
                sprintf('Não foi encontrado o código do orgão para o estado "%s"', $uf),
                404
            );
        }
        $codigo = $this->uf_codes['orgaos'][strtoupper($uf)];
        return intval($codigo);
    }

    /**
     * Obtém a aliquota do imposto de acordo com o tipo
     */
    public function getImpostoAliquota($ncm, $uf, $ex = null, $cnpj = null, $token = null)
    {
        return $this->getIBPT()->getImposto($cnpj, $token, $ncm, $uf, $ex);
    }

    /**
     * Obtém o código IBGE do município
     */
    public function getCodigoMunicipio($municipio, $uf)
    {
        if (!isset($this->mun_codes['municipios'][strtoupper($uf ?? '')])) {
            throw new \Exception(
                sprintf('Não exite municípios para o estado "%s"', $uf),
                404
            );
        }
        $array = $this->mun_codes['municipios'][strtoupper($uf)];
        $elem = ['nome' => $municipio];
        $o = Util::binarySearch($elem, $array, function ($o1, $o2) {
            $n1 = Util::removeAccent($o1['nome']);
            $n2 = Util::removeAccent($o2['nome']);
            return strcasecmp($n1, $n2);
        });
        if ($o === false) {
            throw new \Exception(
                sprintf('Não foi encontrado o código do IBGE para o município "%s" do estado "%s"', $municipio, $uf),
                404
            );
        }
        return $o['codigo'];
    }

    /**
     * Obtém as notas pendentes de envio, em contingência e corrigidas após
     * rejeitadas
     */
    public function getNotasAbertas($inicio = null, $quantidade = null)
    {
        return []; // TODO implementar
    }

    /**
     * Obtém as notas em processamento para consulta e possível protocolação
     */
    public function getNotasPendentes($inicio = null, $quantidade = null)
    {
        return []; // TODO implementar
    }

    /**
     * Obtém as tarefas de inutilização, cancelamento e consulta de notas
     * pendentes que entraram em contingência
     */
    public function getNotasTarefas($inicio = null, $quantidade = null)
    {
        return []; // TODO implementar
    }

    public function getInformacaoServico($emissao, $uf, $modelo = null, $ambiente = null)
    {
        switch ($emissao) {
            case '1':
                $emissao = Nota::EMISSAO_NORMAL;
                break;
            case '9':
                $emissao = Nota::EMISSAO_CONTINGENCIA;
                break;
        }
        switch ($modelo) {
            case '55':
                $modelo = Nota::MODELO_NFE;
                break;
            case '65':
                $modelo = Nota::MODELO_NFCE;
                break;
        }
        if ($modelo == Nota::MODELO_NFCE) {
            $emissao = Nota::EMISSAO_NORMAL; // NFCe envia contingência pelo webservice normal
        }
        if (!isset($this->servicos[$emissao])) {
            throw new \Exception(
                sprintf('Falha ao obter o serviço da SEFAZ para o tipo de emissão "%s"', $emissao),
                404
            );
        }
        $array = $this->servicos[$emissao];
        if (!isset($array[strtoupper($uf)])) {
            throw new \Exception(
                sprintf('Falha ao obter o serviço da SEFAZ para a UF "%s"', $uf),
                404
            );
        }
        $array = $array[strtoupper($uf)];
        if (!is_array($array)) {
            $array = $this->getInformacaoServico($emissao, $array);
        }
        $_modelos = [Nota::MODELO_NFE, Nota::MODELO_NFCE];
        foreach ($_modelos as $_modelo) {
            if (!isset($array[$_modelo])) {
                continue;
            }
            $node = $array[$_modelo];
            if (!is_array($node)) {
                $node = $this->getInformacaoServico($emissao, $node, $_modelo);
            }
            if (isset($node['base'])) {
                $base = $this->getInformacaoServico($emissao, $node['base'], $_modelo);
                $node = array_replace_recursive($node, $base);
            }
            $array[$_modelo] = $node;
        }
        if (!is_null($modelo)) {
            if (!isset($array[$modelo])) {
                throw new \Exception(
                    sprintf('Falha ao obter o serviço da SEFAZ para o modelo de nota "%s"', $modelo),
                    404
                );
            }
            $array = $array[$modelo];
        }
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        if (!is_null($modelo) && !is_null($ambiente)) {
            if (!isset($array[$ambiente])) {
                throw new \Exception(
                    sprintf('Falha ao obter o serviço da SEFAZ para o ambiente "%s"', $ambiente),
                    404
                );
            }
            $array = $array[$ambiente];
        }
        return $array;
    }

    public function toArray($recursive = false)
    {
        $estatico = parent::toArray($recursive);
        $estatico['ibpt'] = $this->getIBPT();
        return $estatico;
    }

    public function fromArray($estatico = [])
    {
        if ($estatico instanceof Estatico) {
            $estatico = $estatico->toArray();
        } elseif (!is_array($estatico)) {
            return $this;
        }
        parent::fromArray($estatico);
        $this->setIBPT(new IBPT());
        return $this;
    }
}
