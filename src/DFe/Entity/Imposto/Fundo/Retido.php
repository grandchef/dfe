<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\Fundo;

use DOMElement;
use DFe\Common\Util;

/**
 * Valor e Percentual do imposto para o Fundo de Combate à Pobreza retido
 * anteriormente por substituição tributária
 */
class Retido extends Substituido
{
    /**
     * Constroi uma instância de Retido vazia
     * @param  array $retido Array contendo dados do Retido
     */
    public function __construct($retido = [])
    {
        parent::__construct($retido);
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $retido = parent::toArray($recursive);
        return $retido;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $retido Array ou instância de Retido, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($retido = [])
    {
        if ($retido instanceof Retido) {
            $retido = $retido->toArray();
        } elseif (!is_array($retido)) {
            return $this;
        }
        parent::fromArray($retido);
        $this->setGrupo(self::GRUPO_FCPSTRET);
        return $this;
    }

    /**
     * Verifica se o elemento informado contém os dados dessa instância
     * @param DOMElement $element Nó que pode contér os dados dessa instância
     * @return boolean   True se contém os dados dessa instância ou false caso contrário
     */
    public function exists($element)
    {
        // se o primeiro campo obrigatório existir, significa que deve ter os outros campos
        return Util::nodeExists(
            $element,
            'vBCFCPSTRet'
        );
    }

    /**
     * Cria um nó XML do retido de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'FCPSTRet');
        Util::appendNode($element, 'vBCFCPSTRet', $this->getBase(true));
        Util::appendNode($element, 'pFCPSTRet', $this->getAliquota(true));
        Util::appendNode($element, 'vFCPSTRet', $this->getValor(true));
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'FCPSTRet';
        $element = Util::findNode($element, $name);
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCFCPSTRet',
                'Tag "vBCFCPSTRet" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pFCPSTRet',
                'Tag "pFCPST" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
