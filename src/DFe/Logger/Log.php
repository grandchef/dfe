<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Logger;

use Psr\Log\LoggerInterface;

/**
 * Salva mensagens de erro, depuração entre outros
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 */
class Log
{
    /**
     * Pasta onde será salvo os arquivos de log
     *
     * @var string
     */
    private $directory;

    /**
     * Processador de log
     *
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Instância salva para uso estático
     *
     * @var Log
     */
    private static $instance;

    /**
     * Cria uma instância de Log
     * @param array $log campos para preencher o log
     */
    public function __construct($log = [])
    {
        $this->logger = new \Monolog\Logger('NFeAPI');
        $this->fromArray($log);
    }

    /**
     * Get current or create a new instance
     * @return Log current instance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Pasta onde serão salvos os arquivos de Log
     * @return string diretório atual onde os logs são salvos
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Informa a nova pasta onde os logs serão salvos
     * @param string $directory caminho absoluto da pasta
     * @return self a própria instância de Log
     */
    public function setDirectory($directory)
    {
        if ($this->directory == $directory) {
            return $this;
        }
        $this->directory = $directory;
        $this->setHandler(null);
        return $this;
    }

    /**
     * Altera o gerenciador que escreve os logs, informe null para restaurar o padrão
     *
     * @param \Monolog\Handler\AbstractHandler|null $handler nova função que será usada
     *
     * @return self a própria instância de Log
     */
    public function setHandler($handler)
    {
        if ($handler === null) {
            $handler = new \Monolog\Handler\RotatingFileHandler($this->getDirectory() . '/{date}.txt');
            $handler->setFilenameFormat('{date}', 'Ymd');
        }
        $this->logger->pushHandler($handler);
        return $this;
    }
    /**
     * Altera a instância escritora dos logs
     *
     * @param LoggerInterface $logger
     *
     * @return self a própria instância de Log
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     *
     * @param bool $recursive
     *
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $log = [];
        $log['directory'] = $this->getDirectory();
        return $log;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param array|Log $log Array ou instância de Log, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($log = [])
    {
        if ($log instanceof Log) {
            $log = $log->toArray();
        } elseif (!is_array($log)) {
            return $this;
        }
        if (!isset($log['directory'])) {
            $this->setDirectory(dirname(dirname(dirname(__DIR__))) . '/storage/logs');
        } else {
            $this->setDirectory($log['directory']);
        }
        return $this;
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function error($message, $context = [])
    {
        self::getInstance()->logger->error($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function warning($message, $context = [])
    {
        self::getInstance()->logger->warning($message, $context);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function debug($message, $context = [])
    {
        self::getInstance()->logger->debug($message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     *
     * @return void
     */
    public static function info($message, $context = [])
    {
        self::getInstance()->logger->info($message, $context);
    }
}
