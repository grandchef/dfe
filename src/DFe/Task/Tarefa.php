<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Task;

use DFe\Core\Nota;

class Tarefa
{
    /**
     * Ação a ser realizada sobre o objeto ou recibo
     */
    public const ACAO_CONSULTAR = 'consultar';
    public const ACAO_INUTILIZAR = 'inutilizar';
    public const ACAO_CANCELAR = 'cancelar';

    private $id;
    private $acao;
    private $nota;
    private $documento;
    private $agente;
    private $resposta;

    public function __construct($tarefa = [])
    {
        $this->fromArray($tarefa);
    }

    /**
     * Código aleatório e opcional que identifica a tarefa
     */
    public function getID()
    {
        return $this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Ação a ser realizada sobre o objeto ou recibo
     */
    public function getAcao()
    {
        return $this->acao;
    }

    public function setAcao($acao)
    {
        $this->acao = $acao;
        return $this;
    }

    /**
     * Nota que será processada se informado
     *
     * @return Nota
     */
    public function getNota()
    {
        return $this->nota;
    }

    public function setNota($nota)
    {
        $this->nota = $nota;
        return $this;
    }

    /**
     * Informa o XML do objeto, quando não informado o XML é gerado a partir do
     * objeto
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
        return $this;
    }

    /**
     * Agente que obteve ou vai obter a resposta, podendo ser: pedido de
     * inutilização(NF\Inutilizacao), recibo(NF\Recibo) ou pedido de
     * cancelamento(NF\Evento)
     *
     * @return Inutilizacao|Evento|Situacao
     */
    public function getAgente()
    {
        return $this->agente;
    }

    public function setAgente($agente)
    {
        $this->agente = $agente;
        return $this;
    }

    /**
     * Resposta da tarefa após ser executada
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    public function setResposta($resposta)
    {
        $this->resposta = $resposta;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $tarefa = [];
        $tarefa['id'] = $this->getID();
        $tarefa['acao'] = $this->getAcao();
        if (!is_null($this->getNota()) && $recursive) {
            $tarefa['nota'] = $this->getNota()->toArray($recursive);
        } else {
            $tarefa['nota'] = $this->getNota();
        }
        if (!is_null($this->getDocumento()) && $recursive) {
            $tarefa['documento'] = $this->getDocumento()->saveXML();
        } else {
            $tarefa['documento'] = $this->getDocumento();
        }
        if (!is_null($this->getAgente()) && $recursive) {
            $tarefa['agente'] = $this->getAgente()->toArray($recursive);
        } else {
            $tarefa['agente'] = $this->getAgente();
        }
        if (!is_null($this->getResposta()) && $recursive) {
            $tarefa['resposta'] = $this->getResposta()->toArray($recursive);
        } else {
            $tarefa['resposta'] = $this->getResposta();
        }
        return $tarefa;
    }

    public function fromArray($tarefa = [])
    {
        if ($tarefa instanceof Tarefa) {
            $tarefa = $tarefa->toArray();
        } elseif (!is_array($tarefa)) {
            return $this;
        }
        $this->setID($tarefa['id'] ?? null);
        $this->setAcao($tarefa['acao'] ?? null);
        $this->setNota($tarefa['nota'] ?? null);
        $this->setDocumento($tarefa['documento'] ?? null);
        $this->setAgente($tarefa['agente'] ?? null);
        $this->setResposta($tarefa['resposta'] ?? null);
        return $this;
    }

    /**
     * Resposta da tarefa após ser executada
     */
    public function executa()
    {
        $retorno = null;
        switch ($this->getAcao()) {
            case self::ACAO_CANCELAR:
                $retorno = $this->cancela();
                break;
            case self::ACAO_INUTILIZAR:
                $retorno = $this->inutiliza();
                break;
            case self::ACAO_CONSULTAR:
                $retorno = $this->consulta();
                break;
        }
        $this->setResposta($retorno);
        return $this->getResposta();
    }

    private function cancela()
    {
        $nota = $this->getNota();
        $evento = $this->getAgente();
        if (is_null($evento)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de cancelamento', 404);
            }
            if (is_null($nota->getProtocolo()) && $nota->getModelo() != Nota::MODELO_CFE) {
                throw new \Exception(
                    'A nota "' . $nota->getID() . '" não possui protocolo de autorização para o cancelamento',
                    404
                );
            }
            $evento = new Evento();
            $evento->setData(time());
            $evento->setOrgao($nota->getEmitente()->getEndereco()->getMunicipio()->getEstado()->getUF());
            $evento->setJustificativa($nota->getJustificativa());
            $this->setAgente($evento);
        } elseif (!($evento instanceof Evento)) {
            throw new \Exception('O agente informado não é um evento', 500);
        }
        if (!is_null($nota)) {
            $evento->setAmbiente($nota->getAmbiente());
            $evento->setModelo($nota->getModelo());
            $evento->setIdentificador($nota->getEmitente()->getCNPJ());
            $evento->setCaixa($nota->getCaixa());
            $evento->setResponsavel($nota->getResponsavel());
            if (!is_null($nota->getProtocolo())) {
                $evento->setNumero($nota->getProtocolo()->getNumero());
            }
            $evento->setChave($nota->getID());
        }
        $evento->envia();
        if ($evento->getInformacao()->isCancelado()) {
            $this->setDocumento($evento->getDocumento());
        }
        return $evento->getInformacao();
    }

    private function inutiliza()
    {
        $nota = $this->getNota();
        $inutilizacao = $this->getAgente();
        if (is_null($inutilizacao)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de inutilização', 404);
            }
            $inutilizacao = new Inutilizacao();
            $inutilizacao->setAno(date('Y'));
            $inutilizacao->setJustificativa($nota->getJustificativa());
            $this->setAgente($inutilizacao);
        } elseif (!($inutilizacao instanceof Inutilizacao)) {
            throw new \Exception('O agente informado não é uma inutilização', 500);
        }
        if (!is_null($nota)) {
            $inutilizacao->setCNPJ($nota->getEmitente()->getCNPJ());
            $inutilizacao->setSerie($nota->getSerie());
            $inutilizacao->setInicio($nota->getNumero());
            $inutilizacao->setFinal($nota->getNumero());
            $inutilizacao->setUF($nota->getEmitente()->getEndereco()->getMunicipio()->getEstado()->getUF());
            $inutilizacao->setAmbiente($nota->getAmbiente());
            $inutilizacao->setModelo($nota->getModelo());
        }
        $dom = $inutilizacao->getNode()->ownerDocument;
        $dom = $inutilizacao->assinar($dom);
        $dom = $inutilizacao->envia($dom);
        $this->setDocumento($dom);
        return $inutilizacao;
    }

    private function consulta()
    {
        $nota = $this->getNota();
        /** @var Situacao|Recibo */
        $agente = $this->getAgente();
        if (is_null($agente)) {
            if (is_null($nota)) {
                throw new \Exception('A nota não foi informada na tarefa de consulta', 404);
            }
            $agente = new Situacao();
            $agente->setChave($nota->getID());
            $this->setAgente($agente);
        } elseif (!($agente instanceof Situacao) && !($agente instanceof Recibo)) {
            throw new \Exception('O agente informado não é uma consulta de situação e nem um recibo', 500);
        }
        if (!is_null($nota)) {
            $agente->setAmbiente($nota->getAmbiente());
            $agente->setModelo($nota->getModelo());
        }
        $retorno = $agente->consulta($this->getNota());
        if ($agente->isCancelado()) {
            $this->setDocumento($retorno->getDocumento());
            $retorno = $retorno->getInformacao();
        }
        return $retorno;
    }
}
