<?php
/**
 * DAEMON管理系统 在线流失人数统计
 * 
 * @name stat/lossNum.php
 * @author BEE 2013-02-24
 */

//define('IN_DAEM', true);
//require(dirname(dirname(__FILE__)) . '/includes/init.php');

//获取用户的信息
$userInfoAry = $rbac->rbacGetUserInfo();

//计算记录总数
$sql = "select count(*) as countNum from ".DB_DAEMDB.".per_user";
$result = $db->query_first($sql);
$countNum = $result['countNum'];

//获取所有角色组id对应的角色名数组
$roleAry = array();
$sql = "select * from ".DB_DAEMDB.".per_role";
$query = $db->query($sql);
while ($row = $db->fetch_array($query))
{
	$roleAry[$row['role_id']] = $row['role_name'];
}

//依次遍历用户信息，获取用户的角色
foreach ($userInfoAry as $key=>$val)
{
	//将查询到的角色id->角色名 赋值到用户info数组中
	$userInfoAry[$key][rolename] = $roleAry[$rbac->getUserRole($val['uid'])];
	
	//最后登录时间
	$loginInfo = explode(',', $val['try_error_num']);
	$userInfoAry[$key][lastLoginDate] = $loginInfo[0];
}

//print_r($userInfoAry);

include template('stat','lossNum');