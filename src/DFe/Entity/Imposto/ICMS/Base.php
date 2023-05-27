<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS;

use DFe\Entity\Imposto;
use DFe\Common\Util;
use DFe\Entity\Imposto\Fundo\Base as Fundo;
use DFe\Entity\Imposto\Fundo\Retido;
use DFe\Entity\Imposto\Fundo\Substituido;

/**
 * Classe base do ICMS
 */
abstract class Base extends Imposto
{
    /**
     * origem da mercadoria: 0 - Nacional
     * 1 - Estrangeira - Importação direta
     *
     * 2 - Estrangeira - Adquirida no mercado interno
     */
    public const ORIGEM_NACIONAL = 'nacional';
    public const ORIGEM_ESTRANGEIRA = 'estrangeira';
    public const ORIGEM_INTERNO = 'interno';

    /**
     * origem da mercadoria:
     * 0 - Nacional
     * 1 - Estrangeira - Importação direta
     * 2
     * - Estrangeira - Adquirida no mercado interno
     */
    private $origem;

    /**
     * Fundo de Combate à Probreza
     */
    private $fundo;

    /**
     * Constroi uma instância de Base vazia
     * @param  array $base Array contendo dados do Base
     */
    public function __construct($base = [])
    {
        parent::__construct($base);
        $this->setGrupo(self::GRUPO_ICMS);
    }

    /**
     * origem da mercadoria:
     * 0 - Nacional
     * 1 - Estrangeira - Importação direta
     * 2
     * - Estrangeira - Adquirida no mercado interno
     * @param boolean $normalize informa se a origem deve estar no formato do XML
     * @return mixed origem do Base
     */
    public function getOrigem($normalize = false)
    {
        if (!$normalize) {
            return $this->origem;
        }
        switch ($this->origem) {
            case self::ORIGEM_NACIONAL:
                return '0';
            case self::ORIGEM_ESTRANGEIRA:
                return '1';
            case self::ORIGEM_INTERNO:
                return '2';
        }
        return $this->origem;
    }

    /**
     * Altera o valor da Origem para o informado no parâmetro
     * @param mixed $origem novo valor para Origem
     * @return self A própria instância da classe
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;
        return $this;
    }

    /**
     * Fundo de Combate à Probreza
     * @return \DFe\Entity\Imposto\Fundo\Base|null Base do fundo de combate à pobreza
     */
    public function getFundo()
    {
        return $this->fundo;
    }

    /**
     * Altera o valor do Fundo para o informado no parâmetro
     * @param mixed $fundo novo valor para Fundo
     * @return self A própria instância da classe
     */
    public function setFundo($fundo)
    {
        $this->fundo = $fundo;
        return $this;
    }

    protected function exportFundo($element, $version)
    {
        if (is_null($this->getFundo()) || is_null($this->getFundo()->getAliquota())) {
            return $element;
        }
        $fundo = $this->getFundo()->getNode($version);
        return Util::mergeNodes($element, $fundo);
    }

    protected function importFundo($element, $version = '')
    {
        if (is_null($this->getFundo())) {
            return $this;
        }
        if (!$this->getFundo()->exists($element)) {
            $this->getFundo()->fromArray([]);
        } else {
            $this->getFundo()->loadNode($element, $element->nodeName, $version);
        }
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $base = parent::toArray($recursive);
        $base['origem'] = $this->getOrigem();
        if (!is_null($this->getFundo()) && $recursive) {
            $base['fundo'] = $this->getFundo()->toArray($recursive);
        } else {
            $base['fundo'] = $this->getFundo();
        }
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
        if (!isset($base['origem'])) {
            $this->setOrigem(self::ORIGEM_NACIONAL);
        } else {
            $this->setOrigem($base['origem']);
        }
        if (isset($base['fundo']) && $base['fundo'] instanceof Fundo) {
            $this->setFundo(clone $base['fundo']);
        } elseif (isset($base['fundo']['grupo'])) {
            switch ($base['fundo']['grupo']) {
                case Fundo::GRUPO_FCPST:
                    $this->setFundo(new Substituido($base['fundo']));
                    break;
                case Fundo::GRUPO_FCPSTRET:
                    $this->setFundo(new Retido($base['fundo']));
                    break;
                default: //Fundo::GRUPO_FCP
                    $this->setFundo(new Fundo($base['fundo']));
                    break;
            }
        } else {
            $this->setFundo(null);
        }
        return $this;
    }
}
