<?php
/**
* 退出系统
* @author BEE 2012-08-22
*/

define('IN_DAEM', TRUE);
require_once './includes/init.php';

$_SESSION['UserId'] = '';
$_SESSION['UserName'] = '';
$_SESSION['UserGroup']='';
$db->close();
gourl("","login.php",'',"top");
