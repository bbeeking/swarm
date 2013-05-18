<?php
defined('IN_DAEM') or exit('HTTP/1.1 ACCESS FORBIDDEN!');

//	本函数调用模板解析函数，并把编译后的模板内容写入到缓存文件中。
//	@param string $mod代表模块文件夹
//	@parame string $tamplate 模板文件名
//	@return int $strlen 写入缓存的字节数，返回非整数或0将代表写入失败，因此验证是否编译成功可以用if(template_compile($mod,$template)){......}
function template_compile($mod,$template)
{
	$content = file_get_contents(ROOT_PATH.'templates/'.$mod.'/'.$template.'.html.php');
	$content = template_parse($content);
	$compiledtplfile = DAEM_TEMPLATE_CACHE_ROOT.$mod.'_'.$template.'.tpl.php';
	$strlen = file_put_contents($compiledtplfile, $content);
	@chmod($compiledtplfile, 0777);
	return $strlen;
}

//	模板缓存刷新，本函数重新存储直接编译后的模板文件到缓存
//	@param string $tplfile模板文件路径
//	@param string $compiledtplfile编译后的缓存路径
//	@return int $strlen 写入缓存的字节数
function template_refresh($tplfile,$compiledtplfile)
{
	$str = file_get_contents($tplfile);
	$str = template_parse($str);
	$strlen = file_put_contents($compiledtplfile, $str);
	@chmod($compiledtplfile, 0777);
	return $strlen;
}

//	批量编译单模块中的模板文件
//	@param string $mod模块名称
//	@return bool
function template_mod($mod)
{
	//获取该文件夹下所有的.html.php文件存放数组中
	$files = glob(ROOT_PATH.'/templates/'.$mod.'/*.html.php');
	if(is_array($files))
	{
		foreach($files as $tpl)
		{
			$template = str_replace('.html.php', '', basename($tpl));
			template_compile($mod, $template);
		}
	}
	return TRUE;
}

//	批量编译所有模块中的模板文件
//	@return bool
function template_cache()
{
    global $mod;
	foreach($mod as $mod=>$m)
    {
        template_mod($mod);
	}
	return TRUE;
}

//	模板编译函数。本函数采用正则替换模板文件中的标签至标准的php代码
//	@param string $str需要编译的内容，通常是从模板文件读取的字节流，如：$str = file_get_contents($template_file);
//	@return string $str 编译后的字节流
//	insun 08.05.19
function template_parse($str)
{
	$str = preg_replace("/([\n\r]+)\t+/s","\\1",$str);
	//$str = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}",$str);
	$str = preg_replace("/\{template\s+([^\}]+)\}/","\n<?php include template(\\1); ?>\n",$str);
	$str = preg_replace("/\{include\s+([^\}]+)\}/","\n<?php include \\1; ?>\n",$str);
	$str = preg_replace("/\{php\s+(.+?)\}/","<?php \\1?>",$str); //此行解析只能解析单行php代码，如果是多行的，要改成单行，删除不必要的换行字符。
	$str = preg_replace("/\{if\s+(.+?)\}/","<?php if(\\1) { ?>",$str);
	$str = preg_replace("/\{else\}/","<?php } else { ?>",$str);
	$str = preg_replace("/\{elseif\s+(.+?)\}/","<?php } elseif (\\1) { ?>",$str);
	$str = preg_replace("/\{\/if\}/","<?php } ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2) { ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/","\n<?php if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>",$str);
	$str = preg_replace("/\{\/loop\}/","\n<?php } ?>\n",$str);
	//$str = preg_replace("/\{tag_([^}]+)\}/e", "get_tag('\\1')", $str);
	$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "addquote('<?php echo \\1;?>')",$str);
	$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*?\[(['\"])[^\n]+?\\2\])\s*?\}/","<?php echo \\1;?>",$str);

	$str = "<?php defined('IN_DAEM') or exit('Access Denied'); ?>".$str;
	return $str;
}

//	自动给模板中的数组加单引号。比如模板中的$array[abc]将被替换成$array['abc']
//	@param string $var包含需要替换的数组的字节流
//	@return string $var 替换后的字节流
function addquote($var)
{
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}
?>