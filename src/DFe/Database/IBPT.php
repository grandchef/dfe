<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Database;

use Curl\Curl;
use DFe\Logger\Log;

class IBPT
{
    private $tabela;
    private $offline;

    public function __construct()
    {
        $this->tabela = [];
        $this->offline = false;
    }

    public function isOffline()
    {
        return $this->offline;
    }

    public function setOffline($offline)
    {
        $this->offline = $offline;
    }

    private function load($uf)
    {
        if (isset($this->tabela[$uf])) {
            return $this->tabela[$uf];
        }
        $file = __DIR__ . '/data/IBPT/' . $uf . '.json';
        if (!file_exists($file)) {
            return false;
        }
        $content = file_get_contents($file);
        if ($content === false) {
            return false;
        }
        $data = json_decode($content, true);
        $this->tabela = [$uf => $data];
        return $data;
    }

    private function getImpostoOffline($ncm, $uf, $ex)
    {
        $data = $this->load($uf);
        if ($data === false) {
            return false;
        }
        $key = $ncm . '.' . sprintf('%02s', $ex);
        if (!isset($data['estados'][$uf][$key])) {
            return false;
        }
        $o = $data['estados'][$uf][$key];
        $o['info'] = $data['info'];
        $o['info']['origem'] = 'Tabela offline';
        return $o;
    }

    private function getImpostoOnline($cnpj, $token, $ncm, $uf, $ex)
    {
        if ($this->isOffline()) {
            return false;
        }
        $url = 'http://iws.ibpt.org.br/api/Produtos';
        $params = [
            'token' => $token,
            'cnpj' => $cnpj,
            'codigo' => $ncm,
            'uf' => $uf,
            'ex' => intval($ex)
        ];
        $curl = new Curl($url);
        $curl->setConnectTimeout(2);
        $curl->setTimeout(3);
        $data = $curl->get($params);
        if ($curl->error) {
            Log::warning('IBPT.getImpostoOnline(' . $curl->errorCode . ') - ' . $curl->errorMessage);
            $this->setOffline(true);
            return false;
        }
        $o = [
            'importado' => $data->Importado,
            'nacional' => $data->Nacional,
            'estadual' => $data->Estadual,
            'municipal' => $data->Municipal,
            'tipo' => $data->Tipo
        ];
        $vigenciainicio = date_create_from_format('d/m/Y', $data->VigenciaInicio);
        $vigenciafim = date_create_from_format('d/m/Y', $data->VigenciaFim);
        $info = [
            'origem' => 'API IBPT',
            'fonte' => $data->Fonte,
            'versao' => $data->Versao,
            'chave' => $data->Chave,
            'vigencia' => [
                'inicio' => date_format($vigenciainicio, 'Y-m-d'),
                'fim' => date_format($vigenciafim, 'Y-m-d')
            ]
        ];
        $o['info'] = $info;
        return $o;
    }

    public function getImposto($cnpj, $token, $ncm, $uf, $ex)
    {
        $uf = strtoupper($uf);
        $uf = preg_replace('/[^A-Z]/', '', $uf);
        if (is_null($cnpj) || is_null($token)) {
            return $this->getImpostoOffline($ncm, $uf, $ex);
        }
        $o = $this->getImpostoOnline($cnpj, $token, $ncm, $uf, $ex);
        if ($o === false) {
            return $this->getImpostoOffline($ncm, $uf, $ex);
        }
        return $o;
    }
}
