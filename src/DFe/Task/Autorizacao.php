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
use DFe\Core\SEFAZ;
use DFe\Loader\NFe\V4\Task\LoteLoader;
use DFe\Loader\CFe\V008\Task\LoteLoader as CFeLoteLoader;

class Autorizacao extends Retorno
{
    public function __construct($autorizacao = [])
    {
        parent::__construct($autorizacao);
    }

    public function toArray($recursive = false)
    {
        $autorizacao = parent::toArray($recursive);
        return $autorizacao;
    }

    public function fromArray($autorizacao = [])
    {
        if ($autorizacao instanceof Autorizacao) {
            $autorizacao = $autorizacao->toArray();
        } elseif (!is_array($autorizacao)) {
            return $this;
        }
        parent::fromArray($autorizacao);
        return $this;
    }

    public function getLoteLoader(Nota $nota, \DOMDocument $dom)
    {
        if ($nota->getModelo() === Nota::MODELO_CFE) {
            return new CFeLoteLoader($dom);
        }
        return new LoteLoader($dom);
    }

    public function envia($nota, $dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_AUTORIZACAO);
        $envio->setAmbiente($nota->getAmbiente());
        $envio->setModelo($nota->getModelo());
        $envio->setEmissao($nota->getEmissao());
        $this->setVersao($envio->getVersao());

        $loader = $this->getLoteLoader($nota, $dom);
        $dom_lote = $loader->getNode()->ownerDocument;
        $envio->setConteudo($dom_lote);
        $resp = $envio->envia();
        $this->loadNode($resp->documentElement);
        if ($this->isProcessado()) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($resp->documentElement);
            if ($protocolo->isAutorizado()) {
                $nota->setProtocolo($protocolo);
            }
            return $protocolo;
        } elseif ($this->isRecebido()) {
            $recibo = new Recibo($this->toArray());
            $recibo->setModelo($nota->getModelo());
            $recibo->loadNode($resp->documentElement, Recibo::INFO_TAGNAME);
            return $recibo;
        } elseif ($this->isParalisado()) {
            $config = SEFAZ::getInstance()->getConfiguracao();
            $config->setOffline(time());
            throw new \DFe\Exception\NetworkException('Serviço paralisado ou em manutenção', $this->getStatus());
        }
        return $this;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $tag = $name ?? 'retEnviNFe';
        $element = parent::loadNode($element, $tag);
        return $element;
    }
}
