<?php
/**
 * 左侧菜单栏
 * @author BEE 2012-08-22
 */

define('IN_DAEM', TRUE);
include_once './includes/init.php';

//递归查询菜单
function showmenu($pid)
{
	global $db;
	//如果不是管理员，则判断其权限
	if ($_SESSION['UserGroup'] != 1)
	{
		$sql = "select m.* from ".DB_DAEMDB.".db_menu m,".DB_DAEMDB.".db_purview p
				where m_parentid='$pid' and m_id=p_rid and p_gid='".$_SESSION['UserGroup']."' and m_isview='1' UNION select m.* from ".DB_DAEMDB.".db_menu m,".DB_DAEMDB.".db_purview p
				where m_parentid='$pid' and m_isview='2'
				order by m_locality asc";
	}
	else 
	{
		//管理员则显示所有属性为显示的菜单
		$sql="select * from ".DB_DAEMDB.".db_menu where (m_parentid='$pid' and m_isview='1') or (m_parentid='$pid' and m_isview='2')order by m_locality asc";
	}
	$result = $db->query($sql);
	
	$menu_string = '';
	$i = 1;
	while($row = $db->fetch_array($result))
	{
		if($row["m_parentid"] == 0)
		{//如果是 顶级菜单
			if ($menu_string != "")
			{
				$menu_string .= '</ul></div>';
			}
			$menu_string .= '<div class="openitem" id="show'.$i.'" onclick="ClickFun(\'show'.$i.'\' );open_close_item(\'show'.$i.'\');open_close_li(\'item'.$i.'\');">'.L($row['m_name']).'</div><div style="display:block;" id="item'.$i.'"><ul>';

			$menu_string .= showmenu($row["m_id"]);
		}
		else
		{//否则是子菜单
			$menu_string .= '<li class="liimg"><a href="'.$row['m_url'].'" target="right" onClick="ClickFun(\'menuli_'.$row['m_id'].'\' )" id="menuli_'.$row['m_id'].'" >'.L($row['m_name']).'</a></li>';
		}
		$i++;
	}
	return $menu_string;
}

$menu = showmenu(0);
include template('','left');
$db->close();