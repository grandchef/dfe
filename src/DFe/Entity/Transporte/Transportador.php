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
use DFe\Entity\Destinatario;

/**
 * Dados da transportadora
 */
class Transportador extends Destinatario
{
    public function __construct($transportador = [])
    {
        parent::__construct($transportador);
    }

    public function toArray($recursive = false)
    {
        $transportador = parent::toArray($recursive);
        return $transportador;
    }

    public function fromArray($transportador = [])
    {
        if ($transportador instanceof Transportador) {
            $transportador = $transportador->toArray();
        } elseif (!is_array($transportador)) {
            return $this;
        }
        parent::fromArray($transportador);
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'transporta');
        if (!empty($this->getCNPJ())) {
            Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        } else {
            Util::appendNode($element, 'CPF', $this->getCPF(true));
        }
        if (!empty($this->getCNPJ())) {
            Util::appendNode($element, 'xNome', $this->getRazaoSocial(true));
        } else {
            Util::appendNode($element, 'xNome', $this->getNome(true));
        }
        if (!empty($this->getCNPJ())) {
            Util::appendNode($element, 'IE', $this->getIE(true));
        }
        if (!is_null($this->getEndereco())) {
            $endereco = $this->getEndereco();
            Util::appendNode($element, 'xEnder', $endereco->getDescricao(true));
            Util::appendNode($element, 'xMun', $endereco->getMunicipio()->getNome(true));
            Util::appendNode($element, 'UF', $endereco->getMunicipio()->getEstado()->getUF(true));
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'transporta';
        $element = Util::findNode($element, $name);
        $cnpj = Util::loadNode($element, 'CNPJ');
        $cpf = Util::loadNode($element, 'CPF');
        if (is_null($cnpj) && is_null($cpf)) {
            throw new \Exception('Tag "CNPJ" ou "CPF" não encontrada no Transportador', 404);
        }
        $this->setCNPJ($cnpj);
        $this->setCPF($cpf);
        if (!empty($this->getCNPJ())) {
            $this->setRazaoSocial(
                Util::loadNode(
                    $element,
                    'xNome',
                    'Tag "xNome" do campo "RazaoSocial" não encontrada'
                )
            );
        } else {
            $this->setNome(
                Util::loadNode(
                    $element,
                    'xNome',
                    'Tag "xNome" do campo "Nome" não encontrada'
                )
            );
        }
        $this->setIE(
            Util::loadNode(
                $element,
                'IE',
                'Tag "IE" do campo "IE" não encontrada'
            )
        );
        $this->setIM(null);
        $descricao = Util::loadNode($element, 'xEnder');
        if (is_null($descricao)) {
            $this->setEndereco(null);
            return $element;
        }
        $endereco = new \DFe\Entity\Endereco();
        $endereco->parseDescricao($descricao);
        $endereco->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do nome do município não encontrada'
            )
        );
        $endereco->getMunicipio()->getEstado()->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" da UF do estado não encontrada'
            )
        );
        $this->setEndereco($endereco);
        return $element;
    }
}
