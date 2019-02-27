<?php
/**
* MailServer
*/
namespace MailServer\Imap\IO;

use Workerman\Worker;

class Output
{
	const CODE_OK = 'OK';
	const CODE_ERR = 'BAD';
	const CODE_NO = 'NO';
	const CODE_EMPTY = '';
	
	private $code = '';
	private $msg = '';
	private $client = '';
	
	public function __construct($client = '*',$code = self::OK,$msg = '')
	{
		$this->client = $client;
		$this->code = $code;
		$this->msg = $msg;
	}
	public function __toString()
	{
		return $this->client. ' ' . $this->code .' '. $this->msg;
	}
}
