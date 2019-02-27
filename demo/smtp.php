<?php
require('../vendor/autoload.php');

use MailServer\Smtp\Server as SmtpServer;

$server = new SmtpServer;
$server->run();
