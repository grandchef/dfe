<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\Task;

use DFe\Core\Nota;
use DFe\Common\Util;
use DFe\Task\Status;
use DFe\Common\Loader;
use DFe\Entity\Estado;

/**
 * Status das respostas de envios para os servidores da SEFAZ
 */
class StatusLoader implements Loader
{
    public function __construct(private Status $status)
    {
    }

    public function getAmbiente()
    {
        switch ($this->status->getAmbiente()) {
            case Nota::AMBIENTE_PRODUCAO:
                return '1';
            case Nota::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->status->getAmbiente();
    }

    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->status->setAmbiente($ambiente);
        return $this;
    }

    /**
     * código da UF de atendimento
     */
    public function getUF()
    {
        if (is_numeric($this->status->getUF())) {
            return $this->status->getUF();
        }

        $estado = new Estado();
        $estado->setUF($this->status->getUF());
        $estado->checkCodigos();
        return $estado->getCodigo();
    }

    /**
     * Gera um número único com 15 dígitos
     *
     * @return string
     */
    public static function genLote()
    {
        return substr(Util::padText(number_format(microtime(true) * 1000000, 0, '', ''), 15), 0, 15);
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'Status');
        Util::appendNode($element, 'tpAmb', $this->getAmbiente());
        Util::appendNode($element, 'verAplic', $this->status->getVersao());
        Util::appendNode($element, 'cStat', $this->status->getStatus());
        Util::appendNode($element, 'xMotivo', $this->status->getMotivo());
        if (!is_null($this->status->getUF())) {
            Util::appendNode($element, 'cUF', $this->getUF());
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'Status';
        $element = Util::findNode($element, $name);
        $this->setAmbiente(
            Util::loadNode(
                $element,
                'tpAmb',
                'Tag "tpAmb" não encontrada no Status'
            )
        );
        $this->status->setVersao(
            Util::loadNode(
                $element,
                'verAplic',
                'Tag "verAplic" não encontrada no Status'
            )
        );
        $this->status->setStatus(
            Util::loadNode(
                $element,
                'cStat',
                'Tag "cStat" não encontrada no Status'
            )
        );
        $this->status->setMotivo(
            Util::loadNode(
                $element,
                'xMotivo',
                'Tag "xMotivo" não encontrada no Status'
            )
        );
        $this->status->setUF(Util::loadNode($element, 'cUF'));
        return $element;
    }
}
