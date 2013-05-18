<?php
/**
 * 用户登录判断处理
 * @author BEE 2012-08-22
 * @return 		
 * 			0:登录成功
 * 			1:帐号不存在 
 * 			2：帐号密码错误 
 * 			3：帐号已被禁止
 * 			4:验证码错误
 */

require_once("includes/global.php");

require_once("includes/db_mysql.php");

//实例化数据库对象
$db = new db();
$db->connect($slaveDb);

//获取验证码
//$code = $_POST['code'];
	
//获取输入的用户名
$username = trim($_POST['username']);
//获取输入的用户密码并MD5加密
$password = $_POST['password'];

//验证验证码
/*if(strtolower($_SESSION['code']) !== strtolower($code))
{
	exit("4");//验证码错误
}*/

//如果账号已经锁定，直接返回状态3

//`try_error_num` varchar(255) NOT NULL DEFAULT '2013-01-01,0' COMMENT '最后登陆日期，连续输入错误次数',
$isLock = array();
$isLock = $db->query_first("select uid,is_permit,try_error_num from ".DB_DAEMDB.".per_user where username='".$username."'");
$errTryAry = explode(',',$isLock['try_error_num']);
if( $isLock['is_permit'] == 1 )
{
	echo "3";
	$db->close();
	exit;
}

//进行数据查询  并判断输入的用户名和密码是否正确
$sql = "select uid,is_permit,log_time,log_ip,username,language from ".DB_DAEMDB.".per_user where username='".$username."' and password='".$password."'";
if($row = $db->query_first($sql))
{
	//用户名存入COOKIE
	setcookie("adminaccount",$username,time()+60*60*24*365);
	setcookie("adminlogininfo",$row["log_time"].",".$row["log_ip"].",".$username,time()+60*60*24);
	
	//把用户名所属ID存入
	$_SESSION['UserId'] = $row['uid'];
	$_SESSION['UserName'] = $row['username'];
	$_SESSION['LanguageType']	= $row['language'];
	$sql = "update ".DB_DAEMDB.".per_user set log_time=".time().",log_ip='".$_SERVER['REMOTE_ADDR']."' where uid=".$row['uid'];
	$db->query($sql);
	echo "1";
}
else
{
	//当天连续尝试错误密码次数++
	if( $isLock['uid'] > 0 )
	{
		$errTimes = ($errTryAry[0] == date('Y-m-d')) ? (int)$errTryAry[1]+1 : 1;
		$setstr = "set try_error_num = '".date('Y-m-d').",".$errTimes."'";
		if($errTimes >= 5)
		{
			$setstr .= ", is_permit='1' ";
		}
		$sql = "update per_user ".$setstr." where uid = ".(int)$isLock['uid'];
		$db->query($sql);
		
		$diff = (5 - (int)$errTryAry[1] - 1);
		$accountMsg = array(0 => L('五次错误会导致锁号').'</br>'.L('今天剩余').$diff.L('次'));
		
		header("Content-type: text/html; charset=utf-8");
		echo json_encode($accountMsg);
	}else{
		echo "2";//帐号不存在	
	}
}
if(!empty($_SERVER['HTTP_HOST']))
{
//	$db->user_log_record($db);
}
$db->close();
