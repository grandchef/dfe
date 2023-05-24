<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS;

use DFe\Common\Util;

/**
 * Tributação pelo ICMS
 * 40 - Isenta
 * 41 - Não tributada
 * 50 - Suspensão,
 * estende de Generico
 */
class Isento extends Generico
{
    /**
     * Informar o motivo da desoneração:
     * 1 – Táxi;
     * 3 – Produtor Agropecuário;
     * 4
     * – Frotista/Locadora;
     * 5 – Diplomático/Consular;
     * 6 – Utilitários e
     * Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio (Resolução
     * 714/88 e 790/94 – CONTRAN e suas alterações);
     * 7 – SUFRAMA;
     * 8 - Venda a
     * órgão Público;
     * 9 – Outros
     * 10- Deficiente Condutor
     * 11- Deficiente não
     * condutor
     * 16 - Olimpíadas Rio 2016
     */
    public const MOTIVO_TAXI = 'taxi';
    public const MOTIVO_PRODUTOR = 'produtor';
    public const MOTIVO_LOCADORA = 'locadora';
    public const MOTIVO_CONSULAR = 'consular';
    public const MOTIVO_CONTRAN = 'contran';
    public const MOTIVO_SUFRAMA = 'suframa';
    public const MOTIVO_VENDA = 'venda';
    public const MOTIVO_OUTROS = 'outros';
    public const MOTIVO_CONDUTOR = 'condutor';
    public const MOTIVO_DEFICIENTE = 'deficiente';
    public const MOTIVO_OLIMPIADAS = 'olimpiadas';

    private $desoneracao;
    private $motivo;

    public function __construct($isento = [])
    {
        parent::__construct($isento);
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return 0.00; // sempre zero
        }
        return Util::toCurrency($this->getBase());
    }

    /**
     * O valor do ICMS será informado apenas nas operações com veículos
     * beneficiados com a desoneração condicional do ICMS.
     */
    public function getDesoneracao($normalize = false)
    {
        if (!$normalize) {
            return $this->desoneracao;
        }
        return Util::toCurrency($this->desoneracao);
    }

    public function setDesoneracao($desoneracao)
    {
        if (!empty($desoneracao)) {
            $desoneracao = floatval($desoneracao);
        }
        $this->desoneracao = $desoneracao;
        return $this;
    }

    /**
     * Informar o motivo da desoneração:
     * 1 – Táxi;
     * 3 – Produtor Agropecuário;
     * 4
     * – Frotista/Locadora;
     * 5 – Diplomático/Consular;
     * 6 – Utilitários e
     * Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio (Resolução
     * 714/88 e 790/94 – CONTRAN e suas alterações);
     * 7 – SUFRAMA;
     * 8 - Venda a
     * órgão Público;
     * 9 – Outros
     * 10- Deficiente Condutor
     * 11- Deficiente não
     * condutor
     * 16 - Olimpíadas Rio 2016
     */
    public function getMotivo($normalize = false)
    {
        if (!$normalize) {
            return $this->motivo;
        }
        switch ($this->motivo) {
            case self::MOTIVO_TAXI:
                return '1';
            case self::MOTIVO_PRODUTOR:
                return '3';
            case self::MOTIVO_LOCADORA:
                return '4';
            case self::MOTIVO_CONSULAR:
                return '5';
            case self::MOTIVO_CONTRAN:
                return '6';
            case self::MOTIVO_SUFRAMA:
                return '7';
            case self::MOTIVO_VENDA:
                return '8';
            case self::MOTIVO_OUTROS:
                return '9';
            case self::MOTIVO_CONDUTOR:
                return '10';
            case self::MOTIVO_DEFICIENTE:
                return '11';
            case self::MOTIVO_OLIMPIADAS:
                return '16';
        }
        return $this->motivo;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $isento = parent::toArray($recursive);
        $isento['desoneracao'] = $this->getDesoneracao();
        $isento['motivo'] = $this->getMotivo();
        return $isento;
    }

    public function fromArray($isento = [])
    {
        if ($isento instanceof Isento) {
            $isento = $isento->toArray();
        } elseif (!is_array($isento)) {
            return $this;
        }
        parent::fromArray($isento);
        $this->setDesoneracao($isento['desoneracao'] ?? null);
        $this->setMotivo($isento['motivo'] ?? null);
        if (!isset($isento['tributacao'])) {
            $this->setTributacao('40');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $element = parent::getNode($version, $name ?? 'ICMS40');
        if (!is_null($this->getDesoneracao())) {
            Util::appendNode($element, 'vICMSDeson', $this->getDesoneracao(true));
        }
        if (!empty($this->getMotivo())) {
            Util::appendNode($element, 'motDesICMS', $this->getMotivo(true));
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'ICMS40';
        $element = parent::loadNode($element, $name);
        $this->setDesoneracao(Util::loadNode($element, 'vICMSDeson'));
        $this->setMotivo(Util::loadNode($element, 'motDesICMS'));
        return $element;
    }
}
