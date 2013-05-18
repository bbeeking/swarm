<?php
/**
 * rbac权限控制类:
 * @author BEE LEUNG 2012-12-13
 * @version v1.0
 */

class RBAC 
{
	public $conn;//数据库连接句柄
	public $userId;//用户id
	public $roleId;//用户对应角色id
	public $accessModAry;//角色对应模块同菜单的许可列表
	
	public function __construct($userId,$conn){
		$this->userId = $userId;
		$this->conn = $conn;
		
		$this->roleId = RBAC::getUserRole($this->userId);
		
		//角色对应模块同菜单的允许访问列表(在登录时查询后存放入session中)
		$this->accessModAry = RBAC::getRoleMod($this->roleId);
	}
	
	//根据用户id获取用户对应的角色组id
	public function getUserRole($userId){
		$aRole = '';
		$sql = "SELECT * FROM per_user_role_control WHERE user_id = '".$userId."' limit 1";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query))
		{
			$aRole = $row['role_id'];
		}
		return $aRole;
	}

	
	/**
	 * 对资源整体区分成3个模块： 模块(mod) -> 菜单 -> 页面元素(table,button,input,pic)
	 * 1.用户登录时便计算其角色对各个模块的许可情况
	 * 2.若模块禁止访问则不记录入允许访问表中
	 * 3.若模块允许访问，依次计算角色对该模块下各个菜单的许可情况
	 * 
	 * 输入角色返回角色对应允许访问的模块及菜单
	 
	static function getRoleModMenuPerm($userRole){
		$menuAry = array();
		
		//计算角色分配到的模块及其菜单
		$sql = "SELECT * from per_role_resource_control where role_id = '".$userRole."' and resource_type = 'module'";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query)) 
		{
			$sqlSon = "select * from per_role_resource_control where role_id = '".$userRole."' and resource_type = 'menu' and per_resource_id = '".$row['resource_id']."'";
			$querySon = mysql_query($sqlSon);
			while ($res = mysql_fetch_assoc($querySon)) 
			{
				$menuAry[$row['module_name']][$res['menu_name']] = $row['module_sign'].'/'.$res['menu_sign'];
			}
		}
		return $menuAry;
	}	
	*/
	
/*
	//获取角色许可的模块->菜单
	static function getRoleMod($roleId)
	{
		$modMenuAry = array();
		$sql = "select a.*,b.* from per_role_resource_control a,per_module b 
				where a.role_id = '".$roleId."' 
				and a.resource_id = b.module_id
				and b.per_module_id = 0
				and b.allow_access = '1'
				order by b.order";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query)) 
		{
			//查询父模块下的子菜单模块
			$sqlSon = "select a.*,b.* from per_role_resource_control a,per_module b 
						where a.role_id = '".$roleId."'
						and a.resource_id = b.module_id
						and b.per_module_id = '".$row['resource_id']."'
						and b.allow_access = '1'
						order by b.order";
			$querySon = mysql_query($sqlSon);
			while ($res = mysql_fetch_assoc($querySon))
			{
				//modMenuAry 用于session存放展示菜单
//				$modMenuAry[$row['module_name']][$res['module_name']] = $row['module_sign'].'_'.$res['module_sign'];
				
				//modAry 用于用户访问的权限判断，在授权模块下的授权菜单才允许用户访问
//				$modMenuAry[$row['module_sign']][$res['module_sign']] = 1;

				//合并modMenuAry同modAry,增加前缀区分·
				$modAry['show'][$row['module_name']][$res['module_name']] = $row['module_sign'].'_'.$res['module_sign'];
				$modAry['check'][$row['module_sign']][$res['module_sign']] = 1;
			}
		}
		
		return $modAry;
	}
*/

	//获取角色许可的模块->菜单
	static function getRoleMod($roleId)
	{
		$modMenuAry = array();
		$sql = "select a.*,b.* from per_role_resource_control a,per_module b 
				where a.role_id = '".$roleId."' 
				and a.resource_id = b.module_id
				and b.per_module_id = 0
				and b.allow_access = '1'
				order by b.order";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query)) 
		{
			//查询父模块下的子菜单模块
			$sqlSon = "select a.*,b.* from per_role_resource_control a,per_module b 
						where a.role_id = '".$roleId."'
						and a.resource_id = b.module_id
						and b.per_module_id = '".$row['resource_id']."'
						and b.allow_access = '1'
						order by b.order";
			$querySon = mysql_query($sqlSon);
			while ($res = mysql_fetch_assoc($querySon))
			{
				//合并modMenuAry同modAry,增加前缀区分·
				$modAry[$row['module_sign']][$res['module_sign']] = 0;
			}
		}
		
		return $modAry;
	}

	/**
	 * 扩展 ：根据用户的角色id同模块获取角色对应功能模块元素的授权操作
	 * @param Int $roleId 
	 * @param String $mod : 'stat/onlineNum'
	 * @return Array $elementPermAry
	 */
	static function getRoleModElement($roleId,$mod)
	{
		$elementPermAry = array();
		
		//查询模块id
		$modAry = explode('/', $mod);
		$count = count($modAry);
		
		$sql = "select module_id from per_module where module_sign = '".$modAry[$count-1]."' limit 1";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query))
		{
			$moduleId = $row['module_id'];
		}
		
		//查询该模块下的所有元素id
		$sql = "select element_id from per_element where module_id = '".$moduleId."'";
		$query = mysql_query($sql);
		
		$elementStr = '';
		while ($row = mysql_fetch_assoc($query))
		{
			$elementStr .= "'".$row['element_id']."',";
		}
		$elementStr = trim($elementStr,',');
		
		
		//查询角色对应查询结果的元素的权限
		$sql = "select a.*,b.* from per_role_element_control a,per_element b  
				where a.role_id = '".$roleId."' 
				and a.element_id = b.element_id
				and a.element_id in (".$elementStr.")";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query))
		{
			$elementPermAry[$row['element_sign']] = $row['oper_code'];
		}
		
		return $elementPermAry;
	}
	
	//计算当前受限资源操作授权权限最大值
	static function countPermMaximum()
	{
		//初始化授权码
		$permMaximum = 0;
		
		$sql = "select sum(oper_id) as perm_maximum from per_operation";
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query))
		{
			$permMaximum += $row['perm_maximum'];
		}
		return $permMaximum;
	}
	
	/**
	 * 
	 * 根据角色获取的授权码oper_code同操作类型 如：检索search,增加add,删除del,修改update等判断该操作是否合法
	 * @param int $authCode	授权码如 7
	 * @param string $oper	操作类型
	 */
	static function checkAuthorization($authCode,$oper)
	{
		$permMaximum = RBAC::countPermMaximum();
		if ($authCode == $permMaximum){
			return $permMaximum;
		}
		else 
		{
			$operIdAry = array();
			
			//获取该操作的对应操作id
			$sql = "select oper_id from per_operation where oper_sign = '".$oper."'";
			$query = mysql_query($sql);
			while ($row = mysql_fetch_assoc($query))
			{
				$operId = $row['oper_id'];
			}
			
			//查询所有操作的操作码
			$sql = "select oper_id from per_operation order by oper_id asc";
			$query = mysql_query($sql);
			while ($row = mysql_fetch_assoc($query))
			{
				$operIdAry = $row['oper_id'];
			}
			$countNum = count($operIdAry);
			
			//判断待查询操作的oper_id是否在授权码中判断该操作是否合法
			while ($authCode >= $operId)
			{
				if ($authCode >= $operIdAry[$countNum-1])
				{
					if ($operIdAry[$countNum-1] == $operId)
					{
						return TRUE;
					}
					else 
					{
						$authCode -= $operIdAry[$countNum-1];
						$countNum -= 1;
						if ($authCode < $operId)
							return FALSE; 
					}
				}
				else 
				{
					$countNum -= 1;
				}
			}
		}
	}
	
	
	
	
	
	/* 以下是RBAC权限控制管理类基本的管理应用操作 2013-01-21  */
	
	/**
	 * 查询用户信息
	 * username默认为空：查询所有的用户信息
	 * username不为空：查询该指定用户的相关信息
	 * 
	 * @param String $username 
	 * @return Array $userInfo
	 */
	public function rbacGetUserInfo($username)
	{
		$userInfo = array();
		if (empty($username))
		{
			$sql = "select * from per_user";
		}
		else
		{
			$sql = "select * from per_user where username = '".$username."'";
		}
		
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query))
		{
			$userInfo[] = $row;
		}
		
		return $userInfo;
	}
	
	
	
	/**
	 * 添加管理员账号
	 * 将需添加的管理员信息以数组形式传递，函数检测用户的信息后插入数据表，返回结果
	 * @extend: 从配置文件中读取添加账号的 必须填写信息 有哪些，来进行检测
	 * 
	 * @param array $userInfo
	 * @return Boolean true/false 1/0
	 */
	public function rbacUserAdd($userInfo)
	{
		if (!is_array($userInfo))
		{
			return FALSE;
		}
		else 
		{
			//判断该用户是否已经存在
			$userCheck = $this->rbacGetUserInfo($userInfo['username']);
			if (!empty($userCheck)){
				return FALSE;	
			}
			
			$insertStr = '';
			foreach ($userInfo as $key=>$val)
			{
				$insertStr .= $key." = '".$val."',";
			}
			$insertStr = trim($insertStr,',');
			$sql = "insert into per_user set ".$insertStr;
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	
	/**
	 * 修改管理员账号信息
	 * 将需修改的管理员username，以及新的账号信息以数组形式传递，函数检测用户的信息后更新数据表，返回结果
	 * @extend: 从配置文件中读取添加账号的 必须填写信息 有哪些，来进行检测
	 * @todo： 考虑是否将其合并到 管理员账号的添加操作中
	 * 			合并至账号控制函数：
	 * 				1.增加 ： $username = null,$infoAry != null
	 * 				2.修改 ： $username != null, $infoAry != null
	 * 				3.删除 ： $username != null, $infoAry = null
	 * 				4.查询全部 ： $username = null, $infoAry = null
	 * 但这样如果刚好数据轮空，容易造成误删操作,所以必须还加上操作标示
	 * 
	 * @param String $username
	 * @param array $userInfo
	 * @return Boolean true/false 1/0
	 */
	public function rbacUserUpdate($username,$updateInfo)
	{
		if (!is_array($updateInfo) || empty($username))
		{
			return FALSE;
		}
		else 
		{
			//查询该username是否存在
			$userInfo = $this->rbacGetUserInfo($username);
			if (empty($userInfo)){
				return FALSE;
			}
			else 
			{
				//判断是否需要修改username，如果是则判断新的username是否已经存在
				$updateUsername = $this->rbacGetUserInfo($updateInfo['username']);
				if (!empty($updateUsername))
				{
					return FALSE;
				}
				
				$updateStr = '';
				foreach ($updateInfo as $key=>$val)
				{
					$updateStr .= $key." = '".$val."',";
				}
				$updateStr = trim($updateStr,',');
				$sql = "update per_user set ".$updateStr." where username = '".$username."'";
				$result = mysql_query($sql);
				return $result;
			}
		}
	}
	
	
	/**
	 * 删除管理员账号
	 * 传递管理员username，检测账号是否存在，处理后返回结果
	 * 
	 * @param String $username
	 * @return Boolean true/false 1/0
	 */
	public function rbacUserDel($username)
	{
		if (empty($username))
		{
			return false;
		}
		else
		{
			//查询用户账号是否存在
			$userInfo = $this->rbacGetUserInfo($username);
			if (empty($userInfo))
			{
				return FALSE;
			}
			else 
			{
				$sql = "delete from per_user where username = '".$username."'";
			}
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	
	/**
	 * 查询角色信息
	 * $roleName默认为空：查询所有的角色信息
	 * $roleName不为空：查询该指定角色的相关信息
	 * 
	 * @param String $roleName 
	 * @return Array $roleInfo
	 */
	public function rbacGetRoleInfo($roleName)
	{
		$roleInfo = array();
		if (empty($roleName) || $roleName == '')
		{
			$sql = "select * from per_roles";
			$query = mysql_query($sql);
			while ($row = mysql_fetch_assoc($query))
			{
				$roleInfo[] = $row;
			}
		}
		else
		{
			$sql = "select * from per_roles where role_name = '".$roleName."'";
			$query = mysql_query($sql);
			$roleInfo = mysql_fetch_assoc($query);
		}
		
		return $roleInfo;
	}
	
	/**
	 * 添加角色
	 * 将需添加的角色信息以数组形式传递，函数检测角色的信息后插入数据表，返回结果
	 * @param Array $roleInfo
	 */
	public function rbacRoleAdd($roleInfo)
	{
		if (!is_array($roleInfo))
		{
			return FALSE;
		}
		else 
		{
			//判断该角色是否已经存在
			$roleCheck = $this->rbacGetRoleInfo($roleInfo['role_name']);
			if (!empty($roleCheck)) return FALSE;
			
			$insertStr = '';
			foreach ($roleInfo as $key=>$val)
			{
				$insertStr .= $key." = '".$val."',";
			}
			$insertStr = trim($insertStr,',');
			$sql = "insert into per_roles set ".$insertStr;
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	/**
	 * 修改角色信息
	 * 将需修改的角色名role_name，以及新的角色信息以数组形式传递，函数检测角色的信息后更新数据表，返回结果
	 * @extend: 从配置文件中读取添加角色的 必须填写信息 有哪些，来进行检测
	 * 
	 * @param String $roleName
	 * @param array $roleInfo
	 * @return Boolean true/false 1/0
	 */
	public function rbacRoleUpdate($roleName,$updateInfo)
	{
		if (!is_array($updateInfo) || empty($roleName))
		{
			return FALSE;
		}
		else 
		{
			//查询该需修改的角色$roleName是否存在
			$roleInfo = $this->rbacGetRoleInfo($roleName);
			if (empty($roleInfo)){return FALSE;}
			else 
			{
				//判断是否需要修改角色名，如果是则判断新的角色名是否已经存在
				$updateRoleName = $this->rbacGetRoleInfo($updateInfo['role_name']);
				if (!empty($updateRoleName))
				{
					return FALSE;
				}
				
				$updateStr = '';
				foreach ($updateInfo as $key=>$val)
				{
					$updateStr .= $key." = '".$val."',";
				}
				$updateStr = trim($updateStr,',');
				$sql = "update per_roles set ".$updateStr." where role_name = '".$roleName."'";
				$result = mysql_query($sql);
				return $result;
			}
		}
	}
	
	/**
	 * 删除角色
	 * 传递角色role_name，检测该角色是否存在，处理后返回结果
	 * 
	 * @param String $roleName
	 * @return Boolean true/false 1/0
	 */
	public function rbacRoleDel($roleName)
	{
		if (empty($roleName))
		{
			return false;
		}
		else
		{
			//查询用户账号是否存在
			$roleInfo = $this->rbacGetRoleInfo($roleName);
			if (empty($roleInfo))
			{
				return FALSE;
			}
			else 
			{
				$sql = "delete from per_roles where username = '".$roleName."'";
			}
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	
	
	/**
	 * 用户角色分配UserAssignment(UA)
	 * 
	 * @param String $username 用户名
	 * @param String $rolename 角色名
	 * @return Boolean true/false
	 */
	public function rbacCoreUserAssignment($username,$rolename)
	{
		if (empty($username) || empty($rolename))
		{
			return 'Parameter Missing';
		}
		else
		{
			//判断用户及角色是否存在
			$userInfo = $this->rbacGetUserInfo($username);
			$roleInfo = $this->rbacGetRoleInfo($rolename);
			
			if (!isset($userInfo) || empty($userInfo)) 
			{
				return 'User Info No Found';
			}
			if (!isset($roleInfo) || empty($roleInfo))
			{
				return 'Role Info No Found';
			}
			
			//判断用户是否已经进行了角色分配
			$checkUserRole = $this->getUserRole($userInfo['uid']);
			if ($checkUserRole > 0)
			{
				return 'Repeat UserAssignment';
			}
			else 
			{
				$sql = "insert into per_user_role_control set user_id = '".$userInfo['uid']."',role_id = '".$roleInfo['role_id']."'";
				$result = mysql_query($sql);
				return $result;
			}
		}
	}
	
	
	/**
	 * 用户角色分配(UA)修改操作
	 * 
	 * @param String $username 用户名
	 * @param String $rolename 角色名
	 * @return Boolean true/false
	 */
	public function rbacCoreUserAssignmentUpdate($username,$rolename)
	{
		
	}
	
	/**
	 * 用户角色分配(UA)删除操作
	 * 
	 * @param String $username 用户名
	 * @param String $rolename 角色名
	 * @return Boolean true/false
	 */
	public function rbacCoreUserAssignmentDel($username,$rolename)
	{
		
	}
	
	
	
	/* 以下是module模块控制管理类基本的管理应用操作 2013-01-23  */
	
	/**
	 * 查询模块信息
	 * $modName 模块标识 默认为空：查询所有的模块信息
	 * $modName不为空：查询该指定模块的相关信息
	 * 
	 * @param String $modSign 
	 * @return Array $modInfo
	 */
	public function getModuleInfo($modSign)
	{
		$modInfo = array();
		if (empty($modSign))
		{
			$sql = "select * from per_module";
			$query = mysql_query($sql);
			while ($row = mysql_fetch_assoc($query))
			{
				$modInfo[] = $row;
			}
		}
		else
		{
			$sql = "select * from per_module where module_sign = '".$modSign."'";
			$query = mysql_query($sql);
			$modInfo = mysql_fetch_assoc($query);
		}
		
		
		return $modInfo;
	}
	
	
	
	/**
	 * 添加模块节点
	 * 将需添加的模块信息以数组形式传递，函数检测模块的信息后插入数据表，返回结果
	 * 
	 * @param Array $modInfo
	 */
	public function setModuleInsert($modInfo)
	{
		if (!is_array($modInfo) || empty($modInfo))
		{
			return 'Module Info Missing Or illegal';
		}
		else 
		{
			//判断该模块是否已经存在
			$modCheck = $this->getModuleInfo($modInfo['module_sign']);
			if (!empty($modCheck)) return 'Repeat Module Sign';
			
			$insertStr = '';
			foreach ($modInfo as $key=>$val)
			{
				$insertStr .= "`$key` = '".$val."',";
			}
			$insertStr = trim($insertStr,',');
			$sql = "insert into `per_module` set ".$insertStr;
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	
	/**
	 * 修改模块信息
	 * 将需修改的模块标识以及新的模块信息以数组形式传递，函数检测模块的信息后更新数据表，返回结果
	 * @extend: 从配置文件中读取添加模块的 必须填写信息 有哪些，来进行检测
	 * 
	 * @param String $modSign
	 * @param array $roleInfo
	 * @return Boolean true/false 1/0
	 */
	public function setModuleUpdate($modSign,$updateInfo)
	{
		if (!is_array($updateInfo) || empty($updateInfo) || empty($modSign))
		{
			return 'Param Info Missing Or illegal';
		}
		else 
		{
			//查询该需修改的模块$modName是否存在
			$roleInfo = $this->getModuleInfo($modSign);
			if (empty($roleInfo)){
				return 'Module Not Found';
			}
			else 
			{
				//判断是否需要修改模块标志，如果是则判断新的模块标志是否已经存在
				$updateModSign = $this->rbacGetRoleInfo($updateInfo['module_sign']);
				if (!empty($updateModSign))
				{
					return 'Repeat Module Sign';
				}
				
				$updateStr = '';
				foreach ($updateInfo as $key=>$val)
				{
					$updateStr .= "`$key` = '".$val."',";
				}
				$updateStr = trim($updateStr,',');
				$sql = "update `per_module` set ".$updateStr." where `module_sign` = '".$modSign."'";
				$result = mysql_query($sql);
				return $result;
			}
		}
	}
	
	
	/**
	 * 删除模块节点
	 * 传递模块标识module_sign，检测该模块是否存在，处理后返回结果
	 * 
	 * @param String $modSign
	 * @return Boolean true/false 1/0
	 */
	public function setModDel($modSign)
	{
		if (empty($modSign))
		{
			return 'Param Info Missing Or illegal';
		}
		else
		{
			//查询模块节点是否存在
			$roleInfo = $this->getModuleInfo($modSign);
			if (empty($roleInfo))
			{
				return 'Module Not Found';
			}
			else 
			{
				$sql = "delete from `per_module` where `module_sign` = '".$modSign."'";
			}
			$result = mysql_query($sql);
			return $result;
		}
	}
	
	
	//角色与模块间的关联操作 2013-01-30 
	
	/**
	 * 角色许可分配PermissionAssignment(PA)
	 * 
	 * @param String $rolename 角色名
	 * @param String $modSign 模块标识
	 * @param Int $oper_code 权限操作码 默认为0(无权限)
	 * @return Boolean true/false
	 */
	public function rbacCorePermissionAssignment($rolename,$modSign,$oper_code=0)
	{
		if (empty($rolename) || empty($modSign))
		{
			return 'Parameter Missing';
		}
		else
		{
			//判断角色及模块是否存在
			$roleInfo = $this->rbacGetRoleInfo($rolename);
			$modInfo = $this->getModuleInfo($modSign);
			
			if (!isset($roleInfo) || empty($roleInfo)) 
			{
				return 'Role Info No Found';
			}
			if (!isset($modInfo) || empty($modInfo))
			{
				return 'Mod Info No Found';
			}
			if ($oper_code < 0)
			{
				return 'Oper Code illegal';
			}
			
			//判断角色对应模块的权限是否已经分配
			$sql = "select id from per_role_resource_control where role_id = '".$roleInfo['role_id']."' and resource_id = '".$modInfo['module_id']."'";
			$query = mysql_query($sql);
			$result = mysql_fetch_assoc($query);
			
			if ($result['id'] > 0)
			{
				return 'Repeat PermissionAssignment';
			}
			else 
			{
				if ($modInfo['per_module_id'] == 0)
				{
					//如果是父模块则直接分配角色-模块权限
					$sql = "insert into per_role_resource_control 
							set role_id = '".$roleInfo['role_id']."',
							resource_id = '".$modInfo['module_id']."',
							oper_code = '".$oper_code."'";
					$result = mysql_query($sql);
					return $result;
				}
				else 
				{
					//子模块则判断其父模块是否已经分配权限
					$sql = "select oper_code from per_role_resource_control where role_id = '".$roleInfo['role_id']."' and resource_id = '".$modInfo['per_module_id']."'";
					$query = mysql_query($sql);
					
					//@todo 优化所有的查询单条记录的 fetch_assoc 无需在循环体内处理
					$result = mysql_fetch_assoc($query);
					
					if ($result['oper_code'] > 0)
					{
						//父模块已经分配权限，检查分配的模块权限是否非法大于父模块
						if ($oper_code > $result['oper_code']) 
						{
							return 'Son\'s Opercode no More Then Father\'s';
						}
						else
						{
							$sql = "insert into per_role_resource_control 
									set role_id = '".$roleInfo['role_id']."',
									resource_id = '".$modInfo['module_id']."',
									oper_code = '".$oper_code."'";
							$result = mysql_query($sql);
							return $result;
						}
					}
					else 
					{
						return 'Per Mod Must Be Assigned First';
					}
				}
			}
		}
	}
	
	
	/**
	 * 角色许可分配修改&删除(PA)update Del 合并
	 * @todo 如果删除或者修改父模块的时候，也需要检查新的权限值不能小于子模块
	 * @todo 授权opercode需要判断操作，不是单纯的数值大于就可以了
	 * 
	 * @param String $rolename 角色名
	 * @param String $modSign 模块标识
	 * @param Int $oper_code 权限操作码 为0(无权限)表示删除分配
	 * @return Boolean true/false
	 */
	public function rbacCorePermissionAssignmentUpdate($rolename,$modSign,$oper_code)
	{
		if (empty($rolename) || empty($modSign))
		{
			return 'Parameter Missing';
		}
		else
		{
			//判断角色及模块是否存在
			$roleInfo = $this->rbacGetRoleInfo($rolename);
			$modInfo = $this->getModuleInfo($modSign);
			
			if (!isset($roleInfo) || empty($roleInfo)) 
			{
				return 'Role Info No Found';
			}
			if (!isset($modInfo) || empty($modInfo))
			{
				return 'Mod Info No Found';
			}
			if ($oper_code < 0)
			{
				return 'Oper Code illegal';
			}
			
			if ($modInfo['per_module_id'] == 0)
			{
				//如果是父模块则判断是否非法小于子模块的权限
				//查询该模块的子模块
				$sonModIdStr = '';
				$sql = "select module_id from per_module where per_module_id = '".$modInfo['module_id']."'";
				$query = mysql_query($sql);
				while ($row = mysql_fetch_assoc($query))
				{
					$sonModIdStr .= "'".$row['module_id']."',";
				}
				$sonModIdStr = trim($sonModIdStr,',');
				
				if (!empty($sonModIdStr))
				{
					//查询所有角色对应子模块的分配的权限
					$sql = "select oper_code from per_role_resource_control where role_id = '".$roleInfo['role_id']."' and resource_id in (".$sonModIdStr.")";
					$query = mysql_query($sql);
					while ($row = mysql_fetch_assoc($query))
					{
						if ($oper_code < $row['oper_code'])
						{
							//存在子模块的权限比要修改的父模块权限高
							return 'Exit Son\'s Opercode More Than Father\'s';
						}
					}
					
					//检查通过
					$sql = "update per_role_resource_control 
							set oper_code = '".$oper_code."' 
							where role_id = '".$roleInfo['role_id']."' 
							and resource_id = '".$modInfo['module_id']."'";
					$result = mysql_query($sql);
					return $result;
				}
				else 
				{
					//不存在子模块直接更新/删除
					$sql = "update per_role_resource_control 
							set oper_code = '".$oper_code."' 
							where role_id = '".$roleInfo['role_id']."' 
							and resource_id = '".$modInfo['module_id']."'";
					$result = mysql_query($sql);
					return $result;
				}
			}
			else 
			{
				//子模块则获取其父模块已分配权限
				$sql = "select oper_code from per_role_resource_control where role_id = '".$roleInfo['role_id']."' and resource_id = '".$modInfo['per_module_id']."'";
				$query = mysql_query($sql);
				$result = mysql_fetch_assoc($query);
				
				//父模块已经分配权限，检查分配的模块权限是否非法大于父模块
				if ($oper_code > $result['oper_code']) 
				{
					return 'Son\'s Opercode No More Then Father\'s';
				}
				else
				{
					$sql = "update per_role_resource_control 
							set oper_code = '".$oper_code."'
							where role_id = '".$roleInfo['role_id']."' 
							and resource_id = '".$modInfo['module_id']."'";
					$result = mysql_query($sql);
					return $result;
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
}








