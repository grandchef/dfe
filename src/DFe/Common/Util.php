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
 * Utilitário para conversões de moeda, datas, verificação de dígitos, etc.
 */
class Util
{
    public const ACCENT_CHARS =
    'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
    public const NORMAL_CHARS =
    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY';

    /**
     * Converte float para string informando a quantidade de
     * casas decimais e usando ponto como separador
     * @param  float   $value  valor para ser convertido
     * @param  integer $places quantidade de casas decimais, padrão 2 casas
     * @return string          valor formatado
     */
    public static function toCurrency($value, $places = 2)
    {
        return number_format($value ?: 0, $places, '.', '');
    }

    /**
     * Converte float para string informando a quantidade de
     * casas decimais e usando ponto como separador
     * @param  float   $value  valor para ser convertido
     * @param  integer $places quantidade de casas decimais, padrão 4 casas
     * @return string          valor formatado
     */
    public static function toFloat($value, $places = 4)
    {
        return number_format($value ?: 0, $places, '.', '');
    }

    /**
     * Converte timestamp para data GMT
     * @param  integer $time data para ser convertida
     * @return string        data no formato GMT
     */
    public static function toDateTime($time)
    {
        return date('Y-m-d\TH:i:sP', $time);
    }

    /**
     * Converte uma cadeira de bytes para hexadecimal
     * @param  string $string cadeira de bytes ou de caracteres
     * @return string         representação em hexadecimal
     */
    public static function toHex($string)
    {
        $hexstr = unpack('H*', $string);
        return array_shift($hexstr);
    }

    /**
     * Adiciona zeros à esquerda para completar o comprimento
     * @param  string  $text  texto ou número a ser adicionados os zeros
     * @param  integer $len  quantidade de caracteres mínimo que deve ter
     * @param  string  $digit permite alterar o caractere a ser concatenado
     * @return string        texto com os zeros à esquerda
     */
    public static function padDigit($text, $len, $digit = '0')
    {
        return str_pad($text, $len, $digit, STR_PAD_LEFT);
    }

    /**
     * Adiciona zeros à direita para completar o comprimento
     * @param string $str texto ou número a ser adicionado os zeros
     * @param integer $len quantidade de caracteres mínimo
     * @param string  $txt caractere a ser adicionado quando não atingir
     * a quantidade len
     * @return string       texto com os zeros à direita
     */
    public static function padText($str, $len, $txt = '0')
    {
        return str_pad($str, $len, $txt, STR_PAD_RIGHT);
    }

    /**
     * Compara se dois valores flutuantes são iguais usando um delta como erro
     * @param  float  $value   valor a ser comparado
     * @param  float  $compare valor a ser comparado
     * @param  float   $delta   margem de erro para igualdade
     * @return boolean          true se for igual ou false caso contrário
     */
    public static function isEqual($value, $compare, $delta = 0.005)
    {
        return $compare < ($value + $delta) && ($value - $delta) < $compare;
    }

    /**
     * Compara se um valor é maior que outro usando um delta como erro
     * @param  float  $value   valor para testar se é maior
     * @param  float  $compare valor com que será comparado
     * @param  float   $delta   margem de erro para informar se é maior
     * @return boolean          true se o valor for maior ou false caso contrário
     */
    public static function isGreater($value, $compare, $delta = 0.005)
    {
        return $value > ($compare + $delta);
    }

    /**
     * Compara se um valor é menor que outro usando um delta como erro
     * @param  float  $value   valor a testar se é menor
     * @param  float  $compare valor com que comparar
     * @param  float   $delta   margem de erro para dizer se é menor
     * @return boolean          true se o valor for menor ou false caso contrário
     */
    public static function isLess($value, $compare, $delta = 0.005)
    {
        return ($value + $delta) < $compare;
    }

    /**
     * Converte um valor para a moeda Real já incluindo o símbolo
     * @param  float $value valor a ser formatado
     * @return string        valor já formatado e com o símbolo
     */
    public static function toMoney($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Realiza uma busca binária num array ordenado usando uma função customizada
     * para comparação
     * @param mixed $elem   elemento a ser procurado
     * @param array $array  array contendo todos os elementos
     * @param callable $cmp_fn função que irá comparar dois elementos
     * @return mixed retorna o valor do array referente a chave ou false caso não encontre
     */
    public static function binarySearch($elem, $array, $cmp_fn)
    {
        $bot = 0;
        $top = count($array) - 1;
        while ($top >= $bot) {
            $p = floor(($top + $bot) / 2);
            $o = $array[$p];
            $r = $cmp_fn($o, $elem);
            if ($r < 0) {
                $bot = $p + 1;
            } elseif ($r > 0) {
                $top = $p - 1;
            } else {
                return $o;
            }
        }
        return false;
    }

    /**
     * Remove acentos e caracteres especiais do texto
     * @param  string $str string com caracteres especiais
     * @return string      texto no formato ANSI sem caracteres especiais
     */
    public static function removeAccent($str)
    {
        return strtr(
            utf8_decode($str),
            utf8_decode(self::ACCENT_CHARS),
            self::NORMAL_CHARS
        );
    }

    /**
     * Cria diretório com permissões
     * @param string $dir caminho da pasta a ser criada
     * @param int $access permissões da pasta
     */
    public static function createDirectory($dir, $access = 0711)
    {
        $oldUmask = umask(0);
        if (!file_exists($dir)) {
            mkdir($dir, $access, true);
        }
        umask($oldUmask);
    }

    /**
     * Retorna o módulo dos dígitos por 11
     * @param string $digitos dígitos para o cálculo
     * @return int            dígito do módulo 11
     */
    public static function getModulo11($digitos)
    {
        $sum = 0;
        $mul = 1;
        $len = strlen($digitos);
        for ($i = $len - 1; $i >= 0; $i--) {
            $mul++;
            $dig = intval($digitos[$i]);
            $sum += $dig * $mul;
            if ($mul == 9) {
                $mul = 1; // reset
            }
        }
        return $sum % 11;
    }

    /**
     * Retorna o módulo dos dígitos por 10
     * @param string $digitos dígitos para o cálculo
     * @return int            dígito do módulo 10
     */
    public static function getModulo10($digitos)
    {
        $sum = 0;
        $mul = 1;
        $len = strlen($digitos);
        for ($i = $len - 1; $i >= 0; $i--) {
            $mul++;
            $dig = intval($digitos[$i]);
            $term = $dig * $mul;
            $sum += ($dig == 9) ? $dig : ($term % 9);
            if ($mul == 2) {
                $mul = 0; // reset
            }
        }
        return $sum % 10;
    }

    /**
     * Retorna o Dígito de Auto-Conferência dos dígitos
     *
     * @param string $digitos
     * @param int $div Número divisor que determinará o resto da divisão
     * @param int $presente Informa o número padrão para substituição do excesso
     * @return int dígito verificador calculado
     */
    public static function getDAC($digitos, $div, $presente = 0)
    {
        $ext = $div % 10;
        if ($div == 10) {
            $ret = self::getModulo10($digitos);
        } else {
            $ret = self::getModulo11($digitos);
        }
        return ($ret <= $ext) ? $presente : ($div - $ret);
    }

    public static function appendNode($element, $name, $text, $before = null)
    {
        $dom = $element->ownerDocument;
        if (is_null($before)) {
            $node = $element->appendChild($dom->createElement($name));
        } else {
            $node = $element->insertBefore($dom->createElement($name), $before);
        }
        $node->appendChild($dom->createTextNode($text ?? ''));
        return $node;
    }

    public static function addAttribute($element, $name, $text)
    {
        $dom = $element->ownerDocument;
        $node = $element->appendChild($dom->createAttribute($name));
        $node->appendChild($dom->createTextNode($text));
        return $node;
    }

    public static function loadNode($element, $name, $exception = null)
    {
        $value = null;
        if (is_null($element)) {
            return $value;
        }
        $list = $element->getElementsByTagName($name);
        if ($list->length > 0) {
            $value = $list->item(0)->nodeValue;
        } elseif (!is_null($exception)) {
            throw new \Exception($exception, 404);
        }
        return $value;
    }

    public static function nodeExists($element, $name)
    {
        $list = $element->getElementsByTagName($name);
        return ($list->length > 0) || ($element->nodeName == $name);
    }

    public static function getNode(\DOMElement $element, string $name): ?\DOMElement
    {
        if ($element->nodeName == $name) {
            return $element;
        }
        $list = $element->getElementsByTagName($name);
        if ($list->length == 0) {
            return null;
        }
        return $list->item(0);
    }

    public static function findNode(\DOMElement $element, string $name, ?string $exception = null): \DOMElement
    {
        $result = self::getNode($element, $name);
        if (is_null($result)) {
            if (is_null($exception)) {
                $exception = 'Tag "' . $name . '" não encontrada no bloco "' . $element->nodeName . '"';
            }
            throw new \Exception($exception, 404);
        }
        return $result;
    }

    public static function mergeNodes($element, $other)
    {
        $dom = $element->ownerDocument;
        foreach ($other->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $element->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                $element->replaceChild($node, $list->item(0));
            } else {
                $element->appendChild($node);
            }
        }
        return $element;
    }
}
