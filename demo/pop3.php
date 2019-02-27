<?php
require('../vendor/autoload.php');

use MailServer\Pop3\Server as Pop3Server;

$server = new Pop3Server;
$server->run();
