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
use DFe\Loader\NFe\EnderecoLoader;
use DFe\Loader\CFe\EnderecoLoader as CFeEnderecoLoader;

/**
 * Informação de endereço que será informado nos clientes e no emitente
 */
class Endereco implements Node
{
    private $pais;
    private $cep;
    private $municipio;
    private $bairro;
    private $logradouro;
    private $numero;
    private $complemento;

    public function __construct($endereco = [])
    {
        $this->fromArray($endereco);
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function setPais($pais)
    {
        $this->pais = $pais;
        return $this;
    }

    public function getCEP($normalize = false)
    {
        if (!$normalize) {
            return $this->cep;
        }
        return $this->cep;
    }

    public function setCEP($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * Município do endereço
     *
     * @return Municipio
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function getBairro($normalize = false)
    {
        if (!$normalize) {
            return $this->bairro;
        }
        return $this->bairro;
    }

    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    public function getLogradouro($normalize = false)
    {
        if (!$normalize) {
            return $this->logradouro;
        }
        return $this->logradouro;
    }

    public function setLogradouro($logradouro)
    {
        $this->logradouro = $logradouro;
        return $this;
    }

    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    public function getComplemento($normalize = false)
    {
        if (!$normalize) {
            return $this->complemento;
        }
        return $this->complemento;
    }

    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * Obtém as informações básicas do endereço em uma linha de texto
     * @param  boolean $normalize informa se o valor deve ser normalizado para um XML
     * @return string             endereço com logradouro, número e bairro
     */
    public function getDescricao($normalize = false)
    {
        return $this->getLogradouro() . ', ' . $this->getNumero() . ' - ' . $this->getBairro();
    }

    /**
     * Desmembra a descrição e salva as informações do endereço em seu respectivo campo
     * @param  string $descricao linha de endereço com diversas informações
     * @return Endereco retorna a própria instância
     */
    public function parseDescricao($descricao)
    {
        $pattern = '/(.*), (.*) - (.*)/';
        if (!preg_match($pattern, $descricao, $matches)) {
            throw new \Exception('Não foi possível desmembrar a linha de endereço', 500);
        }
        $this->setLogradouro($matches[1]);
        $this->setNumero($matches[2]);
        $this->setBairro($matches[3]);
        return $this;
    }

    public function toArray($recursive = false)
    {
        $endereco = [];
        if (!is_null($this->getPais()) && $recursive) {
            $endereco['pais'] = $this->getPais()->toArray($recursive);
        } else {
            $endereco['pais'] = $this->getPais();
        }
        $endereco['cep'] = $this->getCEP();
        if (!is_null($this->getMunicipio()) && $recursive) {
            $endereco['municipio'] = $this->getMunicipio()->toArray($recursive);
        } else {
            $endereco['municipio'] = $this->getMunicipio();
        }
        $endereco['bairro'] = $this->getBairro();
        $endereco['logradouro'] = $this->getLogradouro();
        $endereco['numero'] = $this->getNumero();
        $endereco['complemento'] = $this->getComplemento();
        return $endereco;
    }

    public function fromArray($endereco = [])
    {
        if ($endereco instanceof Endereco) {
            $endereco = $endereco->toArray();
        } elseif (!is_array($endereco)) {
            return $this;
        }
        $this->setPais(new Pais(isset($endereco['pais']) ? $endereco['pais'] : ['codigo' => 1058, 'nome' => 'Brasil']));
        $this->setCEP($endereco['cep'] ?? null);
        $this->setMunicipio(new Municipio(isset($endereco['municipio']) ? $endereco['municipio'] : []));
        $this->setBairro($endereco['bairro'] ?? null);
        $this->setLogradouro($endereco['logradouro'] ?? null);
        $this->setNumero($endereco['numero'] ?? null);
        $this->setComplemento($endereco['complemento'] ?? null);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeEnderecoLoader($this);
        } else {
            $loader = new EnderecoLoader($this);
        }
        return $loader->getNode($version, $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeEnderecoLoader($this);
        } else {
            $loader = new EnderecoLoader($this);
        }
        return $loader->loadNode($element, $name, $version);
    }
}
