<?php
/**
* 修改密码
* @author BEE 2012-08-22
*/

define('IN_DAEM', true);
require_once './includes/init.php';

if (!empty($_POST['submit']))
{
	//判断是否来自本站
	chkfrompath();
	
	$password = $_POST['password'];
	$pwd = $_POST['pwd'];
	$newpwd = $_POST['newpwd'];
	
	if ($password == '' || $pwd == '' || $newpwd == '') {
		$db->close();
		gourl('缺少关键参数。','',-1);
	}
	if ($pwd != $newpwd) {
		$db->close();
		gourl('两次密码输入不一致。','',-1);
	}
	$sql = "select a_id from ".DB_DAEMDB.".db_adminuser where a_id='".$_SESSION['UserId']."' and a_password='".md5($password)."'";
	if (!$db->query_first($sql)) {
		$db->close();
		gourl('对不起，原始密码填写错误。','',-1);
	}
	$sql = "update ".DB_DAEMDB.".db_adminuser set a_password='".md5($newpwd)."' where a_id='".$_SESSION['UserId']."'";
	$db->query($sql);
	gourl('密码修改成功，下次登录时请使用新密码登陆。','right.php');
}

include template('','password');
