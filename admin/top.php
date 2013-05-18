<?php
/**
 * 框架顶部
 * @author BEE 2012-08-22
 */

define('IN_DAEM', true);
require_once './includes/init.php';

$acc = &$_SESSION["UserName"];

include template('','top');
