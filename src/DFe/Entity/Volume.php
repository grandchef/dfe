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
use DFe\Common\Util;

class Volume implements Node
{
    private $quantidade;
    private $especie;
    private $marca;
    private $numeracoes;
    private $peso;
    private $lacres;

    public function __construct($volume = [])
    {
        $this->fromArray($volume);
    }

    public function getQuantidade($normalize = false)
    {
        if (!$normalize) {
            return $this->quantidade;
        }
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        if (!is_null($quantidade)) {
            $quantidade = intval($quantidade);
        }
        $this->quantidade = $quantidade;
        return $this;
    }

    public function getEspecie($normalize = false)
    {
        if (!$normalize) {
            return $this->especie;
        }
        return $this->especie;
    }

    public function setEspecie($especie)
    {
        $this->especie = $especie;
        return $this;
    }

    public function getMarca($normalize = false)
    {
        if (!$normalize) {
            return $this->marca;
        }
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
        return $this;
    }

    public function getNumeracoes()
    {
        return $this->numeracoes;
    }

    public function setNumeracoes($numeracoes)
    {
        $this->numeracoes = $numeracoes;
        return $this;
    }

    public function addNumeracao($numeracao)
    {
        $this->numeracoes[] = $numeracao;
        return $this;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
        return $this;
    }

    public function getLacres()
    {
        return $this->lacres;
    }

    public function setLacres($lacres)
    {
        $this->lacres = $lacres;
        return $this;
    }

    public function addLacre($lacre)
    {
        $this->lacres[] = $lacre;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $volume = [];
        $volume['quantidade'] = $this->getQuantidade();
        $volume['especie'] = $this->getEspecie();
        $volume['marca'] = $this->getMarca();
        $volume['numeracoes'] = $this->getNumeracoes();
        if (!is_null($this->getPeso()) && $recursive) {
            $volume['peso'] = $this->getPeso()->toArray($recursive);
        } else {
            $volume['peso'] = $this->getPeso();
        }
        if ($recursive) {
            $lacres = [];
            $_lacres = $this->getLacres();
            foreach ($_lacres as $_lacre) {
                $lacres[] = $_lacre->toArray($recursive);
            }
            $volume['lacres'] = $lacres;
        } else {
            $volume['lacres'] = $this->getLacres();
        }
        return $volume;
    }

    public function fromArray($volume = [])
    {
        if ($volume instanceof Volume) {
            $volume = $volume->toArray();
        } elseif (!is_array($volume)) {
            return $this;
        }
        $this->setQuantidade($volume['quantidade'] ?? null);
        $this->setEspecie($volume['especie'] ?? null);
        $this->setMarca($volume['marca'] ?? null);
        if (!isset($volume['numeracoes'])) {
            $this->setNumeracoes([]);
        } else {
            $this->setNumeracoes($volume['numeracoes']);
        }
        $this->setPeso(new Peso(isset($volume['peso']) ? $volume['peso'] : []));
        if (!isset($volume['lacres'])) {
            $this->setLacres([]);
        } else {
            $this->setLacres($volume['lacres']);
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'vol');
        if (!is_null($this->getQuantidade())) {
            Util::appendNode($element, 'qVol', $this->getQuantidade(true));
        }
        if (!is_null($this->getEspecie())) {
            Util::appendNode($element, 'esp', $this->getEspecie(true));
        }
        if (!is_null($this->getMarca())) {
            Util::appendNode($element, 'marca', $this->getMarca(true));
        }
        $_numeracoes = $this->getNumeracoes();
        if (!empty($_numeracoes)) {
            Util::appendNode($element, 'nVol', implode(', ', $_numeracoes));
        }
        if (!is_null($this->getPeso())) {
            $peso = $this->getPeso();
            Util::appendNode($element, 'pesoL', $peso->getLiquido(true));
            Util::appendNode($element, 'pesoB', $peso->getBruto(true));
        }
        $_lacres = $this->getLacres();
        if (!empty($_lacres)) {
            foreach ($_lacres as $_lacre) {
                $lacre = $_lacre->getNode();
                $lacre = $dom->importNode($lacre, true);
                $element->appendChild($lacre);
            }
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'vol';
        $element = Util::findNode($element, $name);
        $this->setQuantidade(Util::loadNode($element, 'qVol'));
        $this->setEspecie(Util::loadNode($element, 'esp'));
        $this->setMarca(Util::loadNode($element, 'marca'));
        $numeracoes = [];
        $volumes = Util::loadNode($element, 'nVol');
        if (!empty($volumes)) {
            $numeracoes = explode(', ', $volumes);
        }
        $this->setNumeracoes($numeracoes);
        $peso = new Peso();
        $peso->setLiquido(Util::loadNode($element, 'pesoL'));
        $peso->setBruto(Util::loadNode($element, 'pesoB'));
        if (is_null($peso->getLiquido()) || is_null($peso->getBruto())) {
            $peso = null;
        }
        $this->setPeso($peso);
        $lacres = [];
        $_fields = $element->getElementsByTagName('lacres');
        foreach ($_fields as $_item) {
            $lacre = new Lacre();
            $lacre->loadNode($_item, 'lacres');
            $lacres[] = $lacre;
        }
        $this->setLacres($lacres);
        return $element;
    }
}
