<?php
/**
 * DAEMON管理系统 左侧菜单导航
 * @author BEE 2013-02-19
 */

//error_reporting(0);
//define('IN_DAEM', true);
//require(dirname(__FILE__) . '/includes/init.php');

$authModAry = $_SESSION[menu];


include template('','sidebar');
