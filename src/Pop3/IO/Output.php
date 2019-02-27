<?php
/**
* MailServer
*/
namespace MailServer\Pop3\IO;

use Workerman\Worker;

class Output
{
	const CODE_OK = '+OK';
	const CODE_ERR = '-ERR';
	
	private $code = '';
	private $msg = '';
	
	public function __construct($code = self::OK,$msg = '')
	{
		$this->code = $code;
		$this->msg = $msg;
	}
	public function __toString()
	{
		//如果是多行消息，则要新增一行内容为`.`的结束行
		if(strpos($this->msg,PHP_EOL) !== FALSE)
		{
			$this->msg = rtrim($this->msg,PHP_EOL).PHP_EOL.'.';
		}
		return $this->code.' '.$this->msg;
	}
}
