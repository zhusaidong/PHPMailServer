<?php
require('../vendor/autoload.php');

use MailServer\Imap\Server as ImapServer;

$server = new ImapServer;
$server->run();
