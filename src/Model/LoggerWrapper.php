<?php

namespace Sheetpost\Model;

use DateTimeZone;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerWrapper extends Logger
{
    private string $logsPath;
    private StreamHandler $streamHandler;

    public function __construct(string        $name,
                                array         $handlers = [],
                                array         $processors = [],
                                ?DateTimeZone $timezone = null)
    {
        parent::__construct($name, $handlers, $processors, $timezone);
        $this->logsPath = join(DIRECTORY_SEPARATOR, [dirname(__DIR__, 2), 'var', 'log', 'app.log']);
        $this->streamHandler = new StreamHandler($this->logsPath);
        $this->setHandler();
        $this->setFormat();
    }

    private function setHandler(): void
    {
        $this->pushHandler($this->streamHandler);
        ErrorHandler::register($this);
    }

    private function setFormat(): void
    {
        $output = "[%datetime%] %level_name%: %message%" . PHP_EOL;
        $dateFormat = "d.m.Y H:i:s";
        $formatter = new LineFormatter($output, $dateFormat);
        $this->streamHandler->setFormatter($formatter);
    }
}