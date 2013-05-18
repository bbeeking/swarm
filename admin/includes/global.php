<?php

//错误屏蔽设置
//error_reporting(0);//上线后的配置
error_reporting(E_ALL ^E_NOTICE ^E_WARNING);;//测试阶段的配置
set_time_limit(60);

//定义输出编码
header("Content-type: text/html; charset=utf-8");

session_name("daemon");
session_start();

$TEMPROOT = dirname(dirname(dirname(__FILE__)));
define('SITE_DIR',basename($TEMPROOT)); //网站存放目录
define('ROOT_PATH',$TEMPROOT.'/admin/');//定义网站admin目录，带磁盘符


//分页时,每页展示数量
define('DAEM_PER_PAGE_NUM', 14);

//定义生产data数据存储路径
define('DAEM_DATA_ROOT', dirname(ROOT_PATH).'/data/');

//定义配置数据存储路径
define('DAEM_CONFIG_ROOT', dirname(ROOT_PATH).'/config/');

//php对外接口等
define('DAEM_API_ROOT', dirname(ROOT_PATH).'/api/');


//加载全局语言文件
include ROOT_PATH.'language/'.($_SESSION['LanguageType'] ? $_SESSION['LanguageType'] : 'ch').'/menu.lang.php';
include ROOT_PATH.'language/'.($_SESSION['LanguageType'] ? $_SESSION['LanguageType'] : 'ch').'/global.lang.php';

//网站名称定义
define('IN_DAEM', TRUE);
define('WEBTITLE','DAEMON管理系统');
define("DAEM_LIST_PER_PAGE_RECORDS",2);
define('DAEM_DOMAIN',isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']));
define('DAEM_PATH','http://'.DAEM_DOMAIN.'/'.SITE_DIR.'/');
//获取当前调用文件的相对路径
define('DAEM_SELF',isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']));
define('DAEM_TIME',isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
define('DAEM_REFERER',isset($_SERVER['DI_REFERER']) ? $_SERVER['DI_REFERER'] : '');
define('DAEM_CACHE_ROOT',ROOT_PATH.'cache/');
define('DAEM_TEMPLATE_CACHE_ROOT',DAEM_DATA_ROOT.'cache/template/');
define('TEMPLATE_FRESH',1); //是否更新模板缓存，1：更新，0：不更新

define("ISTEST",0);//测试环境定义，1 为测试环境，0 为非测试环境
define('LANG_ARR_NAME', '_LANG'); // 定义语言数组变量名称

define("GLOBAL_DEBUG_LEVEL",0);//是否要生成SQL日志:0-不生成，1-生成。

//定义中央后台和单服公用的密钥
define('DAEM_CENTER_KEY', 'helloworld!@#$');
define('DAEM_SCREEN_KEY', md5(DAEM_CENTER_KEY.DAEM_TIME));

//定义生成数据文件目录@todo need fix
define('DAEM_HOME_DATA', '/home/admin_game/data/');

//定义数据库连接
include DAEM_CONFIG_ROOT."dbConfig.php";

//加载权限控制类
include_once ROOT_PATH.'includes/class/rbac.class.php';

//加载公共方法
include 'function.php';

//加载枚举文件
include DAEM_CONFIG_ROOT.'enum.php';

//加载模版定义文件
include 'define_template.php';

//获取当前调用文件的所属模块,并加载该模块所需语言文件
$Mod = basename(dirname($_SERVER['PHP_SELF']));
if(empty($Mod))
{
	gourl(L("语言文件自动加载失败").','.L("请联系管理员"),'',-1);
}
else
{
	//如果该文件直属于admin文件夹下，不属于任何模块则值只加载公共语言文件，否则加载相应的模块语言包
	if(SITE_DIR!==$Mod)
	{
		$mod_lang_path = ROOT_PATH.'language/'.($_SESSION['LanguageType'] ? $_SESSION['LanguageType'] : 'cn').'/'.$Mod.'.lang.php';
		include $mod_lang_path;
	}
}

//屏蔽sql注入
if ($_GET)
{
	$_GET = strip_sql($_GET);
	$_GET = strip_html($_GET);
}
if ($_POST)
{
	$_POST = strip_sql($_POST);
	$_POST= strip_html($_POST);
}