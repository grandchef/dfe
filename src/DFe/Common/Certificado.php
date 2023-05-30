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

use DFe\Common\Node;
use DFe\Common\Util;
use DFe\Util\XmlseclibsAdapter;

/**
 * Certificado digital
 */
class Certificado implements Node
{
    /**
     * @var string
     */
    private $chave_publica;

    /**
     * @var string
     */
    private $chave_privada;

    /**
     * @var string
     */
    private $arquivo_chave_publica;

    /**
     * @var string
     */
    private $arquivo_chave_privada;

    /**
     * @var int
     */
    private $expiracao;

    /**
     * @param mixed $certificado array ou instância
     */
    public function __construct($certificado = [])
    {
        $this->fromArray($certificado);
    }

    /**
     * Carrega o certificado PFX e permite salvar em arquivos PEM
     * @param string $arquivo_pfx caminho do arquivo PFX
     * @param string $senha senha do certificado digital
     * @param bool $extrair informa se deve salvar as chave em arquivos
     * @return self
     */
    public function carrega($arquivo_pfx, $senha, $extrair = false)
    {
        if (!file_exists($arquivo_pfx)) {
            throw new \Exception(sprintf('O certificado "%s" não existe', $arquivo_pfx), 404);
        }
        $cert_store = file_get_contents($arquivo_pfx);
        if (!openssl_pkcs12_read($cert_store, $cert_info, $senha)) {
            throw new \Exception(sprintf(
                'Não foi possível ler o certificado "%s": ' . openssl_error_string(),
                $arquivo_pfx
            ), 404);
        }
        $certinfo = openssl_x509_parse($cert_info['cert']);
        $this->setChavePrivada($cert_info['pkey']);
        $this->setChavePublica($cert_info['cert']);
        if ($extrair) {
            Util::createDirectory(dirname($this->getArquivoChavePrivada()));
            file_put_contents($this->getArquivoChavePrivada(), $this->getChavePrivada());
            Util::createDirectory(dirname($this->getArquivoChavePublica()));
            file_put_contents($this->getArquivoChavePublica(), $this->getChavePublica());
        }
        return $this;
    }

    /**
     * Conteúdo da chave pública ou certificado no formato PEM
     * @return string|null
     */
    public function getChavePublica()
    {
        return $this->chave_publica;
    }

    /**
     * Conteúdo da chave pública ou certificado no formato PEM
     * @param string|null $chave_publica
     * @return self
     */
    public function setChavePublica($chave_publica)
    {
        $this->chave_publica = $chave_publica;
        $this->carregaChavePublica();
        return $this;
    }

    /**
     * Conteúdo da chave privada do certificado no formato PEM
     * @return string
     */
    public function getChavePrivada()
    {
        return $this->chave_privada;
    }

    /**
     * Conteúdo da chave privada do certificado no formato PEM
     * @param string|null $chave_privada
     * @return self
     */
    public function setChavePrivada($chave_privada)
    {
        $this->chave_privada = $chave_privada;
        return $this;
    }

    /**
     * Informa o caminho do arquivo da chave pública ou certificado no formato
     * PEM
     * @return string
     */
    public function getArquivoChavePublica()
    {
        return $this->arquivo_chave_publica;
    }

    /**
     * Informa o caminho do arquivo da chave pública ou certificado no formato
     * PEM
     * @param string|null $arquivo_chave_publica
     * @return self
     */
    public function setArquivoChavePublica($arquivo_chave_publica)
    {
        $this->arquivo_chave_publica = $arquivo_chave_publica;
        if (file_exists($arquivo_chave_publica ?? '')) {
            $this->setChavePublica(file_get_contents($arquivo_chave_publica));
        }
        return $this;
    }

    /**
     * Caminho do arquivo da chave privada do certificado no formato PEM
     * @return string
     */
    public function getArquivoChavePrivada()
    {
        return $this->arquivo_chave_privada;
    }

    /**
     * Altera o caminho do arquivo da chave privada do certificado no formato PEM
     * @param string|null $arquivo_chave_privada
     * @return self
     */
    public function setArquivoChavePrivada($arquivo_chave_privada)
    {
        $this->arquivo_chave_privada = $arquivo_chave_privada;
        if (file_exists($arquivo_chave_privada ?? '')) {
            $this->setChavePrivada(file_get_contents($arquivo_chave_privada));
        }
        return $this;
    }

    /**
     * Data de expiração do certificado em timestamp
     * @return int|null
     */
    public function getExpiracao()
    {
        return $this->expiracao;
    }

    /**
     * Informa a data de expiração do certificado em timestamp
     * @param int|null $expiracao
     * @return self
     */
    private function setExpiracao($expiracao)
    {
        $this->expiracao = $expiracao;
        return $this;
    }

    /**
     * @param bool $recursive
     * @return array
     */
    public function toArray($recursive = false)
    {
        $certificado = [];
        $certificado['arquivo_chave_publica'] = $this->getArquivoChavePublica();
        $certificado['arquivo_chave_privada'] = $this->getArquivoChavePrivada();
        $certificado['expiracao'] = $this->getExpiracao();
        return $certificado;
    }

    /**
     * @param mixed $certificado array ou instância
     * @return self
     */
    public function fromArray($certificado = [])
    {
        if ($certificado instanceof Certificado) {
            $certificado = $certificado->toArray();
        } elseif (!is_array($certificado)) {
            return $this;
        }
        $this->setChavePublica($certificado['chave_publica'] ?? null);
        $this->setChavePrivada($certificado['chave_privada'] ?? null);
        $this->setArquivoChavePublica($certificado['arquivo_chave_publica'] ?? null);
        $this->setArquivoChavePrivada($certificado['arquivo_chave_privada'] ?? null);
        return $this;
    }

    /**
     * Carrega a data de exipiração pela chave pública
     */
    private function carregaChavePublica()
    {
        if (is_null($this->getChavePublica())) {
            $this->setExpiracao(null);
        } else {
            $cert = @openssl_x509_read($this->getChavePublica());
            $cert_data = openssl_x509_parse($cert);
            $this->setExpiracao(isset($cert_data['validTo_time_t']) ? $cert_data['validTo_time_t'] : null);
        }
    }

    /**
     * Ao chamar essa função o certificado precisa estar válido (não expirado)
     * @throws \Exception quando o certificado estiver expirado ou não informado
     */
    public function requerValido()
    {
        if (is_null($this->getExpiracao())) {
            throw new \Exception('A data de expiração do certificado não foi informada', 401);
        } elseif ($this->getExpiracao() < time()) {
            throw new \Exception('O certificado digital expirou', 500);
        }
    }

    /**
     * Assina um dado com o certificado e retorna o base64 dele
     *
     * @throws \Exception quando o certificado estiver expirado ou não informado
     */
    public function assina(string $data): string
    {
        $this->requerValido();
        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($this->getChavePrivada());
        $adapter->setPublicKey($this->getChavePublica());
        return base64_encode($adapter->signData($data));
    }

    /**
     * Obtém o certificado representado como XML
     * @param string $name nome da tag raiz do XML
     * @return \DOMElement
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        throw new \Exception('Não implementado', 500);
    }

    /**
     * Carrega o certificado de um XML
     * @param \DOMElement $element elemento do xml que será carregado
     * @param string $name nome da tag raiz do XML
     * @return \DOMElement elemento que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        throw new \Exception('Não implementado', 500);
    }
}
