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

/**
 * Informações do Intermediador da Transação
 */
class Intermediador implements Node
{
    /**
     * CNPJ do Intermediador da Transação (agenciador, plataforma de delivery,
     * marketplace e similar) de serviços e de negócios.
     *
     * @var string
     */
    private $cnpj;

    /**
     * Identificador cadastrado no intermediador
     *
     * @var string
     */
    private $identificador;

    /**
     * Constroi uma instância de Intermediador vazia
     * @param array $intermediador Array contendo dados do Intermediador
     */
    public function __construct($intermediador = [])
    {
        $this->fromArray($intermediador);
    }

    /**
     * CNPJ do Intermediador da Transação (agenciador, plataforma de delivery,
     * marketplace e similar) de serviços e de negócios.
     * @param boolean $normalize informa se o cnpj deve estar no formato do XML
     * @return string cnpj of Intermediador
     */
    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }

    /**
     * Altera o valor do CNPJ para o informado no parâmetro
     *
     * @param string|null $cnpj Novo cnpj para Intermediador
     *
     * @return self A própria instância da classe
     */
    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Identificador cadastrado no intermediador
     * @param boolean $normalize informa se o identificador deve estar no formato do XML
     * @return string identificador of Intermediador
     */
    public function getIdentificador($normalize = false)
    {
        if (!$normalize) {
            return $this->identificador;
        }
        return $this->identificador;
    }

    /**
     * Altera o valor do Identificador para o informado no parâmetro
     *
     * @param string|null $identificador Novo identificador para Intermediador
     *
     * @return self A própria instância da classe
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $intermediador = [];
        $intermediador['cnpj'] = $this->getCNPJ();
        $intermediador['identificador'] = $this->getIdentificador();
        return $intermediador;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $intermediador Array ou instância de Intermediador, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($intermediador = [])
    {
        if ($intermediador instanceof Intermediador) {
            $intermediador = $intermediador->toArray();
        } elseif (!is_array($intermediador)) {
            return $this;
        }
        if (!isset($intermediador['cnpj'])) {
            $this->setCNPJ(null);
        } else {
            $this->setCNPJ($intermediador['cnpj']);
        }
        if (!isset($intermediador['identificador'])) {
            $this->setIdentificador(null);
        } else {
            $this->setIdentificador($intermediador['identificador']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do intermediador de acordo com o leiaute da NFe
     * @param string $name Nome do nó que será criado
     * @return DOMElement Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'infIntermed');
        Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($element, 'idCadIntTran', $this->getIdentificador(true));
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param DOMElement $element Nó do xml com todos as tags dos campos
     * @param string $name Nome do nó que será carregado
     * @return DOMElement Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'infIntermed';
        $element = Util::findNode($element, $name);
        $this->setCNPJ(
            Util::loadNode(
                $element,
                'CNPJ',
                'Tag "CNPJ" não encontrada no Intermediador'
            )
        );
        $this->setIdentificador(
            Util::loadNode(
                $element,
                'idCadIntTran',
                'Tag "idCadIntTran" não encontrada no Intermediador'
            )
        );
        return $element;
    }
}
