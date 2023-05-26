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

use DFe\Common\Node;
use DFe\Loader\NFe\V4\EmitenteLoader;
use DFe\Loader\CFe\V008\EmitenteLoader as CFeEmitenteLoader;

/**
 * Empresa que irá emitir as notas fiscais
 */
class Emitente extends Pessoa implements Node
{
    /**
     * Código de Regime Tributário. Este campo será obrigatoriamente preenchido
     * com: 1 – Simples Nacional; 2 – Simples Nacional – excesso de sublimite
     * de receita bruta; 3 – Regime Normal.
     */
    public const REGIME_SIMPLES = 'simples';
    public const REGIME_EXCESSO = 'excesso';
    public const REGIME_NORMAL = 'normal';

    private $fantasia;
    private $regime;

    public function __construct($emitente = [])
    {
        parent::__construct($emitente);
    }

    /**
     * Nome fantasia do da empresa emitente
     */
    public function getFantasia($normalize = false)
    {
        if (!$normalize) {
            return $this->fantasia;
        }
        return $this->fantasia;
    }

    public function setFantasia($fantasia)
    {
        $this->fantasia = $fantasia;
        return $this;
    }

    /**
     * Código de Regime Tributário. Este campo será obrigatoriamente preenchido
     * com: 1 – Simples Nacional; 2 – Simples Nacional – excesso de sublimite
     * de receita bruta; 3 – Regime Normal.
     */
    public function getRegime($normalize = false)
    {
        if (!$normalize) {
            return $this->regime;
        }
        switch ($this->regime) {
            case self::REGIME_SIMPLES:
                return '1';
            case self::REGIME_EXCESSO:
                return '2';
            case self::REGIME_NORMAL:
                return '3';
        }
        return $this->regime;
    }

    public function setRegime($regime)
    {
        $this->regime = $regime;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $emitente = parent::toArray($recursive);
        $emitente['fantasia'] = $this->getFantasia();
        $emitente['regime'] = $this->getRegime();
        return $emitente;
    }

    public function fromArray($emitente = [])
    {
        if ($emitente instanceof Emitente) {
            $emitente = $emitente->toArray();
        } elseif (!is_array($emitente)) {
            return $this;
        }
        parent::fromArray($emitente);
        if (is_null($this->getEndereco())) {
            $this->setEndereco(new Endereco());
        }
        $this->setFantasia($emitente['fantasia'] ?? null);
        if (!isset($emitente['regime'])) {
            $this->setRegime(self::REGIME_SIMPLES);
        } else {
            $this->setRegime($emitente['regime']);
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeEmitenteLoader($this);
        } else {
            $loader = new EmitenteLoader($this);
        }
        return $loader->getNode($version, $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeEmitenteLoader($this);
        } else {
            $loader = new EmitenteLoader($this);
        }
        return $loader->loadNode($element, $name, $version);
    }
}
