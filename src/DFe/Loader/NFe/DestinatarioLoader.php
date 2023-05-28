<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Destinatario;

/**
 * Cliente pessoa física ou jurídica que está comprando os produtos e irá
 * receber a nota fiscal
 */
class DestinatarioLoader implements Loader
{
    public function __construct(private Destinatario $destinatario)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'dest');
        if (!empty($this->destinatario->getCNPJ())) {
            Util::appendNode($element, 'CNPJ', $this->destinatario->getCNPJ(true));
        } else {
            Util::appendNode($element, 'CPF', $this->destinatario->getCPF(true));
        }
        if (!empty($this->destinatario->getNome())) {
            Util::appendNode($element, 'xNome', $this->destinatario->getNome(true));
        }
        if (!is_null($this->destinatario->getEndereco())) {
            $endereco = $this->destinatario->getEndereco()->getNode($version, 'enderDest');
            $endereco = $dom->importNode($endereco, true);
            if (!empty($this->destinatario->getTelefone())) {
                Util::appendNode($endereco, 'fone', $this->destinatario->getTelefone(true));
            }
            $element->appendChild($endereco);
        }
        Util::appendNode($element, 'indIEDest', $this->destinatario->getIndicador(true));
        if (!empty($this->destinatario->getCNPJ()) && !empty($this->destinatario->getIE())) {
            Util::appendNode($element, 'IE', $this->destinatario->getIE(true));
        }
        if (!empty($this->destinatario->getEmail())) {
            Util::appendNode($element, 'email', $this->destinatario->getEmail(true));
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = (new PessoaLoader($this->destinatario))->loadNode($element, $name ?? 'dest', $version);
        $cpf = Util::loadNode($element, 'CPF');
        if (is_null($cpf) && is_null($this->destinatario->getCNPJ())) {
            throw new \Exception('Tag "CPF" não encontrada no Destinatario', 404);
        }
        $this->destinatario->setCPF($cpf);
        $this->destinatario->setEmail(Util::loadNode($element, 'email'));
        $this->destinatario->setIndicador(
            Util::loadNode(
                $element,
                'indIEDest',
                'Tag "indIEDest" não encontrada no Destinatario'
            )
        );
        return $element;
    }
}
