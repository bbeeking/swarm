<?php
/**
* 后台登陆入口
* @author BEE 2012-08-22
*/

$_SESSION['is_browser'] = 1;
require_once("./includes/global.php");
include template('','login');
