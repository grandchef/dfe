<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Core;

use DOMDocument;
use DFe\Common\Node;
use DFe\Entity\Total;
use DFe\Common\Loader;
use DFe\Common\Util;
use DFe\Entity\Caixa;
use DFe\Task\Protocolo;
use DFe\Entity\Imposto;
use DFe\Entity\Produto;
use DFe\Entity\Emitente;
use DFe\Entity\Pagamento;
use DFe\Entity\Transporte;
use DFe\Entity\Responsavel;
use DFe\Entity\Destinatario;
use DFe\Entity\Intermediador;
use DFe\Util\AdapterInterface;
use DFe\Util\XmlseclibsAdapter;
use DFe\Loader\NFe\V4\NotaLoader;
use DFe\Exception\ValidationException;
use DFe\Loader\CFe\V008\NotaLoader as CFeNotaLoader;

/**
 * Classe base para a formação da nota fiscal
 */
abstract class Nota implements Node
{
    /**
     * Versão da nota fiscal
     */
    public const VERSAO = '4.00';

    /**
     * Versão do aplicativo gerador da nota
     */
    public const APP_VERSAO = '1.0';

    /**
     * Portal da nota fiscal
     */
    public const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 59 = CFe; 65 = NFC-e.
     */
    public const MODELO_NFE = 'nfe';
    public const MODELO_CFE = 'cfe';
    public const MODELO_NFCE = 'nfce';

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     */
    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SAIDA = 'saida';

    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     */
    public const DESTINO_INTERNA = 'interna';
    public const DESTINO_INTERESTADUAL = 'interestadual';
    public const DESTINO_EXTERIOR = 'exterior';

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     */
    public const FORMATO_NENHUMA = 'nenhuma';
    public const FORMATO_RETRATO = 'retrato';
    public const FORMATO_PAISAGEM = 'paisagem';
    public const FORMATO_SIMPLIFICADO = 'simplificado';
    public const FORMATO_CONSUMIDOR = 'consumidor';
    public const FORMATO_MENSAGEM = 'mensagem';

    /**
     * Forma de emissão da NF-e
     */
    public const EMISSAO_NORMAL = 'normal';
    public const EMISSAO_CONTINGENCIA = 'contingencia';

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     */
    public const AMBIENTE_PRODUCAO = 'producao';
    public const AMBIENTE_HOMOLOGACAO = 'homologacao';

    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     */
    public const FINALIDADE_NORMAL = 'normal';
    public const FINALIDADE_COMPLEMENTAR = 'complementar';
    public const FINALIDADE_AJUSTE = 'ajuste';
    public const FINALIDADE_RETORNO = 'retorno';

    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da operação (0-Não se aplica ex.: Nota Fiscal complementar ou de
     * ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
     * presencial, teleatendimento;4-NFC-e entrega em domicílio;5-Operação
     * presencial, fora do estabelecimento;9-Não presencial, outros)
     */
    public const PRESENCA_NENHUM = 'nenhum';
    public const PRESENCA_PRESENCIAL = 'presencial';
    public const PRESENCA_INTERNET = 'internet';
    public const PRESENCA_TELEATENDIMENTO = 'teleatendimento';
    public const PRESENCA_ENTREGA = 'entrega';
    public const PRESENCA_AMBULANTE = 'ambulante';
    public const PRESENCA_OUTROS = 'outros';

    /**
     * Indicador de intermediador/marketplace 0=Operação sem intermediador (em
     * site ou plataforma própria) 1=Operação em site ou plataforma de
     * terceiros (intermediadores/marketplace)
     */
    public const INTERMEDIACAO_NENHUM = 'nenhum';
    public const INTERMEDIACAO_TERCEIROS = 'terceiros';

    /**
     * Chave da nota fiscal
     */
    private $id;

    /**
     * Versão do Documento Fiscal
     */
    private $versao;

    /**
     * Número do Documento Fiscal
     */
    private $numero;

    /**
     * Caixa que realizou a venda
     *
     * @var Caixa
     */
    private $caixa;

    /**
     * Emitente da nota fiscal
     *
     * @var Emitente
     */
    private $emitente;

    /**
     * Destinatário que receberá os produtos
     *
     * @var Destinatario
     */
    private $destinatario;

    /**
     * Grupo de informações do responsável técnico pelo sistema
     *
     * @var Responsavel
     */
    private $responsavel;

    /**
     * Grupo de Informações do Intermediador da Transação
     *
     * @var Intermediador
     */
    private $intermediador;

    /**
     * Produtos adicionados na nota
     *
     * @var Produto[]
     */
    private $produtos;

    /**
     * Informações de trasnporte da mercadoria
     *
     * @var Transporte
     */
    private $transporte;

    /**
     * Pagamentos realizados
     *
     * @var Pagamento[]
     */
    private $pagamentos;

    /**
     * Data e Hora da saída ou de entrada da mercadoria / produto
     */
    private $data_movimentacao;

    /**
     * Informar a data e hora de entrada em contingência
     */
    private $data_contingencia;

    /**
     * Informar a Justificativa da entrada em contingência
     */
    private $justificativa;

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    private $modelo;

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     */
    private $tipo;

    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     */
    private $destino;

    /**
     * Descrição da Natureza da Operação
     */
    private $natureza;

    /**
     * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
     * pelo emitente para cada NF-e.
     */
    private $codigo;

    /**
     * Data e Hora de emissão do Documento Fiscal
     */
    private $data_emissao;

    /**
     * Série do Documento Fiscal: série normal 0-889, Avulsa Fisco 890-899,
     * SCAN 900-999
     */
    private $serie;

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     */
    private $formato;

    /**
     * Forma de emissão da NF-e
     */
    private $emissao;

    /**
     * Digito Verificador da Chave de Acesso da NF-e
     */
    private $digito_verificador;

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     */
    private $ambiente;

    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     */
    private $finalidade;

    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     */
    private $consumidor_final;

    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da oepração (0-Não se aplica, ex.: Nota Fiscal complementar ou
     * de ajuste;1-Operação presencial;2-Não presencial, internet;3-Não
     * presencial, teleatendimento;4-NFC-e entrega em domicílio;9-Não
     * presencial, outros)
     */
    private $presenca;

    /**
     * Indicador de intermediador/marketplace 0=Operação sem intermediador (em
     * site ou plataforma própria) 1=Operação em site ou plataforma de
     * terceiros (intermediadores/marketplace)
     *
     * @var string
     */
    private $intermediacao;

    /**
     * Dados dos totais da NF-e
     */
    private $total;

    /**
     * Informações adicionais de interesse do Fisco
     */
    private $adicionais;

    /**
     * Campo de uso livre do contribuinte informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     */
    private $observacoes;

    /**
     * Campo de uso exclusivo do Fisco informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     */
    private $informacoes;

    /**
     * Protocolo de autorização da nota, informado apenas quando a nota for
     * enviada e autorizada
     *
     * @var Protocolo
     */
    private $protocolo;

    /**
     * Constroi uma instância de Nota vazia
     * @param  array $nota Array contendo dados da Nota
     */
    public function __construct($nota = [])
    {
        $this->fromArray($nota);
    }

    /**
     * Chave da nota fiscal
     *
     * @return mixed id da Nota
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Altera o valor do ID para o informado no parâmetro
     * @param mixed $id novo valor para ID
     * @return self
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Versão do Documento Fiscal
     *
     * @return string|null versão da nota
     */
    public function getVersao()
    {
        return $this->versao;
    }

    /**
     * Altera a versão da nota
     *
     * @param string|null $versao versão da nota
     *
     * @return self
     */
    public function setVersao($versao)
    {
        $this->versao = $versao;
        return $this;
    }

    /**
     * Número do Documento Fiscal
     *
     * @return mixed numero da Nota
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Altera o valor do Numero para o informado no parâmetro
     * @param mixed $numero novo valor para Numero
     * @return self
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Caixa que realizou a venda
     *
     * @return Caixa|null
     */
    public function getCaixa()
    {
        return $this->caixa;
    }

    /**
     * Altera o valor do Caixa para o informado no parâmetro
     *
     * @param Caixa $caixa
     *
     * @return self
     */
    public function setCaixa($caixa)
    {
        $this->caixa = $caixa;
        return $this;
    }

    /**
     * Emitente da nota fiscal
     * @return Emitente|null emitente da Nota
     */
    public function getEmitente()
    {
        return $this->emitente;
    }

    /**
     * Altera o valor do Emitente para o informado no parâmetro
     * @param Emitente $emitente novo valor para Emitente
     * @return self
     */
    public function setEmitente($emitente)
    {
        $this->emitente = $emitente;
        return $this;
    }

    /**
     * Destinatário que receberá os produtos
     * @return Destinatario|null destinatario da Nota
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * Altera o valor do Destinatario para o informado no parâmetro
     * @param Destinatario $destinatario novo valor para Destinatario
     * @return self
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;
        return $this;
    }

    /**
     * Grupo de informações do responsável técnico pelo sistema
     * @return Responsavel|null responsável da Nota
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * Altera o valor do grupo de informações do responsável técnico pelo sistema
     * @param Responsavel $responsavel novo valor para grupo de informações do responsável
     * @return self
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;
        return $this;
    }

    /**
     * Produtos adicionados na nota
     * @return Produto[] produtos da Nota
     */
    public function getProdutos()
    {
        return $this->produtos;
    }

    /**
     * Altera o valor do Produtos para o informado no parâmetro
     *
     * @param Produto[] $produtos
     *
     * @return self
     */
    public function setProdutos($produtos)
    {
        $this->produtos = $produtos;
        return $this;
    }

    /**
     * Adiciona um(a) Produto para a lista de produto
     * @param Produto $produto Instância do Produto que será adicionada
     * @return self
     */
    public function addProduto($produto)
    {
        $this->produtos[] = $produto;
        return $this;
    }

    /**
     * Grupo de Informações do Intermediador da Transação
     *
     * @return Intermediador|null
     */
    public function getIntermediador()
    {
        return $this->intermediador;
    }

    /**
     * Altera o valor do Intermediador para o informado no parâmetro
     *
     * @param Intermediador|null $intermediador
     *
     * @return self
     */
    public function setIntermediador($intermediador)
    {
        $this->intermediador = $intermediador;
        return $this;
    }

    /**
     * Informações de trasnporte da mercadoria
     * @return mixed transporte da Nota
     */
    public function getTransporte()
    {
        return $this->transporte;
    }

    /**
     * Altera o valor da Transporte para o informado no parâmetro
     * @param mixed $transporte novo valor para Transporte
     * @return self
     */
    public function setTransporte($transporte)
    {
        $this->transporte = $transporte;
        return $this;
    }

    /**
     * Pagamentos realizados
     * @return Pagamento[] pagamentos da Nota
     */
    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    /**
     * Altera o valor do Pagamentos para o informado no parâmetro
     * @param mixed $pagamentos novo valor para Pagamentos
     * @return self
     */
    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
        return $this;
    }

    /**
     * Adiciona um(a) Pagamento para a lista de pagamento
     * @param Pagamento $pagamento Instância do Pagamento que será adicionada
     * @return self
     */
    public function addPagamento($pagamento)
    {
        $this->pagamentos[] = $pagamento;
        return $this;
    }

    /**
     * Data e Hora da saída ou de entrada da mercadoria / produto
     *
     * @return mixed data_movimentacao da Nota
     */
    public function getDataMovimentacao()
    {
        return $this->data_movimentacao;
    }

    /**
     * Altera o valor da DataMovimentacao para o informado no parâmetro
     * @param mixed $data_movimentacao novo valor para DataMovimentacao
     * @return self
     */
    public function setDataMovimentacao($data_movimentacao)
    {
        $this->data_movimentacao = $data_movimentacao;
        return $this;
    }

    /**
     * Informar a data e hora de entrada em contingência
     *
     * @return mixed data_contingencia da Nota
     */
    public function getDataContingencia()
    {
        return $this->data_contingencia;
    }

    /**
     * Altera o valor da DataContingencia para o informado no parâmetro
     * @param mixed $data_contingencia novo valor para DataContingencia
     * @return self
     */
    public function setDataContingencia($data_contingencia)
    {
        $this->data_contingencia = $data_contingencia;
        return $this;
    }

    /**
     * Informar a Justificativa da entrada em contingência
     *
     * @return mixed justificativa da Nota
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * Altera o valor da Justificativa para o informado no parâmetro
     * @param mixed $justificativa novo valor para Justificativa
     * @return self
     */
    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     *
     * @return mixed modelo da Nota
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     * @param mixed $modelo novo valor para Modelo
     * @return self
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Tipo do Documento Fiscal (0 - entrada; 1 - saída)
     *
     * @return mixed tipo da Nota
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Altera o valor do Tipo para o informado no parâmetro
     * @param mixed $tipo novo valor para Tipo
     * @return self
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Identificador de Local de destino da operação
     * (1-Interna;2-Interestadual;3-Exterior)
     *
     * @return mixed destino da Nota
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Altera o valor do Destino para o informado no parâmetro
     * @param mixed $destino novo valor para Destino
     * @return self
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;
        return $this;
    }

    /**
     * Descrição da Natureza da Operação
     *
     * @return mixed natureza da Nota
     */
    public function getNatureza()
    {
        return $this->natureza;
    }

    /**
     * Altera o valor da Natureza para o informado no parâmetro
     * @param mixed $natureza novo valor para Natureza
     * @return self
     */
    public function setNatureza($natureza)
    {
        $this->natureza = $natureza;
        return $this;
    }

    /**
     * Código numérico que compõe a Chave de Acesso. Número aleatório gerado
     * pelo emitente para cada NF-e.
     *
     * @return mixed codigo da Nota
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Altera o valor do Codigo para o informado no parâmetro
     * @param mixed $codigo novo valor para Codigo
     * @return self
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Data e Hora de emissão do Documento Fiscal
     *
     * @return int|null
     */
    public function getDataEmissao()
    {
        return $this->data_emissao;
    }

    /**
     * Altera o valor do DataEmissao para o informado no parâmetro
     *
     * @param int|null $data_emissao novo valor para DataEmissao
     *
     * @return self
     */
    public function setDataEmissao($data_emissao)
    {
        $this->data_emissao = $data_emissao;
        return $this;
    }

    /**
     * Série do Documento Fiscal: série normal 0-889, Avulsa Fisco 890-899,
     * SCAN 900-999
     *
     * @return mixed serie da Nota
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Altera o valor do Serie para o informado no parâmetro
     * @param mixed $serie novo valor para Serie
     * @return self
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    /**
     * Formato de impressão do DANFE (0-sem DANFE;1-DANFe Retrato; 2-DANFe
     * Paisagem;3-DANFe Simplificado;4-DANFe NFC-e;5-DANFe NFC-e em mensagem
     * eletrônica)
     *
     * @return mixed formato da Nota
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Altera o valor do Formato para o informado no parâmetro
     * @param mixed $formato novo valor para Formato
     * @return self
     */
    public function setFormato($formato)
    {
        $this->formato = $formato;
        return $this;
    }

    /**
     * Forma de emissão da NF-e
     *
     * @return mixed emissao da Nota
     */
    public function getEmissao()
    {
        return $this->emissao;
    }

    /**
     * Altera o valor do Emissao para o informado no parâmetro
     * @param mixed $emissao novo valor para Emissao
     * @return self
     */
    public function setEmissao($emissao)
    {
        $this->emissao = $emissao;
        return $this;
    }

    /**
     * Digito Verificador da Chave de Acesso da NF-e
     *
     * @return mixed digito_verificador da Nota
     */
    public function getDigitoVerificador()
    {
        return $this->digito_verificador;
    }

    /**
     * Altera o valor do DigitoVerificador para o informado no parâmetro
     * @param mixed $digito_verificador novo valor para DigitoVerificador
     * @return self
     */
    public function setDigitoVerificador($digito_verificador)
    {
        $this->digito_verificador = $digito_verificador;
        return $this;
    }

    /**
     * Identificação do Ambiente: 1 - Produção, 2 - Homologação
     *
     * @return mixed ambiente da Nota
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    /**
     * Altera o valor do Ambiente para o informado no parâmetro
     * @param mixed $ambiente novo valor para Ambiente
     * @return self
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Finalidade da emissão da NF-e: 1 - NFe normal, 2 - NFe complementar, 3 -
     * NFe de ajuste, 4 - Devolução/Retorno
     *
     * @return mixed finalidade da Nota
     */
    public function getFinalidade()
    {
        return $this->finalidade;
    }

    /**
     * Altera o valor da Finalidade para o informado no parâmetro
     * @param mixed $finalidade novo valor para Finalidade
     * @return self
     */
    public function setFinalidade($finalidade)
    {
        $this->finalidade = $finalidade;
        return $this;
    }

    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     *
     * @return mixed consumidor_final da Nota
     */
    public function getConsumidorFinal()
    {
        return $this->consumidor_final;
    }

    /**
     * Indica operação com consumidor final (0-Não;1-Consumidor Final)
     * @return boolean informa se o ConsumidorFinal está habilitado
     */
    public function isConsumidorFinal()
    {
        return $this->consumidor_final == 'Y';
    }

    /**
     * Altera o valor do ConsumidorFinal para o informado no parâmetro
     * @param mixed $consumidor_final novo valor para ConsumidorFinal
     * @return self
     */
    public function setConsumidorFinal($consumidor_final)
    {
        $this->consumidor_final = $consumidor_final;
        return $this;
    }

    /**
     * Indicador de presença do comprador no estabelecimento comercial no
     * momento da oepração (0-Não se aplica (ex.: Nota Fiscal complementar ou
     * de ajuste;1-Operação presencial;2-Não presencial, internet;
     * 3-Não presencial, teleatendimento;4-NFC-e entrega em domicílio;
     * 5-Operação presencial, fora do estabelecimento;9-Não presencial, outros)
     *
     * @return mixed presenca da Nota
     */
    public function getPresenca()
    {
        return $this->presenca;
    }

    /**
     * Altera o valor da Presenca para o informado no parâmetro
     * @param mixed $presenca novo valor para Presenca
     * @return self
     */
    public function setPresenca($presenca)
    {
        $this->presenca = $presenca;
        return $this;
    }

    /**
     * Indicador de intermediador/marketplace 0=Operação sem intermediador (em
     * site ou plataforma própria) 1=Operação em site ou plataforma de
     * terceiros (intermediadores/marketplace)
     *
     * @return string|null intermediacao of Nota
     */
    public function getIntermediacao()
    {
        return $this->intermediacao;
    }

    /**
     * Altera o valor da Intermediacao para o informado no parâmetro
     *
     * @param string|null $intermediacao Novo intermediacao para Nota
     *
     * @return self
     */
    public function setIntermediacao($intermediacao)
    {
        $this->intermediacao = $intermediacao;
        return $this;
    }

    /**
     * Dados dos totais da NF-e
     * @return mixed total da Nota
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Altera o valor do Total para o informado no parâmetro
     * @param mixed $total novo valor para Total
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Informações adicionais de interesse do Fisco
     *
     * @return mixed adicionais da Nota
     */
    public function getAdicionais()
    {
        return $this->adicionais;
    }

    /**
     * Altera o valor da Adicionais para o informado no parâmetro
     * @param mixed $adicionais novo valor para Adicionais
     * @return self
     */
    public function setAdicionais($adicionais)
    {
        $this->adicionais = $adicionais;
        return $this;
    }

    /**
     * Campo de uso livre do contribuinte informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     * @return mixed observacoes da Nota
     */
    public function getObservacoes()
    {
        return $this->observacoes;
    }

    /**
     * Altera o valor da Observacoes para o informado no parâmetro
     * @param mixed $observacoes novo valor para Observacoes
     * @return self
     */
    public function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
        return $this;
    }

    /**
     * Adiciona um(a) Observacao para a lista de observacao
     *
     * @param string $observacao Instância da Observacao que será adicionada
     *
     * @return self
     */
    public function addObservacao($campo, $observacao)
    {
        $this->observacoes[] = ['campo' => $campo, 'valor' => $observacao];
        return $this;
    }

    /**
     * Campo de uso exclusivo do Fisco informar o nome do campo no atributo
     * xCampo e o conteúdo do campo no xTexto
     * @return mixed informacoes da Nota
     */
    public function getInformacoes()
    {
        return $this->informacoes;
    }

    /**
     * Altera o valor da Informacoes para o informado no parâmetro
     * @param mixed $informacoes novo valor para Informacoes
     * @return self
     */
    public function setInformacoes($informacoes)
    {
        $this->informacoes = $informacoes;
        return $this;
    }

    /**
     * Adiciona um(a) Informacao para a lista de informacao
     *
     * @param string $informacao Instância da Informacao que será adicionada
     *
     * @return self
     */
    public function addInformacao($campo, $informacao)
    {
        $this->informacoes[] = ['campo' => $campo, 'valor' => $informacao];
        return $this;
    }

    /**
     * Protocolo de autorização da nota, informado apenas quando a nota for
     * enviada e autorizada
     */
    public function getProtocolo()
    {
        return $this->protocolo;
    }

    public function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $nota = [];
        $nota['id'] = $this->getID();
        $nota['versao'] = $this->getVersao();
        $nota['numero'] = $this->getNumero();
        if (!is_null($this->getCaixa()) && $recursive) {
            $nota['caixa'] = $this->getCaixa()->toArray($recursive);
        } else {
            $nota['caixa'] = $this->getCaixa();
        }
        if (!is_null($this->getEmitente()) && $recursive) {
            $nota['emitente'] = $this->getEmitente()->toArray($recursive);
        } else {
            $nota['emitente'] = $this->getEmitente();
        }
        if (!is_null($this->getDestinatario()) && $recursive) {
            $nota['destinatario'] = $this->getDestinatario()->toArray($recursive);
        } else {
            $nota['destinatario'] = $this->getDestinatario();
        }
        if (!is_null($this->getResponsavel()) && $recursive) {
            $nota['responsavel'] = $this->getResponsavel()->toArray($recursive);
        } else {
            $nota['responsavel'] = $this->getResponsavel();
        }
        if ($recursive) {
            $produtos = [];
            $_produtos = $this->getProdutos();
            foreach ($_produtos as $_produto) {
                $produtos[] = $_produto->toArray($recursive);
            }
            $nota['produtos'] = $produtos;
        } else {
            $nota['produtos'] = $this->getProdutos();
        }
        if (!is_null($this->getIntermediador()) && $recursive) {
            $nota['intermediador'] = $this->getIntermediador()->toArray($recursive);
        } else {
            $nota['intermediador'] = $this->getIntermediador();
        }
        if (!is_null($this->getTransporte()) && $recursive) {
            $nota['transporte'] = $this->getTransporte()->toArray($recursive);
        } else {
            $nota['transporte'] = $this->getTransporte();
        }
        if ($recursive) {
            $pagamentos = [];
            $_pagamentos = $this->getPagamentos();
            foreach ($_pagamentos as $_pagamento) {
                $pagamentos[] = $_pagamento->toArray($recursive);
            }
            $nota['pagamentos'] = $pagamentos;
        } else {
            $nota['pagamentos'] = $this->getPagamentos();
        }
        $nota['data_movimentacao'] = $this->getDataMovimentacao();
        $nota['data_contingencia'] = $this->getDataContingencia();
        $nota['justificativa'] = $this->getJustificativa();
        $nota['modelo'] = $this->getModelo();
        $nota['tipo'] = $this->getTipo();
        $nota['destino'] = $this->getDestino();
        $nota['natureza'] = $this->getNatureza();
        $nota['codigo'] = $this->getCodigo();
        $nota['data_emissao'] = $this->getDataEmissao();
        $nota['serie'] = $this->getSerie();
        $nota['formato'] = $this->getFormato();
        $nota['emissao'] = $this->getEmissao();
        $nota['digito_verificador'] = $this->getDigitoVerificador();
        $nota['ambiente'] = $this->getAmbiente();
        $nota['finalidade'] = $this->getFinalidade();
        $nota['consumidor_final'] = $this->getConsumidorFinal();
        $nota['presenca'] = $this->getPresenca();
        $nota['intermediacao'] = $this->getIntermediacao();
        if (!is_null($this->getTotal()) && $recursive) {
            $nota['total'] = $this->getTotal()->toArray($recursive);
        } else {
            $nota['total'] = $this->getTotal();
        }
        $nota['adicionais'] = $this->getAdicionais();
        $nota['observacoes'] = $this->getObservacoes();
        $nota['informacoes'] = $this->getInformacoes();
        if (!is_null($this->getProtocolo()) && $recursive) {
            $nota['protocolo'] = $this->getProtocolo()->toArray($recursive);
        } else {
            $nota['protocolo'] = $this->getProtocolo();
        }
        return $nota;
    }

    public function fromArray($nota = [])
    {
        if ($nota instanceof Nota) {
            $nota = $nota->toArray();
        } elseif (!is_array($nota)) {
            return $this;
        }
        $this->setID($nota['id'] ?? null);
        $this->setVersao($nota['versao'] ?? null);
        $this->setNumero($nota['numero'] ?? null);
        $this->setCaixa(new Caixa($nota['caixa'] ?? []));
        $this->setEmitente(new Emitente($nota['emitente'] ?? []));
        $this->setDestinatario(new Destinatario($nota['destinatario'] ?? []));
        $this->setResponsavel(new Responsavel($nota['responsavel'] ?? []));
        $this->setProdutos($nota['produtos'] ?? []);
        if (isset($nota['intermediador'])) {
            $this->setIntermediador(new Intermediador($nota['intermediador'] ?? []));
        } else {
            $this->setIntermediador(null);
        }
        $this->setTransporte(new Transporte($nota['transporte'] ?? []));
        if (!isset($nota['pagamentos'])) {
            $this->setPagamentos([]);
        } else {
            $this->setPagamentos($nota['pagamentos']);
        }
        $this->setDataMovimentacao($nota['data_movimentacao'] ?? null);
        $this->setDataContingencia($nota['data_contingencia'] ?? null);
        $this->setJustificativa($nota['justificativa'] ?? null);
        $this->setModelo($nota['modelo'] ?? null);
        if (!isset($nota['tipo'])) {
            $this->setTipo(self::TIPO_SAIDA);
        } else {
            $this->setTipo($nota['tipo']);
        }
        if (!isset($nota['destino'])) {
            $this->setDestino(self::DESTINO_INTERNA);
        } else {
            $this->setDestino($nota['destino']);
        }
        if (!isset($nota['natureza'])) {
            $this->setNatureza('VENDA PARA CONSUMIDOR FINAL');
        } else {
            $this->setNatureza($nota['natureza']);
        }
        $this->setCodigo($nota['codigo'] ?? null);
        $this->setDataEmissao($nota['data_emissao'] ?? null);
        $this->setSerie($nota['serie'] ?? null);
        if (!isset($nota['formato'])) {
            $this->setFormato(self::FORMATO_NENHUMA);
        } else {
            $this->setFormato($nota['formato']);
        }
        if (!isset($nota['emissao'])) {
            $this->setEmissao(self::EMISSAO_NORMAL);
        } else {
            $this->setEmissao($nota['emissao']);
        }
        $this->setDigitoVerificador($nota['digito_verificador'] ?? null);
        if (!isset($nota['ambiente'])) {
            $this->setAmbiente(self::AMBIENTE_HOMOLOGACAO);
        } else {
            $this->setAmbiente($nota['ambiente']);
        }
        if (!isset($nota['finalidade'])) {
            $this->setFinalidade(self::FINALIDADE_NORMAL);
        } else {
            $this->setFinalidade($nota['finalidade']);
        }
        if (!isset($nota['consumidor_final'])) {
            $this->setConsumidorFinal('Y');
        } else {
            $this->setConsumidorFinal($nota['consumidor_final']);
        }
        $this->setPresenca($nota['presenca'] ?? null);
        $this->setIntermediacao($nota['intermediacao'] ?? null);
        $this->setTotal(new Total(isset($nota['total']) ? $nota['total'] : []));
        if (!array_key_exists('adicionais', $nota)) {
            $this->setAdicionais(null);
        } else {
            $this->setAdicionais($nota['adicionais']);
        }
        if (!array_key_exists('observacoes', $nota)) {
            $this->setObservacoes(null);
        } else {
            $this->setObservacoes($nota['observacoes']);
        }
        if (!array_key_exists('informacoes', $nota)) {
            $this->setInformacoes(null);
        } else {
            $this->setInformacoes($nota['informacoes']);
        }
        $this->setProtocolo($nota['protocolo'] ?? null);
        return $this;
    }

    public function getTotais()
    {
        $total = [];
        $total['produtos'] = 0.00;
        $total['desconto'] = 0.00;
        $total['frete'] = 0.00;
        $total['seguro'] = 0.00;
        $total['despesas'] = 0.00;
        $total['tributos'] = 0.00;
        $total['icms'] = 0.00;
        $total['icms.st'] = 0.00;
        $total['base'] = 0.00;
        $total['base.st'] = 0.00;
        $total['ii'] = 0.00;
        $total['ipi'] = 0.00;
        $total['pis'] = 0.00;
        $total['cofins'] = 0.00;
        $total['desoneracao'] = 0.00;
        $total['fundo'] = 0.00;
        $total['fundo.st'] = 0.00;
        $total['fundo.retido.st'] = 0.00;
        $total['ipi.devolvido'] = 0.00;
        $_produtos = $this->getProdutos();
        foreach ($_produtos as $_produto) {
            if (!$_produto->getMultiplicador()) {
                continue;
            }
            $imposto_info = $_produto->getImpostoInfo();
            $total['produtos'] += round($_produto->getPreco() ?: 0, 2);
            $total['desconto'] += round($_produto->getDesconto() ?: 0, 2);
            $total['frete'] += round($_produto->getFrete() ?: 0, 2);
            $total['seguro'] += round($_produto->getSeguro() ?: 0, 2);
            $total['despesas'] += round($_produto->getDespesas() ?: 0, 2);
            $total['tributos'] += round($imposto_info['total'] ?: 0, 2);
            $_impostos = $_produto->getImpostos();
            foreach ($_impostos as $_imposto) {
                switch ($_imposto->getGrupo()) {
                    case Imposto::GRUPO_ICMS:
                        if (
                            ($_imposto instanceof \DFe\Entity\Imposto\ICMS\Cobranca)
                            || ($_imposto instanceof \DFe\Entity\Imposto\ICMS\Simples\Cobranca)
                        ) {
                            $total[$_imposto->getGrupo()] += round($_imposto->getNormal()->getValor() ?: 0, 2);
                            $total['base'] += round($_imposto->getNormal()->getBase() ?: 0, 2);
                        }
                        if ($_imposto instanceof \DFe\Entity\Imposto\ICMS\Parcial) {
                            $total['icms.st'] += round($_imposto->getValor() ?: 0, 2);
                            $total['base.st'] += round($_imposto->getBase() ?: 0, 2);
                        } else {
                            $total[$_imposto->getGrupo()] += round($_imposto->getValor() ?: 0, 2);
                            $total['base'] += round($_imposto->getBase() ?: 0, 2);
                        }
                        $fundo = $_imposto->getFundo();
                        // a ordem de comparação importa pois uma classe estende da outra
                        if ($fundo instanceof \DFe\Entity\Imposto\Fundo\Retido) {
                            $total['fundo.retido.st'] += round($fundo->getTotal() ?: 0, 2);
                        } elseif ($fundo instanceof \DFe\Entity\Imposto\Fundo\Substituido) {
                            $total['fundo.st'] += round($fundo->getTotal() ?: 0, 2);
                        } elseif ($fundo instanceof \DFe\Entity\Imposto\Fundo\Base) {
                            $total['fundo'] += round($fundo->getTotal() ?: 0, 2);
                        }
                        break;
                    default:
                        $total[$_imposto->getGrupo()] += round($_imposto->getValor() ?: 0, 2);
                }
            }
        }
        $produtos = round($total['produtos'] ?: 0, 2) - round($total['desconto'] ?: 0, 2);
        $servicos = round($total['frete'] ?: 0, 2) + round($total['seguro'], 2) + round($total['despesas'] ?: 0, 2);
        $impostos = round($total['ii'] ?: 0, 2) + round($total['ipi'], 2) + round($total['icms.st'] ?: 0, 2);
        $impostos = $impostos - round($total['desoneracao'] ?: 0, 2);
        $total['nota'] = $produtos + $servicos + $impostos;
        return $total;
    }

    public function getLoaderVersion(): string
    {
        if ($this->getModelo() === self::MODELO_CFE) {
            $version = $this->getVersao() ?: CFeNotaLoader::VERSAO;
            return "CFe@{$version}";
        }
        $version = $this->getVersao() ?? self::VERSAO;
        return "NFe@{$version}";
    }

    public function getLoader(string $version = ''): Loader
    {
        if (strpos($version ?: $this->getLoaderVersion(), 'CFe@') !== false) {
            return new CFeNotaLoader($this);
        }
        return new NotaLoader($this);
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $loader = $this->getLoader($version);
        return $loader->getNode($version ?: $this->getLoaderVersion(), $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if ($element->nodeName === 'CFe') {
            $this->setModelo(self::MODELO_CFE);
            $versionNode = Util::findNode($element, 'infCFe');
            $invoiceVersion = $versionNode->getAttribute('versaoDadosEnt');
            $this->setVersao($invoiceVersion);
        } else {
            $this->setModelo(self::MODELO_NFE);
            $versionNode = Util::findNode($element, 'infNFe');
            $invoiceVersion = $versionNode->getAttribute('versao');
            $this->setVersao($invoiceVersion);
        }
        $loader = $this->getLoader($version);
        return $loader->loadNode($element, $name, $version ?: $this->getLoaderVersion());
    }

    /**
     * Carrega um arquivo XML e preenche a nota com as informações dele
     * @param  string $filename caminho do arquivo
     * @return DOMDocument      objeto do documento carregado
     */
    public function load($filename)
    {
        $dom = new \DOMDocument();
        if (!file_exists($filename)) {
            throw new \Exception('Arquivo XML "' . $filename . '" não encontrado', 404);
        }
        $dom->load($filename);
        $this->loadNode($dom->documentElement);
        return $dom;
    }

    /**
     * Assina o XML com a assinatura eletrônica do tipo A1
     */
    public function assinar($dom = null)
    {
        if (is_null($dom)) {
            $xml = $this->getNode();
            $dom = $xml->ownerDocument;
        }
        $config = SEFAZ::getInstance()->getConfiguracao();
        $config->verificaValidadeCertificado();

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($config->getCertificado()->getChavePrivada());
        $adapter->setPublicKey($config->getCertificado()->getChavePublica());
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($dom, 'infNFe');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = __DIR__ . '/schema';
        if (is_null($this->getProtocolo())) {
            $xsd_file = $xsd_path . '/NFe/v4.0.0/nfe_v' . self::VERSAO . '.xsd';
        } else {
            $xsd_file = $xsd_path . '/NFe/v4.0.0/procNFe_v' . self::VERSAO . '.xsd';
        }
        if (!file_exists($xsd_file)) {
            throw new \Exception(sprintf('O arquivo "%s" de esquema XSD não existe!', $xsd_file), 404);
        }
        // Enable user error handling
        $save = libxml_use_internal_errors(true);
        if ($dom->schemaValidate($xsd_file)) {
            libxml_use_internal_errors($save);
            return $dom;
        }
        $msg = [];
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $msg[] = 'Não foi possível validar o XML: ' . $error->message;
        }
        libxml_clear_errors();
        libxml_use_internal_errors($save);
        throw new ValidationException($msg);
    }

    /**
     * Adiciona o protocolo no XML da nota
     */
    public function addProtocolo($dom)
    {
        if (is_null($this->getProtocolo())) {
            throw new \Exception('O protocolo não foi informado na nota "' . $this->getID() . '"', 404);
        }
        $notae = $dom->getElementsByTagName('NFe')->item(0);
        // Corrige xmlns:default
        $notae_xml = $dom->saveXML($notae);

        $element = $dom->createElement('nfeProc');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($notae);
        // Corrige xmlns:default
        $notae = $dom->createElement('NFe', 0);

        $element->appendChild($notae);
        $info = $this->getProtocolo()->getNode();
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<NFe>0</NFe>', $notae_xml, $xml);
        $dom->loadXML($xml);

        return $dom;
    }
}
