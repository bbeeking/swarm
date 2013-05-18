<?php
/**
 * DAEMON管理系统主程序入口
 * @author BEE 2012-08-21
 */

define('IN_DAEM', true);

require(dirname(__FILE__) . '/includes/init.php');

//浏览器访问存放于session中
$_SESSION['is_browser'] = 1;

//测试环境下更新session
$_SESSION[menu] = '';
$_SESSION[menuChoose][perMod] = '';
$_SESSION[menuChoose][mod] = '';


//获取用户的id，角色id以及可访问的模块(菜单)
$userId = $_SESSION['UserId'];
if (empty($_SESSION[menu]))
{
	$roleId = $rbac->getUserRole($userId);
	$_SESSION[menu] =  $rbac->getRoleMod($roleId);
}

//sidebar选择控制
if (!empty($_GET['Mod']))
{
	$ModAry = explode('_', $_GET['Mod']);
	$perMod = $ModAry[0];
	$mod = $ModAry[1];
	
	//同时更新用户的session
	$_SESSION[menuChoose][perMod] = $perMod;
	$_SESSION[menuChoose][mod] = $mod;
	
	$mainPage = dirname(__FILE__).'/'.$perMod.'/'.$mod.'.php';
	
	//如果该主内容页不存在，则进行相应的处理
	if (!file_exists($mainPage))
	{
		//加载404错误页 	@todo 或者是页面重定向到404页
//		$mainPage = dirname(__FILE__).'/templates/error_404.html.php';
		gourl('', './templates/error_404.html.php');
	}
	
}


include template('','index');

