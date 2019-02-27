<?php
/**
 * Created by PhpStorm.
 * User: zhu
 * Date: 2018/12/23
 * Time: 11:58
 */
namespace MailServer\Model;

class User extends \SQLite3
{
	public function __construct()
	{
		$this->open('../mail.db',SQLITE3_OPEN_READWRITE);
	}
	public function _query($sql)
	{
		$result = $this->query($sql);
		$data = [];
		while($row = $result->fetchArray(SQLITE3_ASSOC))
		{
			$data[] = $row;
		}
		return $data;
	}
	
	public function select($row = '*',$where = [])
	{
		$wheres = [];
		foreach($where as $key => $item)
		{
			$wheres[] = $key.'='.$item;
		}
		$whereStr = empty($wheres) ? '' : 'where '.implode(' and ', $wheres);
		return $this->_query("select {$row} from mail_user {$whereStr};");
	}
	public function find($row = '*',$where = [])
	{
		$wheres = [];
		foreach($where as $key => $item)
		{
			$wheres[] = $key.'='.$item;
		}
		$whereStr = empty($wheres) ? '' : 'where '.implode(' and ', $wheres);
		return $this->querySingle("select {$row} from mail_user {$whereStr};", true);
	}
	public function insert($table,$insert = [])
	{
	
	}
	public function delete($table,$where = [])
	{
	
	}
	public function update($table,$update = [],$where = [])
	{
	
	}
	
	public function close()
	{
		$this->close();
	}
}

