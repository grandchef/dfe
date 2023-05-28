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

use DOMDocument;
use DFe\Core\Nota;
use DFe\Core\SEFAZ;
use DFe\Loader\NFe\Task\LoteLoader;
use DFe\Loader\CFe\Task\LoteLoader as CFeLoteLoader;
use DFe\Loader\CFe\Task\AutorizacaoLoader as CFeAutorizacaoLoader;

class Autorizacao extends Retorno
{
    public function __construct(private Nota $nota, private DOMDocument $document)
    {
        parent::__construct();
    }

    public function getDocument(): DOMDocument
    {
        return $this->document;
    }

    public function getNota(): Nota
    {
        return $this->nota;
    }

    public function setDocument(DOMDocument $document)
    {
        $this->document = $document;
        return $this;
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

    public function getLoteLoader()
    {
        if ($this->nota->getModelo() === Nota::MODELO_CFE) {
            return new CFeLoteLoader($this);
        }
        return new LoteLoader($this);
    }

    public function envia()
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_AUTORIZACAO);
        $envio->setAmbiente($this->nota->getAmbiente());
        $envio->setModelo($this->nota->getModelo());
        $envio->setEmissao($this->nota->getEmissao());
        $this->setVersao($envio->getVersao());

        $loader = $this->getLoteLoader();
        $dom_lote = $loader->getNode()->ownerDocument;
        $envio->setConteudo($dom_lote);
        $resp = $envio->envia();
        $this->loadNode($resp->documentElement);
        if ($this->isProcessado()) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($resp->documentElement);
            if ($protocolo->isAutorizado()) {
                $this->nota->setProtocolo($protocolo);
            }
            return $protocolo;
        } elseif ($this->isRecebido()) {
            $recibo = new Recibo($this->toArray());
            $recibo->setModelo($this->nota->getModelo());
            $recibo->loadNode($resp->documentElement, Recibo::INFO_TAGNAME);
            return $recibo;
        } elseif ($this->isParalisado()) {
            $config = SEFAZ::getInstance()->getConfiguracao();
            $config->setOffline(time());
            throw new \DFe\Exception\NetworkException('Serviço paralisado ou em manutenção', $this->getStatus());
        }
        return $this;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if ($this->nota->getModelo() === Nota::MODELO_CFE) {
            $loader =  new CFeAutorizacaoLoader($this);
            return $loader->loadNode($element, $name, $version);
        }
        $element = parent::loadNode($element, $name ?? 'retEnviNFe');
        return $element;
    }
}
