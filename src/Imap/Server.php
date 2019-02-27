<?php
/**
* MailServer
*/
namespace MailServer\Imap;

use MailServer\Imap\IO\Output;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

class Server
{
	public function run()
	{
		$worker = new Worker('imap://0.0.0.0:143');
		Worker::$logFile = 'MailServerWorker.log';
		$worker->count = 1;
		$worker->name = 'Imap Server';
		
		$worker->onConnect = function(ConnectionInterface $connection)
		{
			$connection->send(new Output('',Output::CODE_OK,'Welcome to Imap Server on PHPMailServer.'));
		};
		$worker->onMessage = function(ConnectionInterface $connection,$msg)
		{
			var_dump('onMessage',$msg);
			
			set_error_handler(function(){});
			@list($client,$cmd,$info) = explode(' ',$msg,3);
			restore_error_handler();
			
			$cmd = strtolower($cmd);
			switch($cmd)
			{
				case 'capability':
					$connection->send(new Output('*',Output::CODE_EMPTY,'CAPABILITY IMAP4rev1 STARTTLS AUTH=GSSAPI'));
					$connection->send(new Output($client,Output::CODE_OK,'CAPABILITY completed.'));
					break;
				case 'noop':
					$connection->send(new Output($client,Output::CODE_OK,'NOOP completed.'));
					break;
				case 'logout':
					$connection->send(new Output($client,Output::CODE_EMPTY,'BYE IMAP4rev1 Server logging out'));
					$connection->send(new Output($client,Output::CODE_OK,'LOGOUT completed.'));
					break;
				case 'login':
					$infos = explode(' ', $info);
					//$username = $infos[0];
					$password = trim($infos[1], '"');
					if($password == 'imap')
					{
						$connection->send(new Output($client,Output::CODE_OK,'LOGIN completed.'));
					}
					else
					{
						$connection->send(new Output($client,Output::CODE_NO,'LOGIN error.'));
					}
					break;
				case 'list':
					
					break;
				case 'select':
					
					break;
			}
		};
		$worker->onClose = function(ConnectionInterface $connection)
		{
			var_dump('onClose');
			$connection->send('');
		};
		
		Worker::runAll();
	}
}
