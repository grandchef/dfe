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

use DFe\Logger\Log;
use DFe\Task\Tarefa;
use DFe\Task\Evento;
use DFe\Task\Recibo;
use DFe\Task\Retorno;
use DFe\Common\Ajuste;
use DFe\Task\Protocolo;
use DFe\Task\Autorizacao;
use DFe\Task\Inutilizacao;

/**
 * Classe que envia uma ou mais notas fiscais para os servidores da sefaz
 */
class SEFAZ
{
    /**
     * Lista de notas a serem processadas
     * @var Nota[]
     */
    private $notas;

    /**
     * Configurações a serem usadas
     * @var \DFe\Common\Configuracao
     */
    private $configuracao;

    /**
     * Instância global
     * @var self
     */
    private static $instance = null;

    /**
     * Constroi uma intência a partir de outra ou array
     * @param mixed $sefaz outra instância ou array
     */
    public function __construct($sefaz = [])
    {
        $this->fromArray($sefaz);
    }

    /**
     * Obtém a instância global dessa classe
     * @param bool $new cria uma nova instância
     * @return self default instance
     */
    public static function getInstance($new = false)
    {
        if (is_null(self::$instance) || $new) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lista de notas a serem processadas
     * @return Nota[]
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Informa a lista de notas a serem processadas
     * @param Nota[] $notas
     * @return self
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;
        return $this;
    }

    /**
     * Adiciona uma nota ao processamento
     * @param Nota $nota
     * @return self
     */
    public function addNota($nota)
    {
        $this->notas[] = $nota;
        return $this;
    }

    /**
     * Configuração usada atualmente
     * @return \DFe\Common\Configuracao|null
     */
    public function getConfiguracao()
    {
        return $this->configuracao;
    }

    /**
     * Informa a nova configuração a ser usada
     * @param \DFe\Common\Configuracao $configuracao
     * @return self
     */
    public function setConfiguracao($configuracao)
    {
        $this->configuracao = $configuracao;
        return $this;
    }

    /**
     * Converte a instância atual em array
     * @param bool $recursive se deve converter os itens também em array
     * @return array
     */
    public function toArray($recursive = false)
    {
        $sefaz = [];
        if ($recursive) {
            $notas = [];
            $_notas = $this->getNotas();
            foreach ($_notas as $_nota) {
                $notas[] = $_nota->toArray($recursive);
            }
            $sefaz['notas'] = $notas;
        } else {
            $sefaz['notas'] = $this->getNotas();
        }
        if (!is_null($this->getConfiguracao()) && $recursive) {
            $sefaz['configuracao'] = $this->getConfiguracao()->toArray($recursive);
        } else {
            $sefaz['configuracao'] = $this->getConfiguracao();
        }
        return $sefaz;
    }

    /**
     * Preenche a instância atual com dados do array ou outra instância
     * @param mixed $sefaz outra instância ou array de dados
     * @return self
     */
    public function fromArray($sefaz = [])
    {
        if ($sefaz instanceof SEFAZ) {
            $sefaz = $sefaz->toArray();
        } elseif (!is_array($sefaz)) {
            return $this;
        }
        if (!isset($sefaz['notas'])) {
            $this->setNotas([]);
        } else {
            $this->setNotas($sefaz['notas']);
        }
        $this->setConfiguracao(new Ajuste(isset($sefaz['configuracao']) ? $sefaz['configuracao'] : []));
        return $this;
    }

    /**
     * Chama os eventos da nota lançando exceção em caso de rejeição
     * @param Nota $nota nota a ser despachada
     * @param \DOMDocument $dom xml da nota
     * @param Retorno|Recibo $retorno resposta da SEFAZ
     */
    private function despacha($nota, $dom, $retorno)
    {
        $evento = $this->getConfiguracao()->getEvento();
        if ($retorno->isRecebido()) {
            if ($retorno instanceof Recibo) {
                Log::debug('SEFAZ.despacha - Recibo: ' . $retorno->getNumero() . ' da ' . $nota->getID());
            }
            $evento->onNotaProcessando($nota, $dom, $retorno);
        } elseif ($retorno->isAutorizado()) {
            $dom = $nota->addProtocolo($dom);
            if (
                $retorno instanceof Recibo
                || $retorno instanceof Protocolo
                || $retorno instanceof Inutilizacao
                || $retorno instanceof Evento
            ) {
                Log::debug('SEFAZ.despacha(' . $retorno->getStatus() . ') - ' . $retorno->getMotivo() .
                    ', Protocolo: ' . $retorno->getNumero() . ' - ' . $nota->getID());
            }
            $evento->onNotaAutorizada($nota, $dom, $retorno);
        } elseif ($retorno->isDenegada()) {
            $evento->onNotaDenegada($nota, $dom, $retorno);
        } elseif ($retorno->isCancelado()) {
            $evento->onNotaCancelada($nota, $dom, $retorno);
        } else {
            $evento->onNotaRejeitada($nota, $dom, $retorno);
            throw new \Exception($retorno->getMotivo(), $retorno->getStatus());
        }
    }

    /**
     * Envia as notas adicionadas para a SEFAZ, caso não consiga, torna-as em contingência
     * todos os status são informados no evento da configuração, caso não possua evento,
     * uma \Exception será lançada na primeira falha
     * @return int quantidade de notas processadas ou que entraram em contingência
     */
    public function autoriza()
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($this->getNotas() as $index => $nota) {
            try {
                $envia = true;
                do {
                    $dom = $nota->getNode()->ownerDocument;
                    $evento->onNotaGerada($nota, $dom);
                    $dom = $nota->assinar($dom);
                    $evento->onNotaAssinada($nota, $dom);
                    $dom = $nota->validar($dom); // valida o XML da nota
                    $evento->onNotaValidada($nota, $dom);
                    if (!$envia) {
                        break;
                    }
                    $evento->onNotaEnviando($nota, $dom);
                    $autorizacao = new Autorizacao();
                    try {
                        $retorno = $autorizacao->envia($nota, $dom);
                    } catch (\Exception $e) {
                        $partial_response = $e instanceof \DFe\Exception\IncompleteRequestException;
                        if ($partial_response) {
                            $evento->onNotaPendente($nota, $dom, $e);
                        }
                        if (
                            $nota->getEmissao() == Nota::EMISSAO_CONTINGENCIA
                            || $nota->getModelo() == Nota::MODELO_CFE
                        ) {
                            throw $e;
                        }
                        Log::debug('SEFAZ.autoriza(' . $e->getCode() . ') - Mudando emissão para contingência: ' .
                            $e->getMessage() . ' - ' . $nota->getID());
                        $msg = 'Falha no envio da nota';
                        $nota->setEmissao(Nota::EMISSAO_CONTINGENCIA);
                        $nota->setDataContingencia(time());
                        $nota->setJustificativa($msg);
                        $nova_nota = $evento->onNotaContingencia($nota, !$partial_response, $e);
                        if (isset($nova_nota)) {
                            $nota = $nova_nota;
                            Log::debug('SEFAZ.autoriza(' . $e->getCode() .
                                ') - Mudando tipo de nota em contingência: ' . $nota->getID());
                            $this->notas[$index] = $nota;
                        }
                        $envia = isset($nova_nota);
                        continue;
                    }
                    Log::debug('SEFAZ.autoriza(' . $retorno->getStatus() . ') - ' .
                        $retorno->getMotivo() . ' - ' . $nota->getID());
                    $this->despacha($nota, $dom, $retorno);
                    break;
                } while (true);
                $evento->onNotaCompleto($nota, $dom);
                $i++;
            } catch (\Exception $e) {
                Log::error('SEFAZ.autoriza(' . $e->getCode() . ') - ' . $e->getMessage());
                $evento->onNotaErro($nota, $e);
            }
        }
        return $i;
    }

    /**
     * Consulta o status das notas em processamento
     * @return int quantidade de consultas executadas com sucesso
     */
    public function consulta($pendencias)
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($pendencias as $pendencia) {
            $nota = $pendencia->getNota();
            try {
                $retorno = $pendencia->executa();
                $dom = $pendencia->getDocumento();
                Log::debug('SEFAZ.consulta(' . $retorno->getStatus() . ') - ' .
                    $retorno->getMotivo() . ' - ' . $nota->getID());
                $this->despacha($nota, $dom, $retorno);
                $evento->onNotaCompleto($nota, $dom);
                $pendencia->setDocumento($dom);
                $evento->onTarefaExecutada($pendencia, $retorno);
                $i++;
            } catch (\Exception $e) {
                Log::error('SEFAZ.consulta(' . $e->getCode() . ') - ' . $e->getMessage());
                $evento->onNotaErro($nota, $e);
            }
        }
        return $i;
    }

    /**
     * Consulta se as notas existem e cancela ou inutiliza seus números
     * Também processa pedido de inutilização e cancelamento de notas
     * @return int quantidade de tarefas executadas com sucesso
     */
    public function executa($tarefas)
    {
        $i = 0;
        $evento = $this->getConfiguracao()->getEvento();
        foreach ($tarefas as $tarefa) {
            try {
                $save_dom = $tarefa->getDocumento();
                $retorno = $tarefa->executa();
                $dom = $tarefa->getDocumento();
                Log::debug('SEFAZ.executa(' . $retorno->getStatus() . ') - ' . $retorno->getMotivo() .
                    ' - Tarefa: ' . $tarefa->getID());
                switch ($tarefa->getAcao()) {
                    case Tarefa::ACAO_INUTILIZAR:
                        $inutilizacao = $tarefa->getAgente();
                        Log::debug('SEFAZ.executa[inutiliza](' . $inutilizacao->getStatus() . ') - ' .
                            $inutilizacao->getMotivo() . ' - ' . $inutilizacao->getID());
                        $evento->onInutilizado($inutilizacao, $dom);
                        break;
                    default:
                        $nota = $tarefa->getNota();
                        $this->despacha($nota, $save_dom, $retorno);
                        $evento->onNotaCompleto($nota, $save_dom);
                }
                $evento->onTarefaExecutada($tarefa, $retorno);
                $i++;
            } catch (\Exception $e) {
                Log::error('SEFAZ.executa(' . $e->getCode() . ') - ' . $e->getMessage());
                $evento->onTarefaErro($tarefa, $e);
            }
        }
        return $i;
    }

    /**
     * Inutiliza um intervalo de números de notas fiscais e insere o resultado no
     * próprio objeto de inutilização
     * @param \DFe\Task\Inutilizacao $inutilizacao tarefa a ser inutilizada
     * @return bool se a inutilização foi realizada com sucesso
     */
    public function inutiliza($inutilizacao)
    {
        $tarefa = new Tarefa();
        $tarefa->setAcao(Tarefa::ACAO_INUTILIZAR);
        $tarefa->setAgente($inutilizacao);
        try {
            $this->executa([$tarefa]);
        } catch (\Exception $e) {
            Log::error('SEFAZ.inutiliza(' . $e->getCode() . ') - ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Obtém as notas pendentes de autorização e envia para a SEFAZ
     * @return int quantidade de tarefas e notas processadas
     */
    public function processa()
    {
        $i = 0;
        /* Envia as notas não enviadas, rejeitadas e em contingência */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $notas = $db->getNotasAbertas();
            $this->setNotas($notas);
            $i += $this->autoriza();
        } catch (\Exception $e) {
            Log::error('SEFAZ.processa[autoriza](' . $e->getCode() . ') - ' . $e->getMessage());
        }
        /* Consulta o status das notas em processamento */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $pendencias = $db->getNotasPendentes();
            $i += $this->consulta($pendencias);
        } catch (\Exception $e) {
            Log::error('SEFAZ.processa[pendentes](' . $e->getCode() . ') - ' . $e->getMessage());
        }
        /* Consulta se as notas existem e cancela ou inutiliza seus números
         * Também processa pedido de inutilização e cancelamento de notas
         */
        try {
            $db = $this->getConfiguracao()->getBanco();
            $tarefas = $db->getNotasTarefas();
            $i += $this->executa($tarefas);
        } catch (\Exception $e) {
            Log::error('SEFAZ.processa[tarefas](' . $e->getCode() . ') - ' . $e->getMessage());
        }
        return $i;
    }
}
