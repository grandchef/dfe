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
use DFe\Entity\Imposto;

/**
 * Valor e Percentual do imposto para o Fundo de Combate à Pobreza
 */
class Base extends Imposto
{
    /**
     * Grupo do imposto
     */
    public const GRUPO_FCP = 'fcp';
    public const GRUPO_FCPST = 'fcpst';
    public const GRUPO_FCPSTRET = 'fcpstret';


    /**
     * Constroi uma instância de Base vazia
     * @param  array $base Array contendo dados do Base
     */
    public function __construct($base = [])
    {
        parent::__construct($base);
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $base = parent::toArray($recursive);
        return $base;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $base Array ou instância de Base, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($base = [])
    {
        if ($base instanceof Base) {
            $base = $base->toArray();
        } elseif (!is_array($base)) {
            return $this;
        }
        parent::fromArray($base);
        $this->setGrupo(self::GRUPO_FCP);
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
            'pFCP'
        );
    }

    /**
     * Cria um nó XML do base de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'FCP');
        Util::appendNode($element, 'vBCFCP', $this->getBase(true));
        Util::appendNode($element, 'pFCP', $this->getAliquota(true));
        Util::appendNode($element, 'vFCP', $this->getValor(true));
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
        $name ??= 'FCP';
        $element = Util::findNode($element, $name);
        if (is_null($this->getBase())) {
            $this->setBase(
                Util::loadNode(
                    $element,
                    'vBCFCP',
                    'Tag "vBCFCP" do campo "Base" não encontrada'
                )
            );
        }
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pFCP',
                'Tag "pFCP" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
