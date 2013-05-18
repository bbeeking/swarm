<?php
/**
 * 数据库相关定义文件
 * @author bee 2012-08-21
 */

//数据库前缀
$dbSuffix = '';

//定义数据库前缀
define('DB_SUFFIX', $dbSuffix);

//定义日志表名,合服后查旧数据有用到。
define('DB_DAEMDB',"daemon".DB_SUFFIX);

$slaveDb['dbname'] = DB_DAEMDB;

//主库配置
$masterDbHost = '127.0.0.1';
$masterDb['host']   = $masterDbHost.':3306';
$masterDb['name']   = 'root';
$masterDb['pass']   = '';
$masterDb['dbname'] = DB_DAEMDB;
$masterDb['pconnect'] = '0';

//备库配置
$slaveHost = '127.0.0.1';
$slaveDb['host']   = $slaveHost.':3306';
$slaveDb['name']   = 'root';
$slaveDb['pass']   = '';
$slaveDb['dbname'] = DB_DAEMDB;
$slaveDb['pconnect'] = '0';
