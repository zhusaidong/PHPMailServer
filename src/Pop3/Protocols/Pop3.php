<?php
/**
* Pop3 Protocol
* @author Zsdroid [635925926@qq.com]
*/
namespace Protocols;

use Workerman\Protocols\ProtocolInterface;
use Workerman\Connection\ConnectionInterface;

class Pop3 implements ProtocolInterface
{
    public static function input($buffer, ConnectionInterface $connection)
    {
        return strlen($buffer);
    }
    public static function encode($buffer, ConnectionInterface $connection)
    {
        return $buffer.PHP_EOL;
    }
    public static function decode($buffer, ConnectionInterface $connection)
    {
        return trim($buffer);
    }
}
