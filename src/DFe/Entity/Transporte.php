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
use DFe\Entity\Transporte\Veiculo;
use DFe\Entity\Transporte\Tributo;
use DFe\Entity\Transporte\Transportador;

/**
 * Dados dos transportes da NF-e
 */
class Transporte implements Node
{
    /**
     * Modalidade do frete
     * 0- Contratação do Frete por conta do Remetente
     * (CIF);
     * 1- Contratação do Frete por conta do destinatário/remetente
     * (FOB);
     * 2- Contratação do Frete por conta de terceiros;
     * 3- Transporte
     * próprio por conta do remetente;
     * 4- Transporte próprio por conta do
     * destinatário;
     * 9- Sem Ocorrência de transporte.
     */
    public const FRETE_REMETENTE = 'remetente';
    public const FRETE_DESTINATARIO = 'destinatario';
    public const FRETE_TERCEIROS = 'terceiros';
    public const FRETE_PROPRIOREMETENTE = 'proprio_remetente';
    public const FRETE_PROPRIODESTINATARIO = 'proprio_destinatario';
    public const FRETE_NENHUM = 'nenhum';

    private $frete;
    private $transportador;
    private $retencao;
    private $veiculo;
    private $reboque;
    private $vagao;
    private $balsa;
    private $volumes;

    public function __construct($transporte = [])
    {
        $this->fromArray($transporte);
    }

    /**
     * Modalidade do frete
     * 0- Contratação do Frete por conta do Remetente
     * (CIF);
     * 1- Contratação do Frete por conta do destinatário/remetente
     * (FOB);
     * 2- Contratação do Frete por conta de terceiros;
     * 3- Transporte
     * próprio por conta do remetente;
     * 4- Transporte próprio por conta do
     * destinatário;
     * 9- Sem Ocorrência de transporte.
     * @param boolean $normalize informa se o frete deve estar no formato do XML
     * @return mixed frete da Transporte
     */
    public function getFrete($normalize = false)
    {
        if (!$normalize) {
            return $this->frete;
        }
        switch ($this->frete) {
            case self::FRETE_REMETENTE:
                return '0';
            case self::FRETE_DESTINATARIO:
                return '1';
            case self::FRETE_TERCEIROS:
                return '2';
            case self::FRETE_PROPRIOREMETENTE:
                return '3';
            case self::FRETE_PROPRIODESTINATARIO:
                return '4';
            case self::FRETE_NENHUM:
                return '9';
        }
        return $this->frete;
    }

    /**
     * Altera o valor do Frete para o informado no parâmetro
     * @param mixed $frete novo valor para Frete
     * @return self A própria instância da classe
     */
    public function setFrete($frete)
    {
        switch ($frete) {
            case '0':
                $frete = self::FRETE_REMETENTE;
                break;
            case '1':
                $frete = self::FRETE_DESTINATARIO;
                break;
            case '2':
                $frete = self::FRETE_TERCEIROS;
                break;
            case '3':
                $frete = self::FRETE_PROPRIOREMETENTE;
                break;
            case '4':
                $frete = self::FRETE_PROPRIODESTINATARIO;
                break;
            case '9':
                $frete = self::FRETE_NENHUM;
                break;
        }
        $this->frete = $frete;
        return $this;
    }

    /**
     * Dados da transportadora
     */
    public function getTransportador()
    {
        return $this->transportador;
    }

    public function setTransportador($transportador)
    {
        $this->transportador = $transportador;
        return $this;
    }

    /**
     * Dados da retenção  ICMS do Transporte
     */
    public function getRetencao()
    {
        return $this->retencao;
    }

    public function setRetencao($retencao)
    {
        $this->retencao = $retencao;
        return $this;
    }

    /**
     * Dados do veículo
     */
    public function getVeiculo()
    {
        return $this->veiculo;
    }

    public function setVeiculo($veiculo)
    {
        $this->veiculo = $veiculo;
        return $this;
    }

    /**
     * Dados do reboque/Dolly (v2.0)
     */
    public function getReboque()
    {
        return $this->reboque;
    }

    public function setReboque($reboque)
    {
        $this->reboque = $reboque;
        return $this;
    }

    /**
     * Identificação do vagão (v2.0)
     */
    public function getVagao($normalize = false)
    {
        if (!$normalize) {
            return $this->vagao;
        }
        return $this->vagao;
    }

    public function setVagao($vagao)
    {
        $this->vagao = $vagao;
        return $this;
    }

    /**
     * Identificação da balsa (v2.0)
     */
    public function getBalsa($normalize = false)
    {
        if (!$normalize) {
            return $this->balsa;
        }
        return $this->balsa;
    }

    public function setBalsa($balsa)
    {
        $this->balsa = $balsa;
        return $this;
    }

    /**
     * Dados dos volumes
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    public function setVolumes($volumes)
    {
        $this->volumes = $volumes;
        return $this;
    }

    public function addVolume($volume)
    {
        $this->volumes[] = $volume;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $transporte = [];
        $transporte['frete'] = $this->getFrete();
        if (!is_null($this->getTransportador()) && $recursive) {
            $transporte['transportador'] = $this->getTransportador()->toArray($recursive);
        } else {
            $transporte['transportador'] = $this->getTransportador();
        }
        if (!is_null($this->getRetencao()) && $recursive) {
            $transporte['retencao'] = $this->getRetencao()->toArray($recursive);
        } else {
            $transporte['retencao'] = $this->getRetencao();
        }
        if (!is_null($this->getVeiculo()) && $recursive) {
            $transporte['veiculo'] = $this->getVeiculo()->toArray($recursive);
        } else {
            $transporte['veiculo'] = $this->getVeiculo();
        }
        if (!is_null($this->getReboque()) && $recursive) {
            $transporte['reboque'] = $this->getReboque()->toArray($recursive);
        } else {
            $transporte['reboque'] = $this->getReboque();
        }
        $transporte['vagao'] = $this->getVagao();
        $transporte['balsa'] = $this->getBalsa();
        if ($recursive) {
            $volumes = [];
            $_volumes = $this->getVolumes();
            foreach ($_volumes as $_volume) {
                $volumes[] = $_volume->toArray($recursive);
            }
            $transporte['volumes'] = $volumes;
        } else {
            $transporte['volumes'] = $this->getVolumes();
        }
        return $transporte;
    }

    public function fromArray($transporte = [])
    {
        if ($transporte instanceof Transporte) {
            $transporte = $transporte->toArray();
        } elseif (!is_array($transporte)) {
            return $this;
        }
        if (!isset($transporte['frete'])) {
            $this->setFrete(self::FRETE_NENHUM);
        } else {
            $this->setFrete($transporte['frete']);
        }
        $this->setTransportador(
            new Transportador(
                isset($transporte['transportador']) ? $transporte['transportador'] : []
            )
        );
        $this->setRetencao(new Tributo(isset($transporte['retencao']) ? $transporte['retencao'] : []));
        $this->setVeiculo(new Veiculo(isset($transporte['veiculo']) ? $transporte['veiculo'] : []));
        $this->setReboque(new Veiculo(isset($transporte['reboque']) ? $transporte['reboque'] : []));
        if (isset($transporte['vagao'])) {
            $this->setVagao($transporte['vagao']);
        } else {
            $this->setVagao(null);
        }
        if (isset($transporte['balsa'])) {
            $this->setBalsa($transporte['balsa']);
        } else {
            $this->setBalsa(null);
        }
        if (!isset($transporte['volumes'])) {
            $this->setVolumes([]);
        } else {
            $this->setVolumes($transporte['volumes']);
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'transp' : $name);
        Util::appendNode($element, 'modFrete', $this->getFrete(true));
        if ($this->getFrete() == self::FRETE_NENHUM) {
            return $element;
        }
        if (!is_null($this->getTransportador())) {
            $transportador = $this->getTransportador()->getNode();
            $transportador = $dom->importNode($transportador, true);
            $element->appendChild($transportador);
        }
        if (!is_null($this->getRetencao())) {
            $retencao = $this->getRetencao()->getNode();
            $retencao = $dom->importNode($retencao, true);
            $element->appendChild($retencao);
        }
        if (!is_null($this->getVeiculo())) {
            $veiculo = $this->getVeiculo()->getNode('veicTransp');
            $veiculo = $dom->importNode($veiculo, true);
            $element->appendChild($veiculo);
        }
        if (!is_null($this->getReboque())) {
            $reboque = $this->getReboque()->getNode('reboque');
            $reboque = $dom->importNode($reboque, true);
            $element->appendChild($reboque);
        }
        if (!is_null($this->getVagao())) {
            Util::appendNode($element, 'vagao', $this->getVagao(true));
        }
        if (!is_null($this->getBalsa())) {
            Util::appendNode($element, 'balsa', $this->getBalsa(true));
        }
        if (!is_null($this->getVolumes())) {
            $_volumes = $this->getVolumes();
            foreach ($_volumes as $_volume) {
                $volume = $_volume->getNode();
                $volume = $dom->importNode($volume, true);
                $element->appendChild($volume);
            }
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'transp';
        $element = Util::findNode($element, $name);
        $this->setFrete(
            Util::loadNode(
                $element,
                'modFrete',
                'Tag "modFrete" do campo "Frete" não encontrada'
            )
        );
        $_fields = $element->getElementsByTagName('transporta');
        $transportador = null;
        if ($_fields->length > 0) {
            $transportador = new Transportador();
            $transportador->loadNode($_fields->item(0), 'transporta');
        }
        $this->setTransportador($transportador);
        $_fields = $element->getElementsByTagName('retTransp');
        $retencao = null;
        if ($_fields->length > 0) {
            $retencao = new Tributo();
            $retencao->loadNode($_fields->item(0), 'retTransp');
        }
        $this->setRetencao($retencao);
        $_fields = $element->getElementsByTagName('veicTransp');
        $veiculo = null;
        if ($_fields->length > 0) {
            $veiculo = new Veiculo();
            $veiculo->loadNode($_fields->item(0), 'veicTransp');
        }
        $this->setVeiculo($veiculo);
        $_fields = $element->getElementsByTagName('reboque');
        $reboque = null;
        if ($_fields->length > 0) {
            $reboque = new Veiculo();
            $reboque->loadNode($_fields->item(0), 'reboque');
        }
        $this->setReboque($reboque);
        $this->setVagao(Util::loadNode($element, 'vagao'));
        $this->setBalsa(Util::loadNode($element, 'balsa'));
        $volumes = [];
        $_fields = $element->getElementsByTagName('vol');
        foreach ($_fields as $_item) {
            $volume = new Volume();
            $volume->loadNode($_item, 'vol');
            $volumes[] = $volume;
        }
        $this->setVolumes($volumes);
        return $element;
    }
}
