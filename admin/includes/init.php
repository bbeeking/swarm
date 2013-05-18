<?php
/*
 * 系统配置加载文件
 * @author BEE 2012-08-21
 * 
 */

if (!defined('IN_DAEM'))
{
    die('Hacking attempt');
}

//设置时区
date_default_timezone_set("Asia/Shanghai");
	
define('START_TIME', microtime(true));//获取开启时间
define('START_MEMORY', memory_get_usage());//获取开启时内存

require_once("db_mysql.php");

//全局常量定义
require_once("global.php");

//判断用户是否已经登录@todo need complete
if($_SESSION['UserId']=="") 
{
	gourl("","/".SITE_DIR."/admin/login.php",'',"top");
}

$webtitle = WEBTITLE;

//template模板引擎
require_once('template.func.php');


//创建数据库连接对象
$db = new db();
$db->connect($slaveDb);
if(!empty($_SERVER['HTTP_HOST']))
{
//	$db->user_log_record($db);
}

//创建权限控制对象
$userId = $_SESSION['UserId'];
$rbac = new RBAC($userId, $db);