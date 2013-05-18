<?php
//JS跳转页面
function gourl($alert,$page,$history='',$target="window")
{
	if ($alert != "")
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo '<script language="javascript">alert("'.$alert.'")</script>';
	}
	if ($page != "")
	{
		echo '<script language="javascript">'.$target.'.location.href = "'.$page.'";</script>';
	}
	if ($history != "")
	{
		echo '<script language="javascript">history.go('.$history.');</script>';
	}
	exit;
}

function showpage($page,$pagesize,$count,$url)
{
	/*
	列表分页显示函数
	**参数
	$page:当前页
	$pagesize:每页记录条数
	$count:总记录条数
	$url：页面地址
	Return:String
	*/
	if ($page == "") {
		$page = 1;
	}
	if($count == 0) {
		$pagestr="<font color='red'>".L("没有搜索到合乎的记录！")."</font>";
		return $pagestr;
	}
	$pagenum = ceil($count / $pagesize);
	$pagestr=L("总计")."<font color=\"#FF0000\">".$count."</font>".L("记录")."&nbsp;&nbsp;".L("第")."<font color=\"#FF0000\">$page</font>/".$pagenum."".L("页")."&nbsp;&nbsp;";
	if($page==1)
	{
		$pagestr.=L("首页")."&nbsp;&nbsp;".L("上一页")."&nbsp;&nbsp;";
	}
	else
	{
		$pagestr.="<a href=\"".$url."\">".L("首页")."</a>&nbsp;&nbsp;<a href=\"".$url."page=".($page-1)."\">".L("上一页")."</a>&nbsp;&nbsp;";
	}
	if($page<$pagenum)
	{
		$pagestr.="<a href=\"".$url."page=".($page+1)."\">".L("下一页")."</a>&nbsp;&nbsp;<a href=\"".$url."page=".$pagenum."\">".L("尾页")."</a>";
	}
	else
	{
		$pagestr.=L("下一页")."&nbsp;&nbsp;".L("尾页");
	}
	$pagestr.="&nbsp;&nbsp;".L("第")."<input style='width:44px' type='text' name=\"pageno\" id=\"pageno\" value=\"$page\" onchange=\"
	GetElementsByName('input','pageno',this.value);
	\">".L("页")."
	<input type='button' id='iPageButton' name='iPageButton' onClick='

		if(!(/^-?\d+$/.test(document.getElementById(\"pageno\").value)))
		{
			alert(\"".L("页码请输入数字!")."\");
			return false;
		}
		if(document.getElementById(\"pageno\").value>$pagenum || document.getElementById(\"pageno\").value<1)
		{
			alert(\"".L("输入数字不在页码范围!")."\");
			return false;
		}
		else if(document.getElementById(\"pageno\").value == $page)
		{
			alert(\"".L("已是当前页!")."\");return false
		}
		else
		{
			javascript:window.location=\"".$url."page=\"+document.getElementById(\"pageno\").value
		}
		' value='GO'>
	";

	return $pagestr;
}

/**
 * 去除html标签
 *
 * @param string $html
 * @return string
 */
function triphtml($string)
{
	$string =  preg_replace('/<.*?>/', '', $string);
	if(is_array($string)) {
		foreach($string as $key =>$val) {
			$string[$key] = triphtml(trim($val));
		}
	}else {
		$string = trim($string);
		$string = str_replace(array('&','"','\'','<','>',"\t","\r",'{','}'),array('&amp;','&quot;','&#39;','&lt;','&gt;','&nbsp;&nbsp;','','&#123;','&#125;'),$string);
	}
	return $string;
}

/**
 * 管理员操作记录..
 * @param int $userid,管理员ID
 * @param string $content,操作步骤内容
 * @param string $name,被操作的名称
 * @param string $file, 存储文件
 */
function writerecord($userid,$content,$name,$file = '')
{
	global $db;
	empty($file) ? ($file = DAEM_DATA_ROOT.'/home/adminLog/writerecord.txt') : '';

	$sql = "select a_account from ".DB_DAEMDB.".db_adminuser where a_id='".$userid."'";
	if (!$row = $db->query_first($sql)) {
		$db->close();
		gourl('操作记录保存失败，未查询到该用户信息。','',-1);
	}
	$account = $row['a_account'];
	$array = array();
	$array[0] = array('account'=>$account,'content'=>$content,'name'=>$name,'time'=>time());
	//读取之前的操作记录
	$file_content = unserialize(file_get_contents($file));
	$i = 1;
	//保存至数组
	foreach ($file_content as $val) {
		$array[$i] = array('account'=>$val['account'],'content'=>$val['content'],'name'=>$val['name'],'time'=>$val['time']);
		$i++;
	}
	$serialize_array = serialize($array);

	//重新写入
	$handle = fopen($file,"w");
	fwrite($handle,$serialize_array);
	fclose($handle);
}

function chkpurview($label)
{
	/*
	验证权限
	return null
	*/
	global $db;
	$sql = "select b.g_name from ".DB_DAEMDB.".db_adminuser as a left join ".DB_DAEMDB.".db_admingroup as b on a.a_gid=b.g_id where a.a_id='".$_SESSION['UserId']."'";
	if (!$row = $db->query_first($sql)) {
		$db->close();
		gourl('','../noqx.html');
	}
	if ($row['g_name'] != 'administrator') {
		$sql = "select b.r_id from ".DB_DAEMDB.".db_purview as a left join ".DB_DAEMDB.".db_resource as b on b.r_id=a.p_rid where b.r_label='".$label."'";
		if (!$db->query_first($sql)) {
			$db->close();
			gourl('','../noqx.html');
		}
	}
}

/*
 * 由数组构造出SQL语句，用于添加数据到数据库，即INSERT
 */
function makeInsertSqlFromArray($arr, $table)
{
	$str1 = $str2 = '';
	foreach($arr as $k=>$v)
	{
		$str1 .= "`{$k}`,";
		$str2 .= "'{$v}',";
	}

	$str = "INSERT INTO `{$table}` (" . trim($str1, ', ') . ") VALUES (" . trim($str2, ', ') . ")";
	return $str;
}

/**
 * 本函数用来解析模板
 * @param string $mod模块名称,如try
 * @param string $template 模板名称，如index
 * @return string 返回模板缓存地址
 */
function template($mod='' , $template='')
{
    $modAry = array();
    $modAry = debug_backtrace();
    
    //@fix BEE LEUNG 2013-02-18 修复当加载的模版在跟模版目录下mod为空如：{template "","style_switcher"}将出现默认模块为template
    //造成加载模版文件路径$tplfile出错：D:\www\mynah_cms/admin/templates/template/style_switcher.html.php
    if (empty($mod))
    {
        $mod = basename( dirname($modAry[0]['file']) );
        if ($mod == 'template')
        {
        	$mod = 'admin';
        }
    }

    if (empty($template))
    {
        $baseName = basename($modAry[0]['file']);
        $template = substr( $baseName, 0, strpos( $baseName, '.' ));
    }

	$compiledtplfile = $template=='show_message' ? DAEM_TEMPLATE_CACHE_ROOT.$template.'.tpl.php' : DAEM_TEMPLATE_CACHE_ROOT.$mod.'_'.$template.'.tpl.php';
	
	//是否更新模版缓存
	if(TEMPLATE_FRESH)
	{
		//如果模块式当前的admin根目录，则为空
        $tplfile = ROOT_PATH.'templates/'.($mod=='admin' ? '' : $mod.'/').$template.'.html.php';
        if(!file_exists($compiledtplfile) || @filemtime($tplfile) > @filemtime($compiledtplfile))
		{
			require_once ROOT_PATH.'/includes/template.func.php';
			template_refresh($tplfile, $compiledtplfile);
		}
	}
	
	return $compiledtplfile;
}

/**
 * @name：本函数主要用来生成section下拉表单
 * @author : 006 bee(新增加入该下拉表单的样式参数)
 * @param string $name : 表单名
 * @param array $ary_option : option数据数组,格式为：array(value=>words),分别表示option的值和显示的文字.如:array('1'=>'第一项','2'=>'第二项');
 * @param string $sected : 从其他地方读取，以前选择了那一项，并自动在加载完成时选择该项
 * @param array $ary_first : option的第一项，一般就是：array('0'=>'请选择');
 * @param string $id : 表单id
 * @param string $js : section的特效
 * @param string $style : section的样式
 * @return string 返回section下来表单html代码
 * @example get_section('sex',array(1=>'男',2=>'女'),'',$ary_first=array(0=>'性别'),'','',"width:195px;");
 */
function get_section($name,$ary_option,$sected='',$ary_first='',$id='',$js='',$style='')
{
	echo "<select name='".$name."' id='".($id?$id:'')."'".($js?' '.$js.' ':'').($style == ""? "" : "style ='".$style."'").'>\n';
	if(is_array($ary_first))
	{
		foreach ($ary_first as $key=>$val)
		{
			echo '<option value="'.$key.'">'.$val.'</option>';
		}
	}
	if(is_array($ary_option))
	{
		foreach ($ary_option as $k=>$v)
		{
			echo '<option value="'.$k.'"'.($sected==$k ? ' selected="selected"' : '').'>'.$v."</option>\n";
		}
	}
	echo '</select>';
}

/**
 * @name 打印出一整天中每五分钟的时间格式数组
 * @author php006
 * @version 2010.03.03
 * @param string $have_nyr 是不是有年月日，不为空有，为空没有
 */
function get_per_time($nyr='')
{
	$ary = array();
	for($h=0;$h<24;$h++)
	{
		$sh = sprintf('%02d',$h);
		for ($i=0;$i<12;$i++)
		{
			 $ary[] = ($nyr?$nyr.' ':'').$sh.':'.sprintf('%02d',$i*5);
		}
	}
	$ary[] = $ary[0];
	unset($ary[0]);
	return $ary;
}

/**
 * @name 过滤mysql数据，防止sql注入
 * @author PHP006
 * @version 2010-03-19
 * @param $string 需要被处理的字符串或数组
 * @return 返回处理过的$string,因为参数引用的，所以对函数外部数据有改动
 */
function strip_sql(&$string)
{
	$sqlfrom = array("/ union /i","/ select /i","/ update /i","/ outfile /i","/ or /i","/ delete /i");
	$sqlto	 = array('&nbsp;union&nbsp;','&nbsp;select&nbsp;','&nbsp;update&nbsp;','&nbsp;outfile&nbsp;','&nbsp;or&nbsp;','&nbsp;delete&nbsp;');
	return is_array($string) ? array_map('strip_sql',$string) : preg_replace($sqlfrom,$sqlto,$string);
}

/**
 * @name 将html标签转化为html实体，以防止php和mysql注入
 * @author PHP006
 * @version 2010-03-19
 * @param $string 需要被处理的字符串或数组
 * @return 返回处理过的$string，因为参数引用的，所以对外函数外部数据有改动
 */
function strip_html(&$string)
{
	return is_array($string) ? array_map('strip_html',$string) : htmlspecialchars($string,ENT_QUOTES);
}

/**
 * @name	字符串截取函数
 * @param	string $str
 * @param	int $length 需要保留的字符串长度(汉字的话,就是汉字个数),为0时:表示从$start截取到结尾
 * @param	string $dot 超过时显示的符号
 */
function str_cut($str, $length=0, $start =0, $charset = "utf-8")
{
	if(strlen($str)<4) return $str;
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    if($length==0) $length=count($match[0]);
    for(;;)
    {
	 	return join("", array_slice($match[0], $start, $length));
    }
}


/**
 * 管理员操作记录（无需数据库）
 * @param string $content:操作信息
 * @param string $name:被操作的名称
 * @param int    $number:数量
 * @param string $file:存储地址
 * @param string $username:管理员名称
 */
function myrecord($content,$menu_name,$uid,$number,$file = '')
{
        global $_SESSION;
        empty($file) ? ($file = DAEM_DATA_ROOT.'/home/adminLog/myrecord.txt') : '';
        $array = array();
        $array[0] = array('account'=>$_SESSION['UserName'],'content'=>$content,'name'=>$menu_name,'uid'=>$uid,'number'=>$number,'time'=>time());
        //读取之前的操作记录
        $file_content = unserialize(file_get_contents($file));
        $i = 1;
        //保存至数组
        foreach ($file_content as $val) {
                $array[$i] = array('account'=>$val['account'],'content'=>$val['content'],'name'=>$val['name'],'uid'=>$val['uid'],'number'=>$val['number'],'time'=>$val['time']);
                $i++;
        }
        $serialize_array = serialize($array);

        //重新写入
        $handle = fopen($file,"w");
        fwrite($handle,$serialize_array);
        fclose($handle);
}


/**
 * @name	取出当前页面所有链接参数,返回结果类似：a=1&b=2&
 * @param	array $exclude_array：不获取的参数名列表
 */
function get_params($exclude_array = '') {
	global $_GET;
	if ($exclude_array == '') $exclude_array = array();
	$get_url = '';
	reset($_GET);
	while (list($key, $value) = each($_GET)) {
		$value = urlencode($value);
	  if (($key != session_name()) && ($key != 'error') && (!in_array($key, $exclude_array))) $get_url .= $key . '=' . $value . '&';
	}
	return $get_url;
}


/**
		函数功能：
			优化搜索，把非索引字段的搜索范围转换为索引字段的搜索范围。

		参数说明：
		  $table: 搜索表名
		 	$key_name: 表的索引字段名
		 	$minid: 索引字段搜索范围最小值
		 	$maxid: 索引字段搜索范围最大值
		 	$search_name: 原始sql匹配的搜索字段
		 	$search_value: 原始sql匹配的搜索字段对比值
		 	$type: max-获取优化sql语句的索引字段最大匹配值，min-获取优化sql语句的索引字段最小匹配值
		 	$deep: 搜索深度
		 	$exitv: 搜索结果小于此数值，返回结果

	 	用例说明：
		 	从xiuxian_log.t_log_getitem表搜索get_time在2011-01-14 00:00:00 到 2011-01-14 23:59:59这个时间段内的数据。
		 	但xiuxian_log.t_log_getitem数据量非常大，且get_time没索引，id有索引，且id越大get_time越大。
		 	符合上述情况的搜索适用本函数优化
		 	$s_t = "2011-01-14 00:00:00";
		 	$e_t = "2011-01-14 23:59:59";
		 	$id_ceil 	= get_search_key(1,$MaxIdOfTable,'xiuxian_log.t_log_getitem','id','get_time',$e_t,'max');
			$id_floor = get_search_key(1,$MaxIdOfTable,'xiuxian_log.t_log_getitem','id','get_time',$s_t,'min');
			$id_opt = " and a.id >= ".(int)$id_floor." and a.id <= ".(int)$id_ceil; //添加此搜索条件到原来sql中
	**/
	function get_search_key($minid,$maxid,$table,$key_name,$search_name,$search_value,$type,$deep=20,$exitv=3600)
	{
	    global $db;
		if($minid == 1)
		{
			$min_query = $db->query("select min($key_name) as minid from $table");
			$rsmin = $db->fetch_array($min_query);
			if($rsmin && $rsmin['minid'] > 1)
				$minid = $rsmin['minid'];
		}

		if($deep==0 || ($maxid-$minid) < $exitv)
		{
			return $type=='max' ? $maxid : $minid;
		}

		$id = round(($minid+$maxid)/2);
		$query = $db->query("select $key_name, $search_name from $table where $key_name <= $id order by id desc limit 1");
		$rs = $db->fetch_array($query);

		if( !$rs || ( $rs && $rs[$key_name] <= $minid ) )
		{
			return $type=='max' ? $maxid : $minid;
		}

		$id = $rs[$key_name];
		if( $rs[$search_name] < $search_value )
		{
			$minid = $id;
			$deep--;
			return get_search_key($minid,$maxid,$table,$key_name,$search_name,$search_value,$type,$deep,$exitv);
		}
		elseif( $rs[$search_name] > $search_value )
		{
			$maxid = $id;
			$deep--;
			return get_search_key($minid,$maxid,$table,$key_name,$search_name,$search_value,$type,$deep,$exitv);
		}
		else//相等的情况，避免有相同的$search_name，在这里返回上一此调用的结果
		{
			if($type=='max')
				$sql = "select $key_name, $search_name from $table where $key_name > $id and $search_name > '".$search_value."' order by $key_name limit 1";
			else
				$sql = "select $key_name, $search_name from $table where $key_name < $id and $search_name < '".$search_value."' order by $key_name desc limit 1";
			$query = $db->query($sql);

			if($rs = $db->fetch_array($query))
				return $rs[$key_name];
			else
				return $type=='max' ? $maxid : $minid;
		}
	}


	/**
	 * @name 采用google翻译接口翻译指定语言到其他语言
	 * @param string $text  被翻译的原文
	 * @param string $tlang 目标语言
	 * @param string $flang 原文语言
	 * @param array  $formatfrom 将译文的开头为数字,中间不是字母数字的符号替换为$formatto(例如:$formatfrom=array('/^[0-9]+/' , '/[^a-z0-9]/i'))
	 * @param string $formatto 同上(例如:$formatto='_')
	 * @return 返回格式化有的译文
	 */
	function translate($text,$tlang="zh",$flang="",$formatfrom=array(),$formatto='')
	{
	    $returnStr = '';
	    'cn'===strtolower($tlang) ? ($tlang = 'zh') : '';
		$text = urlencode($text);
		$url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q={$text}&langpair={$flang}|{$tlang}";
	    $cl = curl_init( $url);
	    curl_setopt( $cl , CURLOPT_RETURNTRANSFER,true);
	    curl_setopt( $cl, CURLOPT_USERAGENT , 'Mozilla/5.0');
	    $res = curl_exec($cl);
	    curl_close( $cl);
	    $res = (array)json_decode($res);
	    $res = (array)$res['responseData'];
	    if (empty($formatfrom) || empty($formatto))
	    {
			return $res;
		}
		else
		{
			$retrunstr = str_replace(array('\\','"',"'"), array('','',''), $res);
	    	return preg_replace($formatfrom , $formatto , $retrunstr);
		}
	}

	/**
	 * @name Curl Get提交数据
	 * @param string $url 接收数据的api,以及数据
	 * @param int $second 要求程序必须在$second秒内完成,负责到$second秒后放到后台执行
	 * @return string or boolean 成功且对方有返回值则返回
	 */
	function curl_get($url,$second=30) {
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    if ($data)
	        return $data;
	    else
	        return false;
	}

	/**
	 * @name Curl Post数据
	 * @param string $url 接收数据的api
	 * @param string $vars 提交的数据
	 * @param int $second 要求程序必须在$second秒内完成,负责到$second秒后放到后台执行
	 * @return string or boolean 成功且对方有返回值则返回
	 */
	function curl_post($url, $vars, $second=30)
	{
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_TIMEOUT,$second);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
			$data = curl_exec($ch);
			curl_close($ch);
			if($data)
				return $data;
			else
				return false;
	}


	

	 /**
	 * 语言包判断函数，$key值为中文
	 * return array
	 */
	function L( $key )
	{
	    $key = trim($key);
	    $modAry = array();
        $modAry = debug_backtrace();
		$lang = LANG_ARR_NAME;
		global $$lang,$temp_lang;
		$lang=$$lang;
		
		if(!isset($lang[$key]))
		{
			$temp_lang[$key] = $key;
			//测试环境的时候就去更新ch语言包
			(1===ISTEST) ? upd_L() : '';
			return $key;
		}
		else
		{
			return $lang[$key];
		}
	}

	/**
	 * 更新语言包数组文件
	 */
	function upd_L()
	{
		$langName = LANG_ARR_NAME;
		global $$langName,$temp_lang,$mod_lang_path;
		$mod_lang_path = empty($mod_lang_path) ? '' : $mod_lang_path;
		echo runInfo();//@todo 打印系统信息测试调试用,正式环境不要
		if(count($temp_lang))
		{
			$str_array='';
			$temp_lang = array_unique($temp_lang);

			foreach($temp_lang as $key => $val)
			{
			    if (isset($$langName[$key]))
			    {
			        continue;
			    }
				$str_array.="\r\n".'$'.$langName.'["'.$key.'"]="'.$val.'";';
			}
			if(!file_exists($mod_lang_path))
			{
				file_put_contents($mod_lang_path,'<?php'."\r\n");
			}
			if(!$handle = fopen($mod_lang_path,"a"))
			{
				echo "打开语言文件 ".$mod_lang_path." 失败";
				exit;
			}
			if(fwrite($handle,$str_array)===false)
			{
				echo "写入语言文件  ".$mod_lang_path." 失败";
				exit;
			}
			echo "已更新语言文件：".$mod_lang_path;
			fclose($handle);
		}
	}


	/**
	 * 展示开发环境运行数据
	 */
	function runInfo()
	{
        //显示运行时间
        $return['time'] = number_format((microtime(true)-START_TIME),4).'s';

        //显示运行内容
        $startMem =  array_sum(explode(' ',START_MEMORY));
        $endMem   =  array_sum(explode(' ',memory_get_usage()));
        $return['memory'] = number_format(($endMem - $startMem)/1024).'kb';

        //运行时间大于0.2秒的才打系统信息
        if ($return['time']>0.2)
        {
            $echoMsg =  '<div id="FrtDebugFloat" style="Z-INDEX:200;RIGHT:10px;VISIBILITY:visible;POSITION:absolute;border:1px #333333 solid;">
            			<table border="1" cellpadding="1" cellspacing="0">
                            <font size="1">
                            <tbody align="center" bgcolor="#eeeeec">
                            	<tr><th colspan="5" align="center" bgcolor="#f57900">系统运行信息</th></tr>
                                <tr><th></th><th>Time</th><th>Memory</th><th>mode</th><th></th></tr>
                                <tr><td></td><td>'.$return['time'].'</td><td>'.$return['memory'].'</td><td>web</td><td></td></tr>
                    		</tbody>
                    		</font>
                        </table>
                        </div>
            			<script type="text/javascript">
                        //<![CDATA[
                        var tips;
                        var theTop = 10/*这是默认高度,越大越往下*/;
                        var old = theTop;
                        function initFloatTips()
                        {
                        	tips = document.getElementById("FrtDebugFloat");
                        	moveTips();
                        };
                        function moveTips() {
                        var tt=50;
                        if (window.innerHeight) {
                            pos = window.pageYOffset
                        }
                        else if (document.documentElement && document.documentElement.scrollTop) {
                            pos = document.documentElement.scrollTop
                        }
                        else if (document.body) {
                            pos = document.body.scrollTop;
                        }

                        pos=pos-tips.offsetTop+theTop;
                        pos=tips.offsetTop+pos/10;
                        if (pos < theTop) pos = theTop;
                        if (pos != old) {
                            tips.style.top = pos+"px";
                            tt=10;
                        }

                        old = pos;
                        setTimeout(moveTips,tt);
                        }
                        //!]]>
                        initFloatTips();
                        </script>';
        }
        else
        {
            return false;
        }
        return $echoMsg;
	}

	/**
	 * 匹配字符串 添加选择属性
	 *
	 * @param 原数据 string $string
	 * @param 匹配字符串 string $param
	 * @param 类型 $type
	 * @return string
	 */
	function selected($string,$param =1,$type = 'select') {
		$returnString = '';
		if ($string == $param) {
			$returnString = $type == 'select'?'selected="selected"': 'checked="checked"';
		}
		return $returnString;
	}
/**
 * 返回分页中的url地址
 *
 * @return string
 */
function setPageUrl()
{
	return "?".preg_replace('/&*(page|pageno)=\d{0,}/i', '', $_SERVER["QUERY_STRING"])."&";
}

/**
 * 记录日志
 *
 * @param string $request_url
 * @param string $error
 * @param string $file
 * @reutrn void
 */
function errorLog( $error, $request_url='', $file="errorLog.log")
{
    if (empty($request_url))
    {
        $debugInfo = debug_backtrace();
        $request_url = $debugInfo[0]['file'];
        $line = '=>line:'.$debugInfo[0]['line'];
    }
    else
    {
        $line = '';
    }
	$error = "[".date('Y-m-d H:i:s')."]\n"."url=".$request_url.$line." \n".$error."\n\n";
	$ip = getIp();
	if($ip == "127.0.0.1"){
		@file_put_contents(DAEM_DATA_ROOT.'/home/adminLog/127_'.date('Y-m').'_'.$file,$error,FILE_APPEND);
	}else{
		@file_put_contents(DAEM_DATA_ROOT.'/home/adminLog/'.date('Y-m').'_'.$file,$error,FILE_APPEND);
	}
}


/**
 * 获取当前ip
 *
 * @return string, 返回匹配到的ip地址
 */
function getIp()
{
    $realip = '';
    if (isset($_SERVER))
    {
        if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )
        {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif ( isset($_SERVER["HTTP_CLIENT_IP"]) )
        {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else
        {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    }
    else
    {
        if ( getenv("HTTP_X_FORWARDED_FOR") )
        {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif ( getenv("HTTP_CLIENT_IP") )
        {
            $realip = getenv("HTTP_CLIENT_IP");
        }
        else
        {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/**
 * 将int型日期转为Y-m-d格式
 *
 * @param int $date
 *
 * @return string
 */
function setDateType($date)
{
	$date = intval($date);
	return substr($date, 0,4).'-'.substr($date, 4,2).'-'.substr($date, 6,2);
}


/**
 * 获取周一日期：格式2011-11-18
 *
 * @param string $date, 格式：2011-11-18
 * @param string $type, 默认-1，上周一：-2、-3、-4 等等
 * @return string 格式：2011-11-18
 */
function get_monday( $date, $type="-1" )
{
	$date = empty($date) ? date('Y-m-d') : $date;
	return date('Y-m-d',strtotime($type.' Monday',strtotime($date)+86400));
}

/**
 * 获取周日日期：格式2011-11-18
 *
 * @param string $date, 格式：2011-11-18
 * @param string $type, 默认+1，上周日：-1、-2、-3 等等
 * @return string 格式：2011-11-18
 */
function get_sunday( $date, $type='+1')
{
	$date = empty($date) ? date('Y-m-d') : $date;
	return date('Y-m-d',strtotime($type.' Sunday',strtotime($date)));
}

/**
 * 检查日期格式是否正确
 *
 * @param string $date 格式：2011-01-01 00:00:00
 * @return boolen
 */
function checkDateTime($date)
{
	if(empty($date)) return false;
	$pattern = '/^[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}$/';
	if (preg_match($pattern, $date))
	{
		return true;
	}
	else
	{
		return false;
	}
}


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
