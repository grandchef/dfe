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
 * Classe base dos impostos
 */
abstract class Imposto implements Node
{
    /**
     * Tipo de imposto
     */
    public const TIPO_IMPORTADO = 'importado';
    public const TIPO_NACIONAL = 'nacional';
    public const TIPO_ESTADUAL = 'estadual';
    public const TIPO_MUNICIPAL = 'municipal';

    /**
     * Grupo do imposto
     */
    public const GRUPO_ICMS = 'icms';
    public const GRUPO_PIS = 'pis';
    public const GRUPO_COFINS = 'cofins';
    public const GRUPO_IPI = 'ipi';
    public const GRUPO_II = 'ii';
    public const GRUPO_PISST = 'pisst';
    public const GRUPO_COFINSST = 'cofinsst';
    public const GRUPO_ISSQN = 'issqn';
    public const GRUPO_ICMSUFDEST = 'icmsufdest';

    private $tipo;
    private $grupo;
    private $tributacao;
    private $aliquota;
    private $base;

    public function __construct($imposto = [])
    {
        $this->fromArray($imposto);
    }

    /**
     * Tipo de imposto
     */
    public function getTipo($normalize = false)
    {
        if (!$normalize) {
            return $this->tipo;
        }
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Grupo do imposto
     */
    public function getGrupo($normalize = false)
    {
        if (!$normalize) {
            return $this->grupo;
        }
        switch ($this->grupo) {
            case self::GRUPO_ICMS:
                return 'ICMS';
            case self::GRUPO_PIS:
                return 'PIS';
            case self::GRUPO_COFINS:
                return 'COFINS';
            case self::GRUPO_IPI:
                return 'IPI';
            case self::GRUPO_II:
                return 'II';
            case self::GRUPO_PISST:
                return 'PISST';
            case self::GRUPO_COFINSST:
                return 'COFINSST';
            case self::GRUPO_ISSQN:
                return 'ISSQN';
            case self::GRUPO_ICMSUFDEST:
                return 'ICMSUFDest';
        }
        return $this->grupo;
    }

    public function setGrupo($grupo)
    {
        switch ($grupo) {
            case 'ICMS':
                $grupo = self::GRUPO_ICMS;
                break;
            case 'PIS':
                $grupo = self::GRUPO_PIS;
                break;
            case 'COFINS':
                $grupo = self::GRUPO_COFINS;
                break;
            case 'IPI':
                $grupo = self::GRUPO_IPI;
                break;
            case 'II':
                $grupo = self::GRUPO_II;
                break;
            case 'PISST':
                $grupo = self::GRUPO_PISST;
                break;
            case 'COFINSST':
                $grupo = self::GRUPO_COFINSST;
                break;
            case 'ISSQN':
                $grupo = self::GRUPO_ISSQN;
                break;
            case 'ICMSUFDest':
                $grupo = self::GRUPO_ICMSUFDEST;
                break;
        }
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * Código da situação tributária
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return $this->tributacao;
        }
        return $this->tributacao;
    }

    public function setTributacao($tributacao)
    {
        $this->tributacao = $tributacao;
        return $this;
    }

    /**
     * Porcentagem do imposto
     */
    public function getAliquota($normalize = false)
    {
        if (!$normalize) {
            return $this->aliquota;
        }
        return Util::toFloat($this->aliquota);
    }

    public function setAliquota($aliquota)
    {
        $this->aliquota = $aliquota;
        return $this;
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return $this->base;
        }
        return Util::toCurrency($this->base);
    }

    /**
     * Altera o valor do Base para o informado no parâmetro
     * @param mixed $base novo valor para Base
     * @return self A própria instância da classe
     */
    public function setBase($base)
    {
        $this->base = $base;
        return $this;
    }

    /**
     * Calcula o valor do imposto com base na aliquota e valor base
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return ($this->getBase() * $this->getAliquota()) / 100.0;
        }
        return Util::toCurrency($this->getValor());
    }

    /**
     * Obtém o valor total do imposto
     */
    public function getTotal($normalize = false)
    {
        return $this->getValor($normalize);
    }

    public function toArray($recursive = false)
    {
        $imposto = [];
        $imposto['tipo'] = $this->getTipo();
        $imposto['grupo'] = $this->getGrupo();
        $imposto['tributacao'] = $this->getTributacao();
        $imposto['aliquota'] = $this->getAliquota();
        $imposto['base'] = $this->getBase();
        $imposto['valor'] = $this->getValor();
        return $imposto;
    }

    public function fromArray($imposto = [])
    {
        if ($imposto instanceof Imposto) {
            $imposto = $imposto->toArray();
        } elseif (!is_array($imposto)) {
            return $this;
        }
        $this->setTipo($imposto['tipo'] ?? null);
        $this->setGrupo($imposto['grupo'] ?? null);
        $this->setTributacao($imposto['tributacao'] ?? null);
        $this->setAliquota($imposto['aliquota'] ?? null);
        $this->setBase($imposto['base'] ?? null);
        return $this;
    }

    public static function criaPeloNome($nome, $quantitativo = false)
    {
        switch ($nome) {
                /* Grupo COFINS */
            case 'COFINSAliq':
                $imposto = new Imposto\COFINS\Aliquota();
                break;
            case 'COFINSOutr':
                $imposto = new Imposto\COFINS\Generico();
                break;
            case 'COFINSNT':
                $imposto = new Imposto\COFINS\Isento();
                break;
            case 'COFINSSN':
                $imposto = new Imposto\COFINS\Simples();
                break;
            case 'COFINSQtde':
                $imposto = new Imposto\COFINS\Quantidade();
                break;
                /* Grupo COFINSST */
            case 'COFINSST':
                if ($quantitativo) {
                    $imposto = new Imposto\COFINS\ST\Quantidade();
                } else {
                    $imposto = new Imposto\COFINS\ST\Aliquota();
                }
                break;
                /* Grupo ICMS */
            case 'ICMS60':
                $imposto = new Imposto\ICMS\Cobrado();
                break;
            case 'ICMS10':
                $imposto = new Imposto\ICMS\Cobranca();
                break;
            case 'ICMS51':
                $imposto = new Imposto\ICMS\Diferido();
                break;
            case 'ICMS90':
                $imposto = new Imposto\ICMS\Generico();
                break;
            case 'ICMS00':
                $imposto = new Imposto\ICMS\Integral();
                break;
            case 'ICMS40':
                $imposto = new Imposto\ICMS\Isento();
                break;
            case 'ICMS70':
                $imposto = new Imposto\ICMS\Mista();
                break;
            case 'ICMS30':
                $imposto = new Imposto\ICMS\Parcial();
                break;
            case 'ICMSPart':
                $imposto = new Imposto\ICMS\Partilha();
                break;
            case 'ICMS20':
                $imposto = new Imposto\ICMS\Reducao();
                break;
            case 'ICMSST':
                $imposto = new Imposto\ICMS\Substituto();
                break;
                /* Grupo ICMS Simples */
            case 'ICMSSN500':
                $imposto = new Imposto\ICMS\Simples\Cobrado();
                break;
            case 'ICMSSN201':
                $imposto = new Imposto\ICMS\Simples\Cobranca();
                break;
            case 'ICMSSN900':
                $imposto = new Imposto\ICMS\Simples\Generico();
                break;
            case 'ICMSSN102':
                $imposto = new Imposto\ICMS\Simples\Isento();
                break;
            case 'ICMSSN101':
                $imposto = new Imposto\ICMS\Simples\Normal();
                break;
            case 'ICMSSN202':
                $imposto = new Imposto\ICMS\Simples\Parcial();
                break;
                /* Grupo IPI */
            case 'IPITrib':
                if ($quantitativo) {
                    $imposto = new Imposto\IPI\Quantidade();
                } else {
                    $imposto = new Imposto\IPI\Aliquota();
                }
                break;
            case 'IPINT':
                $imposto = new Imposto\IPI\Isento();
                break;
                /* Grupo PIS */
            case 'PISAliq':
                $imposto = new Imposto\PIS\Aliquota();
                break;
            case 'PISOutr':
                $imposto = new Imposto\PIS\Generico();
                break;
            case 'PISNT':
                $imposto = new Imposto\PIS\Isento();
                break;
            case 'PISQtde':
                $imposto = new Imposto\PIS\Quantidade();
                break;
                /* Grupo PISST */
            case 'PISST':
                if ($quantitativo) {
                    $imposto = new Imposto\PIS\ST\Quantidade();
                } else {
                    $imposto = new Imposto\PIS\ST\Aliquota();
                }
                break;
            case 'PISSN':
                $imposto = new Imposto\PIS\Simples();
                break;
                /* Grupo II básico */
            case 'II':
                $imposto = new Imposto\II();
                break;
                /* Grupo IPI básico */
            case 'IPI':
                $imposto = new Imposto\IPI();
                break;
            default:
                return false;
        }
        return $imposto;
    }

    public static function loadImposto(\DOMElement $element, string $version = '')
    {
        $quantitativo = false;
        switch ($element->nodeName) {
                /* Grupo COFINSST */
            case 'COFINSST':
                $_fields = $element->getElementsByTagName('pCOFINS');
                $quantitativo = $_fields->length == 0;
                break;
                /* Grupo IPI */
            case 'IPITrib':
                $_fields = $element->getElementsByTagName('pIPI');
                $quantitativo = $_fields->length == 0;
                break;
                /* Grupo PISST */
            case 'PISST':
                $_fields = $element->getElementsByTagName('pPIS');
                $quantitativo = $_fields->length == 0;
                break;
        }
        $imposto = self::criaPeloNome($element->nodeName, $quantitativo);
        if ($imposto === false) {
            return false;
        }
        $imposto->loadNode($element, $element->nodeName, $version);
        return $imposto;
    }
}
