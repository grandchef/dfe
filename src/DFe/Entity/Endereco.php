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

    public function checkCodigos()
    {
        $this->getMunicipio()->checkCodigos();
        $this->getMunicipio()->getEstado()->checkCodigos();
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $this->checkCodigos();
        $element = $dom->createElement($name ?? 'enderEmit');
        Util::appendNode($element, 'xLgr', $this->getLogradouro(true));
        Util::appendNode($element, 'nro', $this->getNumero(true));
        if (!empty($this->getComplemento())) {
            Util::appendNode($element, 'xCpl', $this->getComplemento(true));
        }
        Util::appendNode($element, 'xBairro', $this->getBairro(true));
        Util::appendNode($element, 'cMun', $this->getMunicipio()->getCodigo(true));
        Util::appendNode($element, 'xMun', $this->getMunicipio()->getNome(true));
        Util::appendNode($element, 'UF', $this->getMunicipio()->getEstado()->getUF(true));
        Util::appendNode($element, 'CEP', $this->getCEP(true));
        Util::appendNode($element, 'cPais', $this->getPais()->getCodigo(true));
        Util::appendNode($element, 'xPais', $this->getPais()->getNome(true));
        // Util::appendNode($element, 'fone', $this->getTelefone(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'enderEmit';
        $element = Util::findNode($element, $name);
        $this->setLogradouro(
            Util::loadNode(
                $element,
                'xLgr',
                'Tag "xLgr" do campo "Logradouro" não encontrada'
            )
        );
        $this->setNumero(
            Util::loadNode(
                $element,
                'nro',
                'Tag "nro" do campo "Numero" não encontrada'
            )
        );
        $this->setComplemento(Util::loadNode($element, 'xCpl'));
        $this->setBairro(
            Util::loadNode(
                $element,
                'xBairro',
                'Tag "xBairro" do campo "Bairro" não encontrada'
            )
        );
        $this->getMunicipio()->setCodigo(
            Util::loadNode(
                $element,
                'cMun',
                'Tag "cMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->getMunicipio()->getEstado()->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" do objeto "Estado" não encontrada'
            )
        );
        $this->setCEP(
            Util::loadNode(
                $element,
                'CEP',
                'Tag "CEP" do campo "CEP" não encontrada'
            )
        );
        $this->getPais()->setCodigo(
            Util::loadNode(
                $element,
                'cPais',
                'Tag "cPais" do objeto "Pais" não encontrada'
            )
        );
        $this->getPais()->setNome(
            Util::loadNode(
                $element,
                'xPais',
                'Tag "xPais" do objeto "Pais" não encontrada'
            )
        );
        return $element;
    }
}
