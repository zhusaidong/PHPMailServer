<?php
/**
* MailServer
*/
namespace MailServer\Smtp;

use Workerman\Worker;
use MailServer\Smtp\IO\Output;

class Server
{
	public function run()
	{
		$worker = new Worker('smtp://0.0.0.0:25');
		Worker::$logFile = 'MailServerWorker.log';
		$worker->count = 1;
		$worker->name = 'Smtp Server';
		
		$worker->onConnect = function($connection)
		{
			//var_dump('SmtpServer onConnect');
			$connection->send(new Output(220,'Welcome to Smtp Server on PHPMailServer.'));
		};
		$worker->onMessage = function($connection,$msg)
		{
			if(isset($connection->startData) and $connection->startData)
			{
				if($msg != '.')
				{
					$connection->user['data'] .= $msg;
				}
				else
				{
					$connection->startData = FALSE;
					
					$tomail = str_replace([' ','<','>'],'',$connection->user['to']);
					list($user,$host) = explode('@',$tomail);
					
					//不是自已域名,转发
					//var_dump(gethostbyname($host),gethostbyaddr($_SERVER['SERVER_ADDR']));
					if($host != '0.0.1')
					{
						getmxrr($host,$mxhosts,$weight);
						$mx = $mxhosts[array_search(max($weight),$weight)];
						$smtpServerIp = gethostbyname($mx);
					}
					
					$data = $connection->user['data'];
					
					$mailUid = md5($connection->user['id'].time());
					$connection->user['data'] = $mailUid;
					
					file_put_contents('../data/'.$mailUid.'.txt',$data);
					
					$connection->user['size'] = filesize('../data/'.$mailUid.'.txt');
					
					if(!file_exists('../data/mailLists.json'))
					{
						$mailLists = [];
					}
					else
					{
						$mailLists = json_decode(file_get_contents('../data/mailLists.json'),TRUE);
					}
					
					$mailLists[] = $connection->user;
					file_put_contents('../data/mailLists.json',json_encode($mailLists,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
					unset($mailLists);
				}
				
				$connection->send(new Output(250,'OK'));
			}
			else
			{
				//var_dump('SmtpServer onMessage',$msg);
				//file_put_contents('SmtpServer_onMessage.log','SmtpServer_onMessage=>'.$msg.PHP_EOL.PHP_EOL,FILE_APPEND);
				set_error_handler(function(){});
				@list($cmd,$arg) = explode(' ',$msg,2);
				restore_error_handler();
				$cmd = strtolower($cmd);
				
				switch($cmd)
				{
					case 'ehlo'://邮件发送会话开始
						$connection->user = [
							'id'=>$arg,
						];
						
						$connection->send(new Output(250,'OK'));
						break;
					case 'mail'://通过标识邮件的发件人来标识邮件传输开始
						$args = explode(':',$arg);
						$connection->user['from'] = isset($args[1])?trim($args[1]):'';
						
						$connection->send(new Output(250,'OK'));
						break;
					case 'rcpt'://标识邮件的收件人
						$args = explode(':',$arg);
						$connection->user['to'] = isset($args[1])?trim($args[1]):'';
						
						$connection->send(new Output(250,'OK'));
						break;
					case 'data'://开始传输邮件内容
						$connection->user['data'] = '';
						$connection->startData = TRUE;
						
						$connection->send(new Output(354,'OK'));
						break;
					case 'quit'://会话结束
						$connection->send(new Output(250,'OK'));
						break;
				}
			}
		};
		$worker->onClose = function($connection)
		{
			//var_dump('SmtpServer onClose');
		};
		
		Worker::runAll();
	}
}
