<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Common;

/**
 * Evento de emissão de nota fiscal eletrônica
 */
interface Evento
{
    /**
     * Chamado quando o XML da nota foi gerado
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaGerada($nota, $xml);

    /**
     * Chamado após o XML da nota ser assinado
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaAssinada($nota, $xml);

    /**
     * Chamado após o XML da nota ser validado com sucesso
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaValidada($nota, $xml);

    /**
     * Chamado antes de enviar a nota para a SEFAZ
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaEnviando($nota, $xml);

    /**
     * Chamado quando a forma de emissão da nota fiscal muda para contigência,
     * aqui deve ser decidido se o número da nota deverá ser pulado e se esse
     * número deve ser cancelado ou inutilizado
     * @param \DFe\Core\Nota $nota
     * @param bool $offline
     * @param \Exception $exception
     */
    public function onNotaContingencia($nota, $offline, $exception);

    /**
     * Chamado quando a nota foi enviada e aceita pela SEFAZ
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \DFe\Task\Retorno $retorno
     */
    public function onNotaAutorizada($nota, $xml, $retorno);

    /**
     * Chamado quando a emissão da nota foi concluída com sucesso independente
     * da forma de emissão
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     */
    public function onNotaCompleto($nota, $xml);

    /**
     * Chamado quando uma nota é rejeitada pela SEFAZ, a nota deve ser
     * corrigida para depois ser enviada novamente
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \DFe\Task\Retorno $retorno
     */
    public function onNotaRejeitada($nota, $xml, $retorno);

    /**
     * Chamado quando a nota é denegada e não pode ser utilizada (outra nota
     * deve ser gerada)
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \DFe\Task\Retorno $retorno
     */
    public function onNotaDenegada($nota, $xml, $retorno);

    /**
     * Chamado após tentar enviar uma nota e não ter certeza se ela foi
     * recebida ou não (problemas técnicos), deverá ser feito uma consulta pela
     * chave para obter o estado da nota
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \Exception $exception
     */
    public function onNotaPendente($nota, $xml, $exception);

    /**
     * Chamado quando uma nota é enviada, mas não retornou o protocolo que será
     * consultado mais tarde
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \DFe\Task\Retorno $retorno
     */
    public function onNotaProcessando($nota, $xml, $retorno);

    /**
     * Chamado quando uma nota autorizada é cancelada na SEFAZ
     * @param \DFe\Core\Nota $nota
     * @param \DOMDocument $xml
     * @param \DFe\Task\Retorno $retorno
     */
    public function onNotaCancelada($nota, $xml, $retorno);

    /**
     * Chamado quando ocorre um erro nas etapas de geração e envio da nota
     * @param \DFe\Core\Nota $nota
     * @param \Exception $exception
     */
    public function onNotaErro($nota, $exception);

    /**
     * Chamado quando um ou mais números de notas forem inutilizados
     * @param \DFe\Task\Inutilizacao $inutilizacao
     * @param \DOMDocument $xml
     */
    public function onInutilizado($inutilizacao, $xml);

    /**
     * Chamado quando uma tarefa é executada
     * @param \DFe\Task\Tarefa $tarefa
     * @param \DFe\Task\Retorno $retorno
     */
    public function onTarefaExecutada($tarefa, $retorno);

    /**
     * Chamado quando ocorre uma falha na execução de uma tarefa
     * @param \DFe\Task\Tarefa $tarefa
     * @param \Exception $exception
     */
    public function onTarefaErro($tarefa, $exception);
}
