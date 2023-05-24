<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\V4;;

use DOMDocument;
use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Pessoa;
use DFe\Entity\Emitente;
use DFe\Entity\Endereco;

/**
 * Classe base para preenchimento de informações de pessoas físicas e
 * empresas
 */
class PessoaLoader implements Loader
{
    public function __construct(private Pessoa $pessoa)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        return (new DOMDocument())->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'emit';
        $element = Util::findNode($element, $name);
        $razao_social = Util::loadNode($element, 'xNome');
        if (is_null($razao_social) && $this->pessoa instanceof Emitente) {
            throw new \Exception('Tag "xNome" do campo "RazaoSocial" não encontrada', 404);
        }
        $this->pessoa->setRazaoSocial($razao_social);
        $cnpj = Util::loadNode($element, 'CNPJ');
        if (is_null($cnpj) && $this->pessoa instanceof Emitente) {
            throw new \Exception('Tag "CNPJ" do campo "CNPJ" não encontrada', 404);
        }
        $this->pessoa->setCNPJ($cnpj);
        $ie = Util::loadNode($element, 'IE');
        if (is_null($ie) && $this->pessoa instanceof Emitente) {
            throw new \Exception('Tag "IE" do campo "IE" não encontrada', 404);
        }
        $this->pessoa->setIE($ie);
        $this->pessoa->setIM(Util::loadNode($element, 'IM'));
        if ($this->pessoa instanceof Emitente) {
            $tag_ender = 'enderEmit';
        } else {
            $tag_ender = 'enderDest';
        }
        $endereco = null;
        $_fields = $element->getElementsByTagName($tag_ender);
        if ($_fields->length > 0) {
            $endereco = new Endereco();
            $endereco->loadNode($_fields->item(0), $tag_ender, $version);
        } elseif ($this->pessoa instanceof Emitente) {
            throw new \Exception('Tag "' . $tag_ender . '" do objeto "Endereco" não encontrada', 404);
        }
        $this->pessoa->setEndereco($endereco);
        $telefone = null;
        if ($_fields->length > 0) {
            $telefone = Util::loadNode($_fields->item(0), 'fone');
        }
        $this->pessoa->setTelefone($telefone);
        return $element;
    }
}
