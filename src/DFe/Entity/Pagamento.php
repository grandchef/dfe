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
use DFe\Loader\NFe\PagamentoLoader;
use DFe\Loader\CFe\PagamentoLoader as CFePagamentoLoader;

class Pagamento implements Node
{
    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     */
    public const INDICADOR_AVISTA = 'avista';
    public const INDICADOR_APRAZO = 'aprazo';

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     */
    public const FORMA_DINHEIRO = 'dinheiro';
    public const FORMA_CHEQUE = 'cheque';
    public const FORMA_CREDITO = 'credito';
    public const FORMA_DEBITO = 'debito';
    public const FORMA_CREDIARIO = 'crediario';
    public const FORMA_ALIMENTACAO = 'alimentacao';
    public const FORMA_REFEICAO = 'refeicao';
    public const FORMA_PRESENTE = 'presente';
    public const FORMA_COMBUSTIVEL = 'combustivel';
    public const FORMA_DUPLICATA = 'duplicata';
    public const FORMA_BOLETO = 'boleto';
    public const FORMA_DEPOSITO = 'deposito';
    public const FORMA_INSTANTANEO = 'instantaneo';
    public const FORMA_TRANSFERENCIA = 'transferencia';
    public const FORMA_FIDELIDADE = 'fidelidade';
    public const FORMA_CORTESIA = 'cortesia';
    public const FORMA_OUTROS = 'outros';

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     */
    public const BANDEIRA_VISA = 'visa';
    public const BANDEIRA_MASTERCARD = 'mastercard';
    public const BANDEIRA_AMEX = 'amex';
    public const BANDEIRA_SOROCRED = 'sorocred';
    public const BANDEIRA_DINERS = 'diners';
    public const BANDEIRA_ELO = 'elo';
    public const BANDEIRA_HIPERCARD = 'hipercard';
    public const BANDEIRA_AURA = 'aura';
    public const BANDEIRA_CABAL = 'cabal';
    public const BANDEIRA_OUTROS = 'outros';

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     */
    private $indicador;

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     *
     * @var string
     */
    private $forma;

    /**
     * Valor do Pagamento
     *
     * @var float
     */
    private $valor;

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     *
     * @var string
     */
    private $integrado;

    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     *
     * @var string
     */
    private $credenciadora;

    /**
     * Número de autorização da operação cartão de crédito/débito
     *
     * @var string
     */
    private $autorizacao;

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     *
     * @var string
     */
    private $bandeira;

    /**
     * Constroi uma instância de Pagamento vazia
     * @param array $pagamento Array contendo dados do Pagamento
     */
    public function __construct($pagamento = [])
    {
        $this->fromArray($pagamento);
    }

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     *
     * @return mixed indicador da Nota
     */
    public function getIndicador()
    {
        return $this->indicador;
    }

    /**
     * Altera o valor do Indicador para o informado no parâmetro
     * @param mixed $indicador novo valor para Indicador
     * @return self A própria instância da classe
     */
    public function setIndicador($indicador)
    {
        $this->indicador = $indicador;
        return $this;
    }

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     *
     * @return string forma of Pagamento
     */
    public function getForma()
    {
        return $this->forma;
    }

    /**
     * Altera o valor do Forma para o informado no parâmetro
     *
     * @param string|null $forma Novo forma para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setForma($forma)
    {
        $this->forma = $forma;
        return $this;
    }

    /**
     * Valor do Pagamento
     *
     * @return float|string valor of Pagamento
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Altera o valor da Valor para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     *
     * @return string integrado of Pagamento
     */
    public function getIntegrado()
    {
        return $this->integrado;
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     * @return boolean informa se o Integrado está habilitado
     */
    public function isIntegrado()
    {
        return $this->integrado == 'Y';
    }

    /**
     * Altera o valor do Integrado para o informado no parâmetro
     *
     * @param string $integrado Novo integrado para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setIntegrado($integrado)
    {
        $this->integrado = $integrado;
        return $this;
    }

    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     *
     * @return string credenciadora of Pagamento
     */
    public function getCredenciadora()
    {
        return $this->credenciadora;
    }

    /**
     * Altera o valor da Credenciadora para o informado no parâmetro
     *
     * @param string|null $credenciadora Novo credenciadora para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setCredenciadora($credenciadora)
    {
        $this->credenciadora = $credenciadora;
        return $this;
    }

    /**
     * Número de autorização da operação cartão de crédito/débito
     *
     * @return string autorizacao of Pagamento
     */
    public function getAutorizacao()
    {
        return $this->autorizacao;
    }

    /**
     * Altera o valor da Autorizacao para o informado no parâmetro
     *
     * @param string|null $autorizacao Novo autorizacao para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setAutorizacao($autorizacao)
    {
        $this->autorizacao = $autorizacao;
        return $this;
    }

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     *
     * @return string|null bandeira of Pagamento
     */
    public function getBandeira()
    {
        return $this->bandeira;
    }

    /**
     * Altera o valor da Bandeira para o informado no parâmetro
     *
     * @param string|null $bandeira Novo bandeira para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setBandeira($bandeira)
    {
        $this->bandeira = $bandeira;
        return $this;
    }

    /**
     * Informa se o pagamento é em cartão
     */
    public function isCartao()
    {
        return in_array($this->getForma(), [self::FORMA_CREDITO, self::FORMA_DEBITO]);
    }

    public function toArray($recursive = false)
    {
        $pagamento = [];
        $pagamento['indicador'] = $this->getIndicador();
        $pagamento['forma'] = $this->getForma();
        $pagamento['valor'] = $this->getValor();
        $pagamento['integrado'] = $this->getIntegrado();
        $pagamento['credenciadora'] = $this->getCredenciadora();
        $pagamento['autorizacao'] = $this->getAutorizacao();
        $pagamento['bandeira'] = $this->getBandeira();
        return $pagamento;
    }

    public function fromArray($pagamento = [])
    {
        if ($pagamento instanceof Pagamento) {
            $pagamento = $pagamento->toArray();
        } elseif (!is_array($pagamento)) {
            return $this;
        }
        $this->setIndicador($pagamento['indicador'] ?? null);
        $this->setForma($pagamento['forma'] ?? null);
        $this->setValor($pagamento['valor'] ?? null);
        if (!isset($pagamento['integrado'])) {
            $this->setIntegrado('N');
        } else {
            $this->setIntegrado($pagamento['integrado']);
        }
        $this->setCredenciadora($pagamento['credenciadora'] ?? null);
        $this->setAutorizacao($pagamento['autorizacao'] ?? null);
        $this->setBandeira($pagamento['bandeira'] ?? null);
        return $this;
    }


    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFePagamentoLoader($this);
        } else {
            $loader = new PagamentoLoader($this);
        }
        return $loader->getNode($version, $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if (strpos($version, 'CFe@') !== false) {
            $loader = new CFePagamentoLoader($this);
        } else {
            $loader = new PagamentoLoader($this);
        }
        return $loader->loadNode($element, $name, $version);
    }
}
