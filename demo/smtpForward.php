<?php
/**
* 
* @author Zsdroid [635925926@qq.com]
*/
$to = '635925926@126.com';
$tomail = str_replace([' ','<','>'],'',$to);
list($user,$host) = explode('@',$tomail);

getmxrr($host,$mxhosts,$weight);
$mx = $mxhosts[array_search(max($weight),$weight)];
var_dump($mx);
var_dump(gethostbyname($mx));
