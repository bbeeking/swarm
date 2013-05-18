<?php
/**
 * mysql操作类
 */

class db
{
	public $querynum = 0;
  	public $link     = 0;
  	public $query_id = 0;
  	public $record   = array();
  	public $errdesc  = "";
  	public $errno    = 0;
  	public $reporterror = 1;
  	public $dbAry = array();
  	
	/**
  	 * 数据库连接信息
  	 * @param array $dbArray, 连接ip,账号,密码,数据库名,是否为长连接
  	 * @return void
  	 */
	function connect( $dbArray='' ) 
	{
	    if (empty($dbArray))
	    {
	        $this->halt('The info of sql is empty!');
	    }
	    else
	    {
	        $this->dbAry = $dbArray;
	        $dbArray = null;
	    }
	    
		if($this->dbAry['pconnect'])
		{
			if(!$this->link=@mysql_pconnect($this->dbAry['host'], $this->dbAry['name'], $this->dbAry['pass']))
			{
				$this->halt('Can not connect to MySQL server');
			}
		}
		else
		{
		  if(!$this->link=@mysql_connect($this->dbAry['host'], $this->dbAry['name'], $this->dbAry['pass']))
			{
				 $this->halt('Can not connect to MySQL server');
			}
		}
		mysql_select_db($this->dbAry['dbname'],$this->link);
		mysql_query('set names utf8',$this->link);
	}

	function select_db($dbname) {		
		$this->database=$dbname;		
		if(!mysql_select_db($this->database,$this->link)) {
      		$this->halt("cannot use database ".$this->database);
    	}
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
	    $sql = trim($sql);//防止前面有空格,造成本方法cache失败
		$dStartTime = $this->microtimeFloat();
		$sErrorCotent = "";
		if($type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query'))
		{
			$query = mysql_unbuffered_query($sql,$this->link);
		}
		else
		{
			if($type == 'CACHE' && intval(mysql_get_server_info($this->link)) >= 4)
			{
				$sql = 'SELECT SQL_CACHE'.substr($sql, 6);
			}
			if(!($query = mysql_query($sql,$this->link)) && $type != 'SILENT')
			{
				//写日志到XX_DATA_ROOT.'cache/log/'
				$sErrorCotent ="Error:".$this->error();
				$this->writeLog($sql,$sErrorCotent,$dStartTime);
				$this->halt('Error:'.$this->error().'<br>Errno:'.$this->errno(), $sql);
			}
		}
		$this->writeLog($sql,$sErrorCotent,$dStartTime);//写日志
		$this->querynum++;
		return $query;
	}
	
	//事务处理方法
	function _query($sql_ary = array())
	{
		if(is_array($sql_ary))
		{
			$this->begin();
			foreach ($sql_ary as $vv)
			{
				if (!$this->query($vv))
				if (!mysql_query($vv,$this->link))
				{
					$this->rollback();
					RETURN FALSE;
				}
			}
			$this->commit();
		}
		else
		{
			RETURN FALSE;
		}
	}
	
	function begin()
	{
		mysql_query('BEGIN',$this->link);
	}
	
	function commit()
	{
		mysql_query('COMMIT',$this->link);
	}
	
	function rollback()
	{
		mysql_query('ROLLBACK',$this->link);
	}
	
  function query_first($query_string) {
    // does a query and returns first row
    $query_id = $this->query($query_string);
    $returnarray=$this->fetch_array($query_id);
    $this->free_result($query_id);
    if ($returnarray == '')
    	return 0;
    else
    	return $returnarray;
  }
  
  function data_seek($pos,$query_id=-1) {
    // goes to row $pos
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return mysql_data_seek($this->query_id, $pos);
  }

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function error() {
		return mysql_error($this->link);
	}

	function errno() {
		return intval(mysql_errno($this->link));
	}

	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function fetch_object($query) {
		$query = mysql_fetch_object($query);
		return $query;
	}
	
	function num_fields($query) {
		return mysql_num_fields($query);
	}
	
  function field_name($query_id=-1,$n=0) {
    // 返回字段名称
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return mysql_field_name($this->query_id,$n);
  }
        
   function list_tables($query) {
		return mysql_list_tables($query,$this->link);
	}
	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		$id = mysql_insert_id($this->link);
		return $id;
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		if(!mysql_select_db($this->database, $this->link)) {
      $this->halt("cannot use database ".$this->database);
    }
		return $query;
	}

	function close() {
		return mysql_close($this->link);
	}

	/*是否打开转码函数*/
	function IsOpenMBString()
	{
		if (!function_exists('mb_convert_encoding'))
		{
			return false;
		}
		return true;
	}
	/*把UTF-8字符集改成GB2312字符集*/
	function UTF8ToGB2312($sArg)
	{
		if($this -> IsOpenMBString())
		{
			if(is_array($sArg))
			{
				foreach($sArg as $key => $value)
				{
					$sArg[$key] = mb_convert_encoding($value, 'GB2312', 'UTF-8');
				}
			}else $sArg = mb_convert_encoding($sArg, 'GB2312', 'UTF-8');
			return $sArg;
		}
		return NULL;
	}

	/*把GB2312字符集改成UTF-8字符集*/
	function GB2312ToUTF8($sArg)
	{
		if($this -> IsOpenMBString())
		{
			if(is_array($sArg))
			{
				foreach($sArg as $key => $value)
				{
					$sArg[$key] = mb_convert_encoding($value, 'UTF-8', 'GB2312');
				}
			}else $sArg = mb_convert_encoding($sArg,'UTF-8','GB2312');
			return $sArg;
		}
		return NULL;
	}

	/*取得GET参数*/
	function Get($sArg, $sDefault = '', $bCharset = false)
	{
		if (is_array($_GET["$sArg"]))
		{
			foreach($_GET["$sArg"] as $key => $value)
			{
				if (empty($_GET["$sArg"][$key]) && !is_numeric($_GET["$sArg"][$key])) $_GET["$sArg"][$key] = $sDefault;
			}
		}
		else if (empty($_GET["$sArg"]) && !is_numeric($_GET["$sArg"])) $_GET["$sArg"] = $sDefault;
		return $bCharset ? $this -> UTF8ToGB2312($_GET["$sArg"]) : $_GET["$sArg"];
	}

	/*取得POST参数*/
	function Post($sArg, $sDefault = '', $bCharset = false)
	{
		if (is_array($_POST["$sArg"]))
		{
			foreach($_POST["$sArg"] as $key => $value)
			{
				if (empty($_POST["$sArg"][$key]) && !is_numeric($_POST["$sArg"][$key])) $_POST["$sArg"][$key] = $sDefault;
			}
		}else if (empty($_POST["$sArg"]) && !is_numeric($_POST["$sArg"])) $_POST["$sArg"] = $sDefault;
		return $bCharset ? $this -> UTF8ToGB2312($_POST["$sArg"]) : $_POST["$sArg"];
	}
	
	function writeLog($sql,$sErrorCotent="",$dStartTime=0)
	{
		if(!defined('GLOBAL_DEBUG_LEVEL') || GLOBAL_DEBUG_LEVEL == 0)//是否要生成SQL日志:0-不生成，1-生成。
			return 0;
		$dEndTime = $this->microtimeFloat();
		$iSQLTime = intval($dEndTime)-intval($dStartTime);//执行SQL的秒数
		$iDay = "admin_do_log_".@date('Y-m-d');
		$sFile = $iDay.'.log';

		$sIp = $this->get_ip();

		if($sIp == "127.0.0.1")
			$sPathFile = DAEM_DATA_ROOT . 'cache/log/' . $sFile;
	    else
			$sPathFile = '/home/admin_game/adminLog/' . $sFile;
			
		/*
		if(file_exists($sPathFile))
		{
			if($iDay != @date('d', filemtime($sPathFile))) @unlink($sPathFile);
		}
		*/

		$dCreateDate = @date('Y-m-d H:i:s');
		$sUrl = $_SERVER['PHP_SELF'];
		$sIp = $this->get_ip();
		$sUserName = $_SESSION['UserName'];
		
		if(empty($sUserName) || empty($_SERVER['HTTP_HOST']))//如果是后台计划任务则是没有SESSION
			return false;
		$sCotent = "Date:".$dCreateDate."\r\nIP:".$sIp."\r\nUserName:".$sUserName."\r\nUrl:".$sUrl."\r\nSQLTime:".$iSQLTime."\r\n";
		if(!empty($sErrorCotent))
			$sCotent .=$sErrorCotent."\r\n";
		$sCotent .= "DeBugSQL:".$sql."\r\n\r\n";
		
		//@chmod("$sPathFile",0777);
		file_put_contents($sPathFile,$sCotent,FILE_APPEND);
	}
	//获取IP
	function get_ip()
	{
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
		{
			$PHP_IP = getenv('HTTP_CLIENT_IP');
		}
		elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		{
			$PHP_IP = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
		{
			$PHP_IP = getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
		{
			$PHP_IP = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $PHP_IP, $ipmatches);
		unset($PHP_IP);
		return $ipmatches[0] ? $ipmatches[0] : 'unknown';
	}

	function halt($msg,$sql='') {
		$this->errdesc = mysql_error();
		$this->errno = mysql_errno();
		// prints warning message when there is an error
		global $technicalemail, $bbuserinfo, $scriptpath;
		if ($this->reporterror==1) {
		  if ($this->errdesc==='No database selected')
		  {
		  	  if(($_SESSION['HEFU_no_db_alert'])%3===0)
              {
              	echo '<script language="javascript">alert("对不起，数据库【'.$this->dbAry['dbname'].'】不存在，请联系运维处理");</script>';
              }
              ($_SESSION['UserGroup']==1) ? '' : exit();
		  }
		  ++$_SESSION['HEFU_no_db_alert'];
		  $message="<b>Database error in:</b> $this->appname $GLOBALS[templateversion]:\n\n$msg<br><br>\n";
		  $message.="<b>mysql error code:</b>: <font color=red>$this->errdesc</font><br>\n\n";
		  $message.="<b>mysql error number:</b>$this->errno<br>\n\n";
		  $message.="<b>Scripts:</b>" . (($scriptpath) ? $scriptpath : getenv("REQUEST_URI")) . "<br>\n";
		  $message.="<b>Referer URL:</b>".getenv("HTTP_REFERER")."<br>\n";
		  $message.="<b>Date:</b>".date("l dS of F Y h:i:s A")."<br>\n";
		  $message.="<b>Sql:</b>".$sql."<br>\n";

		  if ($technicalemail) {
			@mail ($technicalemail,"$this->appshortname Database error!",$message,"From: $technicalemail");
		  }

		  echo "<html><head><title> Database Error</title><style>P,BODY{FONT-FAMILY:tahoma,arial,宋体;FONT-SIZE:12px;}</style><body>\n\n \n\n";

		  echo "<blockquote><p>&nbsp;</p><p><b> 数据库好象发生了一些微小的错误.</b><br>\n";
		  echo "请按浏览器的 <a href=\"javascript:window.location=window.location;\">刷新</a> 按钮重试.</p>";
		  echo "如果问题仍然存在, 你也可以与我们的<a href=\"mailto:$technicalemail\">技术支持</a>联系.</p>";
		  echo "<p>我们为由此给你带来不便深感抱歉.</p>";
					echo $message ;
					  echo "</blockquote></body></head></html>";
		  exit;
		}
  }
 
	 //将用户的后台操作记录下来
	 function user_log_record($db)
	 {
		  date_default_timezone_set("Asia/Shanghai");
		  $sUserName = $_SESSION['UserName'];		  
		  if(empty($sUserName) || empty($_SERVER['HTTP_HOST']))//如果是后台计划任务则是没有SESSION
			 return false;
		
		  $dRecordTime = @date('Y-m-d H:i:s');
		  $sUrl = $_SERVER['PHP_SELF'];
		  $sUrl = str_replace("/".SITE_DIR."/admin/","",$sUrl);
		  if($sUrl == "chklogin.php")
		  {			 
			 $iMenuId = 0;
			 $sMenuName = "登录";
		  }
		  else if($sUrl == "logout.php")
		  {
			 $iMenuId = -1;
			 $sMenuName = "退出系统";
		  }
		  else if($sUrl == "password.php")
		  {
			 $iMenuId = -2;
			 $sMenuName = "修改密码";
		  }
		  else
		  {
			  $sSql = "select m_id,m_name from ".DB_DAEMDB.".db_menu where m_url like '%$sUrl%' limit 1";
			  $row = $db->query_first($sSql);
			  if(empty($row))
				  return false;
			  $iMenuId = $row['m_id'];
			  $sMenuName = $row['m_name'];
		  }
		  $sIP = $this->get_ip();
		  
		  $sSql = "insert into ".DB_DAEMDB.".log_operation_record set dRecordTime='$dRecordTime',sIP='$sIP',sUserName='$sUserName',sUrl='$sUrl',iMenuId='$iMenuId',sMenuName='$sMenuName'";
		  return $db->query($sSql);
	 }
	 /*获取当前的时间(到微秒级)*/
	function microtimeFloat()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>