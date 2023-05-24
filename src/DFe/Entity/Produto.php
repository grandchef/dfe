<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity;

use DFe\Core\SEFAZ;
use DFe\Common\Node;
use DFe\Common\Util;

/**
 * Produto ou serviço que está sendo vendido ou prestado e será adicionado
 * na nota fiscal
 */
class Produto extends Total
{
    /**
     * Unidade do produto, Não informar a grandeza
     */
    public const UNIDADE_UNIDADE = 'unidade';
    public const UNIDADE_PECA = 'peca';
    public const UNIDADE_METRO = 'metro';
    public const UNIDADE_GRAMA = 'grama';
    public const UNIDADE_LITRO = 'litro';

    private $item;
    private $pedido;
    private $codigo;
    private $codigo_tributario;
    private $codigo_barras;
    private $descricao;
    private $unidade;
    private $multiplicador;
    private $quantidade;
    private $tributada;
    private $peso;
    private $excecao;
    private $cfop;
    private $ncm;
    private $cest;
    private $impostos;

    public function __construct($produto = [])
    {
        $this->fromArray($produto);
    }

    /**
     * Número do Item do Pedido de Compra - Identificação do número do item do
     * pedido de Compra
     */
    public function getItem($normalize = false)
    {
        if (!$normalize) {
            return $this->item;
        }
        return $this->item;
    }

    public function setItem($item)
    {
        if (!empty($item)) {
            $item = intval($item);
        }
        $this->item = $item;
        return $this;
    }

    /**
     * informar o número do pedido de compra, o campo é de livre uso do emissor
     */
    public function getPedido($normalize = false)
    {
        if (!$normalize) {
            return $this->pedido;
        }
        return $this->pedido;
    }

    public function setPedido($pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    /**
     * Código do produto ou serviço. Preencher com CFOP caso se trate de itens
     * não relacionados com mercadorias/produto e que o contribuinte não possua
     * codificação própria
     * Formato ”CFOP9999”.
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Código do produto ou serviço. Preencher com CFOP caso se trate de itens
     * não relacionados com mercadorias/produto e que o contribuinte não possua
     * codificação própria
     * Formato ”CFOP9999”.
     */
    public function getCodigoTributario($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo_tributario;
        }
        return $this->codigo_tributario;
    }

    public function setCodigoTributario($codigo_tributario)
    {
        $this->codigo_tributario = $codigo_tributario;
        return $this;
    }

    /**
     * GTIN (Global Trade Item Number) do produto, antigo código EAN ou código
     * de barras
     */
    public function getCodigoBarras($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo_barras;
        }
        return $this->codigo_barras;
    }

    public function setCodigoBarras($codigo_barras)
    {
        $this->codigo_barras = $codigo_barras;
        return $this;
    }

    /**
     * Descrição do produto ou serviço
     */
    public function getDescricao($normalize = false)
    {
        if (!$normalize) {
            return $this->descricao;
        }
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Unidade do produto, Não informar a grandeza
     */
    public function getUnidade($normalize = false)
    {
        if (!$normalize) {
            return $this->unidade;
        }
        switch ($this->unidade) {
            case self::UNIDADE_UNIDADE:
                return 'UN';
            case self::UNIDADE_PECA:
                return 'PC';
            case self::UNIDADE_METRO:
                return 'm';
            case self::UNIDADE_GRAMA:
                return 'g';
            case self::UNIDADE_LITRO:
                return 'L';
        }
        return $this->unidade;
    }

    public function setUnidade($unidade)
    {
        switch ($unidade) {
            case 'UN':
                $unidade = self::UNIDADE_UNIDADE;
                break;
            case 'PC':
                $unidade = self::UNIDADE_PECA;
                break;
            case 'm':
                $unidade = self::UNIDADE_METRO;
                break;
            case 'g':
                $unidade = self::UNIDADE_GRAMA;
                break;
            case 'L':
                $unidade = self::UNIDADE_LITRO;
                break;
        }
        $this->unidade = $unidade;
        return $this;
    }

    public function getMultiplicador($normalize = false)
    {
        if (!$normalize) {
            return $this->multiplicador;
        }
        return $this->multiplicador;
    }

    public function setMultiplicador($multiplicador)
    {
        if (!empty($multiplicador)) {
            $multiplicador = intval($multiplicador);
        }
        $this->multiplicador = $multiplicador;
        return $this;
    }

    /**
     * Valor unitário de comercialização  - alterado para aceitar 0 a 10 casas
     * decimais e 11 inteiros
     */
    public function getPreco($normalize = false)
    {
        return parent::getProdutos($normalize);
    }

    /**
     * Altera o preço total do produto para o informado no parâmetro
     * @param mixed $preco novo preço para o Produto
     * @return self A própria instância da classe
     */
    public function setPreco($preco)
    {
        $this->setProdutos($preco);
        return $this;
    }

    /**
     * Quantidade Comercial  do produto, alterado para aceitar de 0 a 4 casas
     * decimais e 11 inteiros.
     */
    public function getQuantidade($normalize = false)
    {
        if (!$normalize) {
            return $this->quantidade;
        }
        return Util::toFloat($this->quantidade);
    }

    public function setQuantidade($quantidade)
    {
        if (!empty($quantidade)) {
            $quantidade = floatval($quantidade);
        }
        $this->quantidade = $quantidade;
        return $this;
    }

    /**
     * Informa a quantidade tributada
     */
    public function getTributada($normalize = false)
    {
        if (!$normalize) {
            return is_null($this->tributada) ? $this->getQuantidade() : $this->tributada;
        }
        return Util::toFloat($this->getTributada());
    }

    public function setTributada($tributada)
    {
        if (!empty($tributada)) {
            $tributada = floatval($tributada);
        }
        $this->tributada = $tributada;
        return $this;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
        return $this;
    }

    /**
     * Código EX TIPI
     */
    public function getExcecao($normalize = false)
    {
        if (!$normalize) {
            return $this->excecao;
        }
        return Util::padDigit($this->excecao, 2);
    }

    public function setExcecao($excecao)
    {
        $this->excecao = $excecao;
        return $this;
    }

    public function getCFOP($normalize = false)
    {
        if (!$normalize) {
            return $this->cfop;
        }
        return $this->cfop;
    }

    public function setCFOP($cfop)
    {
        if (!empty($cfop)) {
            $cfop = intval($cfop);
        }
        $this->cfop = $cfop;
        return $this;
    }

    /**
     * Código NCM (8 posições), será permitida a informação do gênero (posição
     * do capítulo do NCM) quando a operação não for de comércio exterior
     * (importação/exportação) ou o produto não seja tributado pelo IPI. Em
     * caso de item de serviço ou item que não tenham produto (Ex.
     * transferência de crédito, crédito do ativo imobilizado, etc.), informar
     * o código 00 (zeros) (v2.0)
     */
    public function getNCM($normalize = false)
    {
        if (!$normalize) {
            return $this->ncm;
        }
        return $this->ncm;
    }

    public function setNCM($ncm)
    {
        $this->ncm = $ncm;
        return $this;
    }

    public function getCEST($normalize = false)
    {
        if (!$normalize) {
            return $this->cest;
        }
        return $this->cest;
    }

    public function setCEST($cest)
    {
        $this->cest = $cest;
        return $this;
    }

    public function getImpostos()
    {
        return $this->impostos;
    }

    public function setImpostos($impostos)
    {
        $this->impostos = $impostos;
        return $this;
    }

    public function addImposto($imposto)
    {
        $this->impostos[] = $imposto;
        return $this;
    }

    /**
     * Valor unitário
     */
    public function getPrecoUnitario($normalize = false)
    {
        if (!$normalize) {
            return $this->getPreco() / $this->getQuantidade();
        }
        return Util::toCurrency($this->getPrecoUnitario(), 10);
    }

    /**
     * Valor tributável
     */
    public function getPrecoTributavel($normalize = false)
    {
        if (!$normalize) {
            return $this->getPreco() / $this->getTributada();
        }
        return Util::toCurrency($this->getPrecoTributavel(), 10);
    }

    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return $this->getPreco() - $this->getDesconto();
        }
        return Util::toCurrency($this->getBase());
    }

    public function getImpostoInfo()
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $db = $config->getBanco();
        $endereco = $config->getEmitente()->getEndereco();
        $info = ['total' => 0.00];
        $tipos = [
            // Imposto::TIPO_IMPORTADO, // TODO: determinar quando usar
            Imposto::TIPO_NACIONAL,
            Imposto::TIPO_ESTADUAL,
            Imposto::TIPO_MUNICIPAL
        ];
        $imposto = new \DFe\Entity\Imposto\Total();
        $imposto->setBase($this->getBase());
        $aliquota = $db->getImpostoAliquota(
            $this->getNCM(),
            $endereco->getMunicipio()->getEstado()->getUF(),
            $this->getExcecao(),
            $config->getEmitente()->getCNPJ(),
            $config->getTokenIBPT()
        );
        if ($aliquota === false) {
            throw new \Exception(
                sprintf(
                    'NCM inválido no item %d - "%s"',
                    $this->getItem(),
                    $this->getDescricao()
                ),
                404
            );
        }
        foreach ($tipos as $tipo) {
            $imposto->setAliquota($aliquota[$tipo]);
            $tributo = round($imposto->getTotal(), 2);
            $info[$tipo] = $tributo;
            $info['total'] += $tributo;
        }
        $info['info'] = $aliquota['info'];
        return $info;
    }

    public function toArray($recursive = false)
    {
        $produto = parent::toArray($recursive);
        unset($produto['produtos']);
        $produto['item'] = $this->getItem();
        $produto['pedido'] = $this->getPedido();
        $produto['codigo'] = $this->getCodigo();
        $produto['codigo_tributario'] = $this->getCodigoTributario();
        $produto['codigo_barras'] = $this->getCodigoBarras();
        $produto['descricao'] = $this->getDescricao();
        $produto['unidade'] = $this->getUnidade();
        $produto['multiplicador'] = $this->getMultiplicador();
        $produto['preco'] = $this->getPreco();
        $produto['quantidade'] = $this->getQuantidade();
        $produto['tributada'] = $this->getTributada();
        if (!is_null($this->getPeso()) && $recursive) {
            $produto['peso'] = $this->getPeso()->toArray($recursive);
        } else {
            $produto['peso'] = $this->getPeso();
        }
        $produto['excecao'] = $this->getExcecao();
        $produto['cfop'] = $this->getCFOP();
        $produto['ncm'] = $this->getNCM();
        $produto['cest'] = $this->getCEST();
        if ($recursive) {
            $impostos = [];
            $_impostos = $this->getImpostos();
            foreach ($_impostos as $_imposto) {
                $impostos[] = $_imposto->toArray($recursive);
            }
            $produto['impostos'] = $impostos;
        } else {
            $produto['impostos'] = $this->getImpostos();
        }
        return $produto;
    }

    public function fromArray($produto = [])
    {
        if ($produto instanceof Produto) {
            $produto = $produto->toArray();
        } elseif (!is_array($produto)) {
            return $this;
        }
        parent::fromArray($produto);
        $this->setItem($produto['item'] ?? null);
        $this->setPedido($produto['pedido'] ?? null);
        $this->setCodigo($produto['codigo'] ?? null);
        $this->setCodigoTributario($produto['codigo_tributario'] ?? null);
        $this->setCodigoBarras($produto['codigo_barras'] ?? null);
        $this->setDescricao($produto['descricao'] ?? null);
        if (!isset($produto['unidade'])) {
            $this->setUnidade(self::UNIDADE_UNIDADE);
        } else {
            $this->setUnidade($produto['unidade']);
        }
        if (!isset($produto['multiplicador'])) {
            $this->setMultiplicador(1);
        } else {
            $this->setMultiplicador($produto['multiplicador']);
        }
        $this->setPreco($produto['preco'] ?? null);
        $this->setQuantidade($produto['quantidade'] ?? null);
        $this->setTributada($produto['tributada'] ?? null);
        $this->setPeso(new Peso(isset($produto['peso']) ? $produto['peso'] : []));
        $this->setExcecao($produto['excecao'] ?? null);
        $this->setCFOP($produto['cfop'] ?? null);
        $this->setNCM($produto['ncm'] ?? null);
        $this->setCEST($produto['cest'] ?? null);
        if (!isset($produto['impostos'])) {
            $this->setImpostos([]);
        } else {
            $this->setImpostos($produto['impostos']);
        }
        return $this;
    }

    public static function addNodeInformacoes($tributos, $element, $name = null)
    {
        $detalhes = [];
        $formatos = [
            Imposto::TIPO_IMPORTADO => '%s Importado',
            Imposto::TIPO_NACIONAL => '%s Federal',
            Imposto::TIPO_ESTADUAL => '%s Estadual',
            Imposto::TIPO_MUNICIPAL => '%s Municipal'
        ];
        foreach ($formatos as $tipo => $formato) {
            if (!isset($tributos[$tipo])) {
                continue;
            }
            if (!Util::isGreater($tributos[$tipo], 0.00)) {
                continue;
            }
            $detalhes[] = sprintf($formato, Util::toMoney($tributos[$tipo]));
        }
        if (count($detalhes) == 0) {
            return null;
        }
        $fonte = 'Fonte: ' . $tributos['info']['fonte'] . ' ' . $tributos['info']['chave'];
        $ultimo = '';
        if (count($detalhes) > 1) {
            $ultimo = ' e ' . array_pop($detalhes);
        }
        $texto = 'Trib. aprox.: ' . implode(', ', $detalhes) . $ultimo . '. ' . $fonte;
        Util::appendNode($element, $name ?? 'infAdProd', $texto);
        return $texto;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'det');
        $attr = $dom->createAttribute('nItem');
        $attr->value = $this->getItem(true);
        $element->appendChild($attr);

        $produto = $dom->createElement('prod');
        Util::appendNode($produto, 'cProd', $this->getCodigo(true));
        Util::appendNode($produto, 'cEAN', $this->getCodigoBarras(true));
        Util::appendNode($produto, 'xProd', $this->getDescricao(true));
        Util::appendNode($produto, 'NCM', $this->getNCM(true));
        //      Util::appendNode($produto, 'NVE', $this->getNVE(true));
        if (!is_null($this->getCEST())) {
            Util::appendNode($produto, 'CEST', $this->getCEST(true));
        }
        if (!is_null($this->getExcecao())) {
            Util::appendNode($produto, 'EXTIPI', $this->getExcecao(true));
        }
        Util::appendNode($produto, 'CFOP', $this->getCFOP(true));
        Util::appendNode($produto, 'uCom', $this->getUnidade(true));
        Util::appendNode($produto, 'qCom', $this->getQuantidade(true));
        Util::appendNode($produto, 'vUnCom', $this->getPrecoUnitario(true));
        Util::appendNode($produto, 'vProd', $this->getPreco(true));
        Util::appendNode($produto, 'cEANTrib', $this->getCodigoTributario(true));
        Util::appendNode($produto, 'uTrib', $this->getUnidade(true));
        Util::appendNode($produto, 'qTrib', $this->getTributada(true));
        Util::appendNode($produto, 'vUnTrib', $this->getPrecoTributavel(true));
        if (Util::isGreater($this->getFrete(), 0.00)) {
            Util::appendNode($produto, 'vFrete', $this->getFrete(true));
        }
        if (Util::isGreater($this->getSeguro(), 0.00)) {
            Util::appendNode($produto, 'vSeg', $this->getSeguro(true));
        }
        if (Util::isGreater($this->getDesconto(), 0.00)) {
            Util::appendNode($produto, 'vDesc', $this->getDesconto(true));
        }
        if (Util::isGreater($this->getDespesas(), 0.00)) {
            Util::appendNode($produto, 'vOutro', $this->getDespesas(true));
        }
        Util::appendNode($produto, 'indTot', $this->getMultiplicador(true));
        //      Util::appendNode($produto, 'DI', $this->getImportacoes(true));
        //      Util::appendNode($produto, 'detExport', $this->getDetalhes(true));
        if (!is_null($this->getPedido())) {
            Util::appendNode($produto, 'xPed', $this->getPedido(true));
        }
        Util::appendNode($produto, 'nItemPed', $this->getItem(true));
        //      Util::appendNode($produto, 'nFCI', $this->getControle(true));
        $element->appendChild($produto);

        $imposto = $dom->createElement('imposto');
        $grupos = [];
        $_impostos = $this->getImpostos();
        foreach ($_impostos as $_imposto) {
            if (is_null($_imposto->getBase())) {
                $_imposto->setBase($this->getBase());
            }
            $grupos[$_imposto->getGrupo(true)][] = $_imposto;
        }
        $imposto_info = $this->getImpostoInfo();
        $this->setTributos($imposto_info['total']);
        Util::appendNode($imposto, 'vTotTrib', Util::toCurrency($imposto_info['total']));
        foreach ($grupos as $tag => $_grupo) {
            $grupo = $dom->createElement($tag);
            foreach ($_grupo as $_imposto) {
                $node = $_imposto->getNode($version);
                $node = $dom->importNode($node, true);
                $grupo->appendChild($node);
            }
            $imposto->appendChild($grupo);
        }
        $element->appendChild($imposto);
        // TODO: verificar se é obrigatório a informação adicional abaixo
        $complemento = self::addNodeInformacoes($imposto_info, $element);
        $this->setComplemento($complemento);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'det';
        $element = Util::findNode($element, $name);
        $root = $element;
        $element = parent::loadNode($element, $name);
        $this->setItem(Util::loadNode($element, 'nItemPed'));
        $this->setPedido(Util::loadNode($element, 'xPed'));
        $this->setCodigo(
            Util::loadNode(
                $element,
                'cProd',
                'Tag "cProd" do campo "Codigo" não encontrada no Produto'
            )
        );
        $this->setCodigoTributario(
            Util::loadNode(
                $element,
                'cEANTrib',
                'Tag "cEANTrib" do campo "CodigoTributario" não encontrada no Produto'
            )
        );
        $this->setCodigoBarras(
            Util::loadNode(
                $element,
                'cEAN',
                'Tag "cEAN" do campo "CodigoBarras" não encontrada no Produto'
            )
        );
        $this->setDescricao(
            Util::loadNode(
                $element,
                'xProd',
                'Tag "xProd" do campo "Descricao" não encontrada no Produto'
            )
        );
        $this->setUnidade(
            Util::loadNode(
                $element,
                'uCom',
                'Tag "uCom" do campo "Unidade" não encontrada no Produto'
            )
        );
        $this->setMultiplicador(
            Util::loadNode(
                $element,
                'indTot',
                'Tag "indTot" do campo "Multiplicador" não encontrada no Produto'
            )
        );
        $this->setQuantidade(
            Util::loadNode(
                $element,
                'qCom',
                'Tag "qCom" do campo "Quantidade" não encontrada no Produto'
            )
        );
        $this->setTributada(
            Util::loadNode(
                $element,
                'qTrib',
                'Tag "qTrib" do campo "Tributada" não encontrada no Produto'
            )
        );
        $this->setExcecao(Util::loadNode($element, 'EXTIPI'));
        $this->setCFOP(
            Util::loadNode(
                $element,
                'CFOP',
                'Tag "CFOP" do campo "CFOP" não encontrada no Produto'
            )
        );
        $this->setNCM(
            Util::loadNode(
                $element,
                'NCM',
                'Tag "NCM" do campo "NCM" não encontrada no Produto'
            )
        );
        $this->setCEST(Util::loadNode($element, 'CEST'));
        $impostos = [];
        $_fields = $root->getElementsByTagName('imposto');
        if ($_fields->length == 0) {
            throw new \Exception('Tag "imposto" da lista de "Impostos" não encontrada no Produto', 404);
        }
        $imposto_node = $_fields->item(0);
        $this->setTributos(Util::loadNode($imposto_node, 'vTotTrib'));
        $_items = $imposto_node->childNodes;
        $total = new \DFe\Entity\Imposto\Total();
        foreach ($_items as $_item) {
            if (!$_item->hasChildNodes() || $_item->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $total->setGrupo($_item->nodeName);
            foreach ($_item->childNodes as $_subitem) {
                if ($_subitem->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }
                $imposto = Imposto::loadImposto($_subitem);
                if ($imposto === false) {
                    continue;
                }
                $imposto->setGrupo($total->getGrupo());
                $impostos[] = $imposto;
            }
        }
        $this->setImpostos($impostos);
        $this->setComplemento(Util::loadNode($root, 'infAdProd'));
        return $element;
    }
}
