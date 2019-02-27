<?php
/**
* MailServer
*/
namespace MailServer\Smtp\IO;

use Workerman\Worker;

class Output
{
	private $code = 0;
	private $msg = '';
	
	public function __construct($code = 0,$msg = '')
	{
		$this->code = $code;
		$this->msg = $msg;
	}
	public function __toString()
	{
		return $this->code.' '.$this->msg;
	}
}
