<?php
/**
 * Created by PhpStorm.
 * User: zhu
 * Date: 2018/12/23
 * Time: 12:12
 */

require('../vendor/autoload.php');

$user = new MailServer\Model\User();
$userInfo = $user->find();
print_r($userInfo);
