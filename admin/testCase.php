<?php
/**
 * RBAC 权限控制类 测试用例
 * @author BEE LEUNG 
 * @version 2013-01-21
 */


error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");

//数据库连接信息
$masterDbHost = '127.0.0.1';
$masterDb['host']   = $masterDbHost.':3306';
$masterDb['name']   = 'root';
$masterDb['pass']   = '';
$masterDb['dbname'] = 'daemon';
$masterDb['pconnect'] = '0';

include_once './includes/db_mysql.php';
$db = new db();
$db->connect($masterDb);

//实例化引用rbac类
include_once './includes/class/rbac.class.php';
$userId = '1';
$rbac = new RBAC($userId, $db);

/**
 * 打印测试结果函数,根据返回的结果类型输出结果
 * @param unknown_type $result
 */
function testCaseResult($result)
{
//	echo "测试函数：rbacRoleAdd()</br>返回:</br>";
	if (is_bool($result))
	{
		if ($result == TRUE){echo "<font color='green'>Success</font></br>";}
		else echo "<font color='Red'>False</font></br>";
	}
	elseif (is_string($result))
	{
		echo $result."</br>";
	}
	elseif (is_array($result))
	{
		print_r('<pre>');
		print_r($result);
		echo "</br>";
	}
	elseif (is_numeric($result))
	{
		echo $result."</br>";
	}
	else 
	{
		//出错了，请返回检查你的代码
		echo "<font color='Red'>"."ERROR PLEASE CHECK YOUR CODE</font></br>";
	}
	echo "------------------------</br>";
}














/**
 * 根据用户id获取用户对应的角色组id
 */

echo "测试函数：getUserRole()</br>返回:</br>";
testCaseResult($rbac->getUserRole('1')); 




/**
 * 测试角色权限控制下获得访问的模块mod 的 show(中文展示) 同 check(英文定义
 * show用于展示中文名称
 * check用于英文定义访问
 */

echo "测试函数：getRoleMod()</br>返回:</br>";
testCaseResult($rbac::getRoleMod('101')); 


/**
 * 模拟角色访问某一模块时 获得该模块下各个的元素对应的权限
 * @param roleId 角色id
 * @param mod 模块(URL传递)
 * @return array 对应的各个页面元素的授权码
 * 
 * @example Array ( [table] => 53 [from] => 22 [title] => 18 [button] => 9 ) 
 */

echo "测试函数：getRoleModElement()</br>返回:</br>";
testCaseResult($rbac::getRoleModElement('101', 'stat/onlineNum'));


/**
 * 计算当前受限资源操作授权权限最大值
 */

echo "测试函数：countPermMaximum()</br>返回:</br>";
testCaseResult($rbac::countPermMaximum());


/**
 * 在获得的授权码中 检查某一操作  是否合法
 * @param int $authCode 授权码
 * @param string $oper 操作
 * @return array 对应的各个页面元素的授权码
 * 
 * 授权码 10 = 8(update) + 2(search)
 */

echo "测试函数：checkAuthorization()</br>返回:</br>";
testCaseResult($rbac::checkAuthorization('10', 'update'));


/**
 * 获取用户信息
 * 用户名为空：获取所有用户信息
 * 用户名非空：获取指定用户信息
 * 
 * @param String $username
 * @return Array $userInfo
 */


$username = 'bbeeking';
echo "测试函数：rbacGetUserInfo()</br>返回:</br>";
testCaseResult($rbac->rbacGetUserInfo($username));


/**
 * 模拟添加管理员账号
 * 传递用户信息数组
 * 
 * @return Array $userInfo
 */

$userInfo = array('username'=>'minibee','password'=>'123123');

echo "测试函数：rbacUserAdd()</br>返回:</br>";
testCaseResult($rbac->rbacUserAdd($userInfo));



/**
 * 模拟修改管理员账号信息
 * 传递用户信息数组
 * 
 * @return boolean
 */

$userInfo = array('username'=>'minibee222','password'=>'123123');
echo "测试函数：rbacUserUpdate()</br>返回:</br>";
testCaseResult($rbac->rbacUserUpdate('minibee',$userInfo));


/**
 * 模拟删除管理员账号
 * 传递用户信息数组
 * 
 * @return boolean
 */

$username = 'minibee222';
echo "测试函数：rbacUserDel()</br>返回:</br>";
testCaseResult($rbac->rbacUserDel($username));



/**
 * 查询已有角色
 * 1：测试角色名为空 $roleName = ''
 * 2：测试角色名为空 $roleName != ''
 * 
 * @return Array $roleInfo
 */

$roleName = '';
echo "测试函数：rbacGetRoleInfo()</br>返回:</br>";
testCaseResult($rbac->rbacGetRoleInfo($roleName));


/**
 * 修改角色
 * 传递角色名同新信息数组
 * @return Array $roleInfo
 */

$newInfo = array('role_name'=>'testrole_update');
echo "测试函数：rbacRoleUpdate()</br>返回:</br>";
testCaseResult($rbac->rbacRoleUpdate('testrole',$newInfo));



/**
 * 添加角色
 * 传递角色信息数组
 * @return Array $roleInfo
 */

$roleInfo = array('role_name'=>'testrole');
echo "测试函数：rbacRoleUpdate()</br>返回:</br>";
testCaseResult($rbac->rbacRoleAdd($roleInfo));



/**
 * 用户角色分配UserAssignment(UA)
 * 
 * @param String $username 用户名
 * @param String $rolename 角色名
 * @return Boolean true/false
 */

$username = 'minipig';
$rolename = 'test';
echo "测试函数：rbacCoreUserAssignment()</br>返回:</br>";
testCaseResult($rbac->rbacCoreUserAssignment($username,$rolename));




/**
 * 查询模块信息
 * $modName默认为空：查询所有的模块信息
 * $modName不为空：查询该指定模块的相关信息
 * 
 * @param String $modName 
 * @return Array $modInfo
 */

echo "测试函数：getModuleInfo()</br>返回:</br>";
testCaseResult($rbac->getModuleInfo(''));




/**
 * 添加模块
 * 将需添加的模块信息以数组形式传递，函数检测模块的信息后插入数据表，返回结果
 * @param Array $modInfo
 */

$data = array('per_module_id'=>'1','module_sign'=>'test','module_name'=>'test','order'=>'3');
echo "测试函数：setModuleInsert()</br>返回:</br>";
testCaseResult($rbac->setModuleInsert($data));


/**
 * 修改模块信息
 * 将需修改的模块标识以及新的模块信息以数组形式传递，函数检测模块的信息后更新数据表，返回结果
 * @extend: 从配置文件中读取添加模块的 必须填写信息 有哪些，来进行检测
 * 
 * @param String $modSign
 * @param array $roleInfo
 * @return Boolean true/false 1/0
 */

echo "测试函数：setModuleUpdate()</br>返回:</br>";
testCaseResult($rbac->setModuleUpdate('test', array('module_sign'=>'newtest','module_name'=>'newtest','order'=>'888')));



/**
 * 删除模块节点
 * 传递模块标识module_sign，检测该模块是否存在，处理后返回结果
 * 
 * @param String $modSign
 * @return Boolean true/false 1/0
 */

echo "测试函数：setModDel()</br>返回:</br>";
testCaseResult($rbac->setModDel('newtest'));



/**
 * 角色许可分配PermissionAssignment(PA)
 * 
 * @param String $rolename 角色名
 * @param String $modSign 模块标识
 * @param Int $oper_code 权限操作码 默认为0(无权限)
 * @return Boolean true/false
 */

echo "测试函数：rbacCorePermissionAssignment()</br>返回:</br>";
testCaseResult($rbac->rbacCorePermissionAssignment('test', 'lossNum','30'));



/**
 * 角色许可分配修改&删除(PA)update Del 合并
 * 
 * @param String $rolename 角色名
 * @param String $modSign 模块标识
 * @param Int $oper_code 权限操作码 为0(无权限)表示删除分配
 * @return Boolean true/false
 */

echo "测试函数：rbacCorePermissionAssignmentUpdate()</br>返回:</br>";
testCaseResult($rbac->rbacCorePermissionAssignmentUpdate('test', 'stat',20));
























