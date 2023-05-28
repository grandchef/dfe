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

use DOMElement;
use DFe\Common\Util;
use DFe\Common\Node;
use DFe\Loader\NFe\TotalLoader;
use DFe\Loader\CFe\TotalLoader as CFeTotalLoader;

/**
 * Dados dos totais da NF-e e do produto
 */
class Total implements Node
{
    /**
     * Valor Total dos produtos e serviços
     */
    private $produtos;

    /**
     * Valor do Desconto
     */
    private $desconto;

    /**
     * informar o valor do Seguro, o Seguro deve ser rateado entre os itens de
     * produto
     */
    private $seguro;

    /**
     * informar o valor do Frete, o Frete deve ser rateado entre os itens de
     * produto.
     */
    private $frete;

    /**
     * informar o valor de outras despesas acessórias do item de produto ou
     * serviço
     */
    private $despesas;

    /**
     * Valor estimado total de impostos federais, estaduais e municipais
     */
    private $tributos;

    /**
     * Informações complementares de interesse do Contribuinte
     */
    private $complemento;

    /**
     * Constroi uma instância de Total vazia
     * @param  array $total Array contendo dados do Total
     */
    public function __construct($total = [])
    {
        $this->fromArray($total);
    }

    /**
     * Valor Total dos produtos e serviços
     * @param boolean $normalize informa se o produtos deve estar no formato do XML
     * @return mixed produtos do Total
     */
    public function getProdutos($normalize = false)
    {
        if (!$normalize) {
            return $this->produtos;
        }
        return Util::toCurrency($this->produtos);
    }

    /**
     * Altera o valor do Produtos para o informado no parâmetro
     * @param mixed $produtos novo valor para Produtos
     * @return self A própria instância da classe
     */
    public function setProdutos($produtos)
    {
        if (!empty($produtos)) {
            $produtos = floatval($produtos);
        }
        $this->produtos = $produtos;
        return $this;
    }

    /**
     * Valor do Desconto
     * @param boolean $normalize informa se o desconto deve estar no formato do XML
     * @return mixed desconto do Total
     */
    public function getDesconto($normalize = false)
    {
        if (!$normalize) {
            return $this->desconto;
        }
        return Util::toCurrency($this->desconto);
    }

    /**
     * Altera o valor do Desconto para o informado no parâmetro
     * @param mixed $desconto novo valor para Desconto
     * @return self A própria instância da classe
     */
    public function setDesconto($desconto)
    {
        if (!empty($desconto)) {
            $desconto = floatval($desconto);
        }
        $this->desconto = $desconto;
        return $this;
    }

    /**
     * informar o valor do Seguro, o Seguro deve ser rateado entre os itens de
     * produto
     * @param boolean $normalize informa se o seguro deve estar no formato do XML
     * @return mixed seguro do Total
     */
    public function getSeguro($normalize = false)
    {
        if (!$normalize) {
            return $this->seguro;
        }
        return Util::toCurrency($this->seguro);
    }

    /**
     * Altera o valor do Seguro para o informado no parâmetro
     * @param mixed $seguro novo valor para Seguro
     * @return self A própria instância da classe
     */
    public function setSeguro($seguro)
    {
        if (!empty($seguro)) {
            $seguro = floatval($seguro);
        }
        $this->seguro = $seguro;
        return $this;
    }

    /**
     * informar o valor do Frete, o Frete deve ser rateado entre os itens de
     * produto.
     * @param boolean $normalize informa se o frete deve estar no formato do XML
     * @return mixed frete do Total
     */
    public function getFrete($normalize = false)
    {
        if (!$normalize) {
            return $this->frete;
        }
        return Util::toCurrency($this->frete);
    }

    /**
     * Altera o valor do Frete para o informado no parâmetro
     * @param mixed $frete novo valor para Frete
     * @return self A própria instância da classe
     */
    public function setFrete($frete)
    {
        if (!empty($frete)) {
            $frete = floatval($frete);
        }
        $this->frete = $frete;
        return $this;
    }

    /**
     * informar o valor de outras despesas acessórias do item de produto ou
     * serviço
     * @param boolean $normalize informa se a despesas deve estar no formato do XML
     * @return mixed despesas do Total
     */
    public function getDespesas($normalize = false)
    {
        if (!$normalize) {
            return $this->despesas;
        }
        return Util::toCurrency($this->despesas);
    }

    /**
     * Altera o valor da Despesas para o informado no parâmetro
     * @param mixed $despesas novo valor para Despesas
     * @return self A própria instância da classe
     */
    public function setDespesas($despesas)
    {
        if (!empty($despesas)) {
            $despesas = floatval($despesas);
        }
        $this->despesas = $despesas;
        return $this;
    }

    /**
     * Valor estimado total de impostos federais, estaduais e municipais
     * @param boolean $normalize informa se o tributos deve estar no formato do XML
     * @return mixed tributos do Total
     */
    public function getTributos($normalize = false)
    {
        if (!$normalize) {
            return $this->tributos;
        }
        return Util::toCurrency($this->tributos);
    }

    /**
     * Altera o valor do Tributos para o informado no parâmetro
     * @param mixed $tributos novo valor para Tributos
     * @return self A própria instância da classe
     */
    public function setTributos($tributos)
    {
        if (!empty($tributos)) {
            $tributos = floatval($tributos);
        }
        $this->tributos = $tributos;
        return $this;
    }

    /**
     * Informações complementares de interesse do Contribuinte
     * @param boolean $normalize informa se o complemento deve estar no formato do XML
     * @return mixed complemento do Total
     */
    public function getComplemento($normalize = false)
    {
        if (!$normalize) {
            return $this->complemento;
        }
        return $this->complemento;
    }

    /**
     * Altera o valor do Complemento para o informado no parâmetro
     * @param mixed $complemento novo valor para Complemento
     * @return self A própria instância da classe
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $total = [];
        $total['produtos'] = $this->getProdutos();
        $total['desconto'] = $this->getDesconto();
        $total['seguro'] = $this->getSeguro();
        $total['frete'] = $this->getFrete();
        $total['despesas'] = $this->getDespesas();
        $total['tributos'] = $this->getTributos();
        $total['complemento'] = $this->getComplemento();
        return $total;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $total Array ou instância de Total, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($total = [])
    {
        if ($total instanceof Total) {
            $total = $total->toArray();
        } elseif (!is_array($total)) {
            return $this;
        }
        if (!isset($total['produtos'])) {
            $this->setProdutos(null);
        } else {
            $this->setProdutos($total['produtos']);
        }
        if (!array_key_exists('desconto', $total)) {
            $this->setDesconto(null);
        } else {
            $this->setDesconto($total['desconto']);
        }
        if (!array_key_exists('seguro', $total)) {
            $this->setSeguro(null);
        } else {
            $this->setSeguro($total['seguro']);
        }
        if (!array_key_exists('frete', $total)) {
            $this->setFrete(null);
        } else {
            $this->setFrete($total['frete']);
        }
        if (!array_key_exists('despesas', $total)) {
            $this->setDespesas(null);
        } else {
            $this->setDespesas($total['despesas']);
        }
        if (!array_key_exists('tributos', $total)) {
            $this->setTributos(null);
        } else {
            $this->setTributos($total['tributos']);
        }
        if (!array_key_exists('complemento', $total)) {
            $this->setComplemento(null);
        } else {
            $this->setComplemento($total['complemento']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do total de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeTotalLoader($this);
        } else {
            $loader = new TotalLoader($this);
        }
        return $loader->getNode($version, $name);
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {

        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeTotalLoader($this);
        } else {
            $loader = new TotalLoader($this);
        }
        return $loader->loadNode($element, $name, $version);
    }
}
