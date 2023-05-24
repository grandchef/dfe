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

/**
 * Classe base para preenchimento de informações de pessoas físicas e
 * empresas
 */
abstract class Pessoa implements Node
{
    private $razao_social;
    private $cnpj;
    private $ie;
    private $im;
    private $endereco;
    private $telefone;

    public function __construct($pessoa = [])
    {
        $this->fromArray($pessoa);
    }

    /**
     * Número identificador da pessoa
     */
    public function getID($normalize = false)
    {
        return $this->getCNPJ($normalize);
    }

    /**
     * Razão Social ou Nome
     */
    public function getRazaoSocial($normalize = false)
    {
        if (!$normalize) {
            return $this->razao_social;
        }
        return $this->razao_social;
    }

    public function setRazaoSocial($razao_social)
    {
        $this->razao_social = $razao_social;
        return $this;
    }

    /**
     * Identificador da pessoa na receita
     */
    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }

    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Inscrição Estadual
     */
    public function getIE($normalize = false)
    {
        if (!$normalize) {
            return $this->ie;
        }
        return $this->ie;
    }

    public function setIE($ie)
    {
        $this->ie = $ie;
        return $this;
    }

    /**
     * Inscrição Municipal
     */
    public function getIM($normalize = false)
    {
        if (!$normalize) {
            return $this->im;
        }
        return $this->im;
    }

    public function setIM($im)
    {
        $this->im = $im;
        return $this;
    }

    /**
     * Dados do endereço
     * 
     * @return Endereco
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }

    public function getTelefone($normalize = false)
    {
        if (!$normalize) {
            return $this->telefone;
        }
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $pessoa = [];
        $pessoa['razao_social'] = $this->getRazaoSocial();
        $pessoa['cnpj'] = $this->getCNPJ();
        $pessoa['ie'] = $this->getIE();
        $pessoa['im'] = $this->getIM();
        if (!is_null($this->getEndereco()) && $recursive) {
            $pessoa['endereco'] = $this->getEndereco()->toArray($recursive);
        } else {
            $pessoa['endereco'] = $this->getEndereco();
        }
        $pessoa['telefone'] = $this->getTelefone();
        return $pessoa;
    }

    public function fromArray($pessoa = [])
    {
        if ($pessoa instanceof Pessoa) {
            $pessoa = $pessoa->toArray();
        } elseif (!is_array($pessoa)) {
            return $this;
        }
        $this->setRazaoSocial($pessoa['razao_social'] ?? null);
        $this->setCNPJ($pessoa['cnpj'] ?? null);
        $this->setIE($pessoa['ie'] ?? null);
        $this->setIM($pessoa['im'] ?? null);
        if (!array_key_exists('endereco', $pessoa)) {
            $this->setEndereco(new Endereco());
        } elseif (isset($pessoa['endereco'])) {
            $this->setEndereco(new Endereco($pessoa['endereco']));
        } else {
            $this->setEndereco($pessoa['endereco']);
        }
        $this->setTelefone($pessoa['telefone'] ?? null);
        return $this;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'emit';
        $element = Util::findNode($element, $name);
        $razao_social = Util::loadNode($element, 'xNome');
        if (is_null($razao_social) && $this instanceof Emitente) {
            throw new \Exception('Tag "xNome" do campo "RazaoSocial" não encontrada', 404);
        }
        $this->setRazaoSocial($razao_social);
        $cnpj = Util::loadNode($element, 'CNPJ');
        if (is_null($cnpj) && $this instanceof Emitente) {
            throw new \Exception('Tag "CNPJ" do campo "CNPJ" não encontrada', 404);
        }
        $this->setCNPJ($cnpj);
        $ie = Util::loadNode($element, 'IE');
        if (is_null($ie) && $this instanceof Emitente) {
            throw new \Exception('Tag "IE" do campo "IE" não encontrada', 404);
        }
        $this->setIE($ie);
        $this->setIM(Util::loadNode($element, 'IM'));
        if ($this instanceof Emitente) {
            $tag_ender = 'enderEmit';
        } else {
            $tag_ender = 'enderDest';
        }
        $endereco = null;
        $_fields = $element->getElementsByTagName($tag_ender);
        if ($_fields->length > 0) {
            $endereco = new Endereco();
            $endereco->loadNode($_fields->item(0), $tag_ender);
        } elseif ($this instanceof Emitente) {
            throw new \Exception('Tag "' . $tag_ender . '" do objeto "Endereco" não encontrada', 404);
        }
        $this->setEndereco($endereco);
        $telefone = null;
        if ($_fields->length > 0) {
            $telefone = Util::loadNode($_fields->item(0), 'fone');
        }
        $this->setTelefone($telefone);
        return $element;
    }
}
