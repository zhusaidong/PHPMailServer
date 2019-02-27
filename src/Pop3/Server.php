<?php
/**
* Pop3 Server
*/
namespace MailServer\Pop3;

use Workerman\Worker;
use MailServer\Pop3\IO\Output;

class Server
{
	public function run()
	{
		$worker = new Worker('pop3://0.0.0.0:110');
		Worker::$logFile = 'MailServerWorker.log';
		$worker->count = 1;
		$worker->name = 'Pop3 Server';
		
		$worker->onConnect = function($connection)
		{
			//var_dump('Pop3Server onConnect');
			$connection->send(new Output(Output::CODE_OK,'Welcome to Pop3 Server on PHPMailServer.'));
		};
		$worker->onMessage = function($connection,$msg)
		{
			//var_dump('Pop3Server onMessage',$msg);
			set_error_handler(function(){});
			@list($cmd,$arg) = explode(' ',$msg,2);
			restore_error_handler();
			$cmd = strtolower($cmd);
			
			switch($cmd)
			{
				case 'user':
					$connection->user = [
						'user'=>$arg,
					];
					
					//找出属于登录者的邮件，而不是所有邮件
					if(!file_exists('../data/mailLists.json'))
					{
						$mailLists = [];
					}
					else
					{
						$mailLists = json_decode(file_get_contents('../data/mailLists.json'),TRUE);
					}
					$lists = [];
					foreach($mailLists as $mail)
					{
						if(strpos($mail['to'],'<'.$arg.'@') !== FALSE)
						{
							$lists[] = $mail;
						}
					}
					$connection->user['mailLists'] = $lists;
					
					$connection->send(new Output(Output::CODE_OK,'pop3 server is ready.'));
					break;
				case 'pass':
					$connection->send(new Output(Output::CODE_OK,'0 message [0 byte]'));
					break;
				case 'stat':
					$allMails = count($connection->user['mailLists']);
					$allMailBits = array_sum(array_column($connection->user['mailLists'],'size'));
					
					$connection->send(new Output(Output::CODE_OK,$allMails.' '.$allMailBits));
					break;
				case 'list':
					$lists = array_column($connection->user['mailLists'],'size');
					if(empty($arg))
					{
						$msg = count($lists).' '.array_sum($lists).PHP_EOL;//总邮件
						foreach($lists as $index => $mail)
						{
							$msg .= ($index + 1).' '.$mail.PHP_EOL;
						}
						$connection->send(new Output(Output::CODE_OK,$msg));
					}
					else
					{
						if(!isset($lists[$arg - 1]))
						{
							$connection->send(new Output(Output::CODE_ERR,'no such message'));
						}
						else
						{
							$connection->send(Output::CODE_OK,$arg.' '.$lists[$arg]);
						}
					}
					break;
				case 'uidl'://返回对应邮件的唯一号，邮件客户端根据这个唯一号判断已读,未读,新邮件
					$uids = array_column($connection->user['mailLists'],'data');
					if(empty($arg))
					{
						$msg = PHP_EOL;
						foreach($uids as $index => $mailUid)
						{
							$msg .= ($index + 1).' '.$mailUid.PHP_EOL;
						}
						$connection->send(new Output(Output::CODE_OK,$msg));
					}
					else
					{
						if(!isset($uids[$arg - 1]))
						{
							$connection->send(new Output(Output::CODE_ERR,'no such message'));
						}
						else
						{
							$connection->send(new Output(Output::CODE_OK,$arg.' '.$uids[$arg]));
						}
					}
					break;
				case 'retr':
					$uids = array_column($connection->user['mailLists'],'data');
					
					$uid = $uids[$arg - 1];
					$email = file_get_contents('../data/'.$uid.'.txt');
					
					$connection->send(new Output(Output::CODE_OK,$email));
					break;
				case 'quit':
					$connection->send(new Output(Output::CODE_OK));
					break;
			}
		};
		$worker->onClose = function($connection)
		{
			//var_dump('Pop3Server onClose');
		};
		Worker::runAll();
	}
}
