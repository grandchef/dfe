<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Transporte;

use DFe\Common\Util;
use DFe\Entity\Imposto;
use DFe\Entity\Municipio;

/**
 * ICMS retido do Transportador
 */
class Tributo extends Imposto
{
    private $servico;
    private $cfop;
    private $municipio;

    public function __construct($tributo = [])
    {
        parent::__construct($tributo);
    }

    public function getServico($normalize = false)
    {
        if (!$normalize) {
            return $this->servico;
        }
        return Util::toCurrency($this->servico);
    }

    public function setServico($servico)
    {
        $this->servico = $servico;
        return $this;
    }

    public function getCFOP($normalize = false)
    {
        if (!$normalize) {
            return $this->cfop;
        }
        return $this->cfop;
    }

    public function setCFOP($cfop)
    {
        $this->cfop = $cfop;
        return $this;
    }

    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $tributo = parent::toArray($recursive);
        $tributo['servico'] = $this->getServico();
        $tributo['cfop'] = $this->getCFOP();
        if (!is_null($this->getMunicipio()) && $recursive) {
            $tributo['municipio'] = $this->getMunicipio()->toArray($recursive);
        } else {
            $tributo['municipio'] = $this->getMunicipio();
        }
        return $tributo;
    }

    public function fromArray($tributo = [])
    {
        if ($tributo instanceof Tributo) {
            $tributo = $tributo->toArray();
        } elseif (!is_array($tributo)) {
            return $this;
        }
        parent::fromArray($tributo);
        $this->setServico($tributo['servico'] ?? null);
        $this->setCFOP($tributo['cfop'] ?? null);
        $this->setMunicipio(new Municipio(isset($tributo['municipio']) ? $tributo['municipio'] : []));
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'retTransp');
        Util::appendNode($element, 'vServ', $this->getServico(true));
        Util::appendNode($element, 'vBCRet', $this->getBase(true));
        Util::appendNode($element, 'pICMSRet', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSRet', $this->getValor(true));
        Util::appendNode($element, 'CFOP', $this->getCFOP(true));
        if (is_null($this->getMunicipio())) {
            return $element;
        }
        $municipio = $this->getMunicipio();
        $municipio->checkCodigos();
        Util::appendNode($element, 'cMunFG', $municipio->getCodigo(true));
        return $element;
    }


    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'retTransp';
        $element = Util::findNode($element, $name);
        $this->setServico(
            Util::loadNode(
                $element,
                'vServ',
                'Tag "vServ" do campo "Servico" não encontrada no Tributo'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCRet',
                'Tag "vBCRet" do campo "Base" não encontrada no Tributo'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pICMSRet',
                'Tag "pICMSRet" do campo "Aliquota" não encontrada no Tributo'
            )
        );
        $this->setCFOP(
            Util::loadNode(
                $element,
                'CFOP',
                'Tag "CFOP" do campo "CFOP" não encontrada no Tributo'
            )
        );
        $municipio = null;
        $codigo = Util::loadNode($element, 'cMunFG');
        if (!is_null($codigo)) {
            $municipio = new Municipio();
            $municipio->setCodigo($codigo);
        }
        $this->setMunicipio($municipio);
        return $element;
    }
}
