<?php
/**
* Imap Protocol
* @author Zsdroid [635925926@qq.com]
*/
namespace Protocols;

use Workerman\Protocols\ProtocolInterface;
use Workerman\Connection\ConnectionInterface;

class Imap implements ProtocolInterface
{
    public static function input($buffer, ConnectionInterface $connection)
    {
        return strlen($buffer);
    }
    public static function encode($buffer, ConnectionInterface $connection)
    {
        return $buffer . "\n";
    }
    public static function decode($buffer, ConnectionInterface $connection)
    {
        return trim($buffer);
    }
}
