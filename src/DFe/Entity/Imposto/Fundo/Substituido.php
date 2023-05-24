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
 * por substituição tributária
 */
class Substituido extends Base
{
    /**
     * Constroi uma instância de Substituido vazia
     * @param  array $substituido Array contendo dados do Substituido
     */
    public function __construct($substituido = [])
    {
        parent::__construct($substituido);
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $substituido = parent::toArray($recursive);
        return $substituido;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $substituido Array ou instância de Substituido, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($substituido = [])
    {
        if ($substituido instanceof Substituido) {
            $substituido = $substituido->toArray();
        } elseif (!is_array($substituido)) {
            return $this;
        }
        parent::fromArray($substituido);
        $this->setGrupo(self::GRUPO_FCPST);
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
            'vBCFCPST'
        );
    }

    /**
     * Cria um nó XML do substituido de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'FCPST');
        Util::appendNode($element, 'vBCFCPST', $this->getBase(true));
        Util::appendNode($element, 'pFCPST', $this->getAliquota(true));
        Util::appendNode($element, 'vFCPST', $this->getValor(true));
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
        $name ??= 'FCPST';
        $element = Util::findNode($element, $name);
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCFCPST',
                'Tag "vBCFCPST" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pFCPST',
                'Tag "pFCPST" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
