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
use DFe\Loader\NFe\DestinatarioLoader;
use DFe\Loader\CFe\DestinatarioLoader as CFeDestinatarioLoader;

/**
 * Cliente pessoa física ou jurídica que está comprando os produtos e irá
 * receber a nota fiscal
 */
class Destinatario extends Pessoa implements Node
{
    /**
     * Indicador da IE do destinatário:
     * 1 – Contribuinte ICMSpagamento à
     * vista;
     * 2 – Contribuinte isento de inscrição;
     * 9 – Não Contribuinte
     */
    public const INDICADOR_PAGAMENTO = 'pagamento';
    public const INDICADOR_ISENTO = 'isento';
    public const INDICADOR_NENHUM = 'nenhum';

    private $cpf;
    private $email;
    private $indicador;

    public function __construct($destinatario = [])
    {
        parent::__construct($destinatario);
    }

    /**
     * Número identificador do destinatario
     */
    public function getID($normalize = false)
    {
        if (!is_null($this->getCNPJ())) {
            return $this->getCNPJ($normalize);
        }
        return $this->getCPF($normalize);
    }

    /**
     * Nome do destinatário
     */
    public function getNome($normalize = false)
    {
        return $this->getRazaoSocial($normalize);
    }

    public function setNome($nome)
    {
        return $this->setRazaoSocial($nome);
    }

    /**
     * CPF do cliente
     */
    public function getCPF($normalize = false)
    {
        if (!$normalize) {
            return $this->cpf;
        }
        return $this->cpf;
    }

    public function setCPF($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * Informar o e-mail do destinatário. O campo pode ser utilizado para
     * informar o e-mail de recepção da NF-e indicada pelo destinatário
     */
    public function getEmail($normalize = false)
    {
        if (!$normalize) {
            return $this->email;
        }
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Indicador da IE do destinatário:
     * 1 – Contribuinte ICMSpagamento à
     * vista;
     * 2 – Contribuinte isento de inscrição;
     * 9 – Não Contribuinte
     */
    public function getIndicador($normalize = false)
    {
        if (!$normalize) {
            return $this->indicador;
        }
        switch ($this->indicador) {
            case self::INDICADOR_PAGAMENTO:
                return '1';
            case self::INDICADOR_ISENTO:
                return '2';
            case self::INDICADOR_NENHUM:
                return '9';
        }
        return $this->indicador;
    }

    public function setIndicador($indicador)
    {
        $this->indicador = $indicador;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $destinatario = parent::toArray($recursive);
        $destinatario['nome'] = $this->getNome();
        $destinatario['cpf'] = $this->getCPF();
        $destinatario['email'] = $this->getEmail();
        $destinatario['indicador'] = $this->getIndicador();
        return $destinatario;
    }

    public function fromArray($destinatario = [])
    {
        if ($destinatario instanceof Destinatario) {
            $destinatario = $destinatario->toArray();
        } elseif (!is_array($destinatario)) {
            return $this;
        }
        parent::fromArray($destinatario);
        $this->setNome($destinatario['nome'] ?? null);
        $this->setCPF($destinatario['cpf'] ?? null);
        $this->setEmail($destinatario['email'] ?? null);
        if (!isset($destinatario['indicador'])) {
            $this->setIndicador(self::INDICADOR_NENHUM);
        } else {
            $this->setIndicador($destinatario['indicador']);
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeDestinatarioLoader($this);
        } else {
            $loader = new DestinatarioLoader($this);
        }
        return $loader->getNode($version, $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFeDestinatarioLoader($this);
        } else {
            $loader = new DestinatarioLoader($this);
        }
        return $loader->loadNode($element, $name, $version);
    }
}
