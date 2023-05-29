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

use DFe\Common\Util;
use DFe\Loader\NFe\NotaLoader;

/**
 * Classe para validação da nota fiscal eletrônica
 */
class NFe extends Nota
{
    /**
     * Versão da nota fiscal
     */
    public const VERSAO = '4.00';

    /**
     * Portal da nota fiscal
     */
    public const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    public function __construct($nfe = [])
    {
        parent::__construct($nfe);
        $this->setModelo(self::MODELO_NFE);
    }

    public function gerarID(): string
    {
        /** @var NotaLoader */
        $loader = $this->getLoader();
        return $loader->gerarID();
    }

    public function getLoaderVersion(): string
    {
        $version = $this->getVersao() ?? self::VERSAO;
        return "NFe@{$version}";
    }

    public function toArray($recursive = false)
    {
        $nfe = parent::toArray($recursive);
        return $nfe;
    }

    public function fromArray($nfe = [])
    {
        if ($nfe instanceof NFe) {
            $nfe = $nfe->toArray();
        } elseif (!is_array($nfe)) {
            return $this;
        }
        parent::fromArray($nfe);
        return $this;
    }
}
