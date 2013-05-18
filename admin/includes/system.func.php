<?php
/**
* 用户管理权限函数文件
* @author BEE 2012-08-22
*/

//显示所有群组
function ShowGroup()
{
	global $db;
	$array = array();
	$sql = "select * from ".DB_DAEMDB.".db_admingroup";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$array['g_id'] = $rows['g_id'];
		$array['g_name'] = $rows['g_name'];
		$array['g_description'] = $rows['g_description'];
		$arrays[] = $array;
	}
	return $arrays;
}

//显示所有管理员
function ShowAdminUser()
{
	global $db;
	$array = array();
	$sql = "select a.*,b.g_description from ".DB_DAEMDB.".db_adminuser as a left join ".DB_DAEMDB.".db_admingroup as b on a.a_gid=b.g_id";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$array['id'] = $rows['a_id'];
		$array['account'] = $rows['a_account'];
		$array['department'] = $rows['a_department'];
		$array['username'] = $rows['a_username'];
		$array['number'] = $rows['a_number'];
		$array['tel'] = $rows['a_tel'];
		$array['logip'] = $rows['a_logip'];
		$array['logtime'] = date("Y-m-d H:i:s",$rows['a_logtime']);
		$array['ispermit'] = $rows['a_ispermit'];
		$array['regtime'] = date("Y-m-d H:i:s",$rows['a_regtime']);
		$array['g_name'] = $rows['g_description'];
		$arrays[] = $array;
	}
	return $arrays;
}
	
//递归显示菜单
function ShowMenuList($parentid)
{
	global $db;
	$sql = "select * from ".DB_DAEMDB.".db_menu where m_parentid='".$parentid."'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		if ($rows['m_isview'] == '1')
			$show = '显示';
		elseif ($rows['m_isview'] == '2')
			$show = '<font color="#FDHFEE">所有可见</font>';
		else
			$show = '<font color="#ff0000">不显示</font>';
		//如果是父级分类
		if ($rows['m_parentid'] == 0) {
			$string .= '<tr><td bgcolor="#FFEEEE" height="25">&nbsp;<img src="../images/menu_plus.gif">&nbsp;'.L($rows["m_name"]).'</td><td bgcolor="#FFEEEE">'.$rows["m_url"].'</td><td bgcolor="#FFEEEE" align="center">'.$rows["m_locality"].'</td><td bgcolor="#FFEEEE" align="center">'.$show.'</td><td bgcolor="#FFEEEE" align="center"><a href="edit_menu.php?id='.$rows["m_id"].'">修改</a>&nbsp;&nbsp;<a href="del.php?id='.$rows["m_id"].'&type=menu&name='.urlencode($rows['m_name']).'" onclick="if(confirm(\'你真的要删除《'.L($rows["m_name"]).'》菜单吗？\')){return true;}else{return false;}">删除</a></td></tr>';
			$string .= ShowMenuList($rows['m_id']);
		}
		//如果为子分类
		else {
			$string .= '<tr><td bgcolor="#FFFFFF" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../images/menu_arrow.gif">&nbsp;'.L($rows["m_name"]).'</td><td bgcolor="#FFFFFF">'.$rows["m_url"].'</td><td bgcolor="#FFFFFF" align="center">'.$rows["m_locality"].'</td><td bgcolor="#FFFFFF" align="center">'.$show.'</td><td bgcolor="#FFFFFF" align="center"><a href="edit_menu.php?id='.$rows["m_id"].'">修改</a>&nbsp;&nbsp;<a href="del.php?id='.$rows["m_id"].'&type=menu&name='.urlencode($rows['m_name']).'" onclick="if(confirm(\'你真的要删除《'.L($rows["m_name"]).'》菜单吗？\')){return true;}else{return false;}">删除</a></td></tr>';
		}
	}
	return $string;
}

//显示菜单顶级分类
function ShowMenu()
{
	global $db;
	$sql = "select m_id,m_name from ".DB_DAEMDB.".db_menu where m_parentid='0'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$array['id'] = $rows['m_id'];
		$array['name'] = L($rows['m_name']);
		$arrays[] = $array;
	}
	return $arrays;
}

//显示资源
function ShowResource()
{
	global $db;
	$sql = "select r_id,r_label,r_name from ".DB_DAEMDB.".db_resource where r_parentid='0'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		
	}
}

//显示资源
function ShowResourceTop()
{
	global $db;
	$sql = "select r_id,r_name from ".DB_DAEMDB.".db_resource where r_parentid='0'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$array['id'] = $rows['r_id'];
		$array['name'] = $rows['r_name'];
		$arrays[] = $array;
	}
	return $arrays;
}

//显示资源
function ShowResourceList($parentid,$string='')
{
	global $db;
	$sql = "select * from ".DB_DAEMDB.".db_resource where r_parentid='".$parentid."'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$string .= '<tr><td bgcolor="#FFFFFF" height="25">'.$rows['r_name'].'&nbsp;（<span style="font-size:14px;color:#ff0000;">'.$rows['r_label'].'</span>）&nbsp;&nbsp;<a href="resource_list.php?id='.$rows['r_id'].'">修改</a>&nbsp;&nbsp;<a href="del.php?id='.$rows['r_id'].'&type=resource" onclick="if(!confirm(\'您真的要删除《'.$rows['r_name'].'》资源吗？删除后将无法恢复。\')){return false;}">删除</a></td></tr>';
		$string .= '<tr><td bgcolor="#FFFFFF" height="25"><table width="96%" border="0" cellspacing="1" cellpadding="2"  align="center"><tr>';
		$sql1 = "select * from ".DB_DAEMDB.".db_resource where r_parentid='".$rows['r_id']."'";
		$result1 = $db->query($sql1);
		$i = 0;
		while ($rows1 = $db->fetch_array($result1)) {
			$string .= '<td width="33%" height="25">'.$rows1['r_name'].'&nbsp;（<span style="font-size:14px;color:#ff0000;">'.$rows1['r_label'].'</span>）&nbsp;&nbsp;<a href="resource_list.php?id='.$rows1['r_id'].'">修改</a>&nbsp;&nbsp;<a href="del.php?id='.$rows1['r_id'].'&type=resource" onclick="if(!confirm(\'您真的要删除《'.$rows1['r_name'].'》资源吗？删除后将无法恢复。\')){return false;}">删除</a></td>';
			$i++;
			if ($i % 3 == 0) {
				$string .= '</tr><tr>';
			}
		}
		$string .= '</tr></table></td></tr>';
	}
	return $string;
}

//显示权限分配
function ShowPurview($parentid,$gid,$date)
{
	global $db;
	$sql = "select * from ".DB_DAEMDB.".db_menu where m_parentid='".$parentid."'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$checked = '';
		if ($date != '') {
			foreach ($date as $val) {
				if ($val['p_rid'] == $rows['m_id']) {
					$checked = 'checked';
				}
			}
		}
		$string .= '<tr><td bgcolor="#FFFFFF" height="25"><input type="checkbox" name="purview[]" value="'.$rows['m_id'].'" '.$checked.' onclick="selectpurview(this)" />&nbsp;<b>'.L($rows['m_name']).'</b></td></tr>';
		$string .= '<tr><td bgcolor="#FFFFFF" height="25"><table width="96%" border="0" cellspacing="1" cellpadding="2"  align="center"><tr>';
		$sql1 = "select * from ".DB_DAEMDB.".db_menu where m_parentid='".$rows['m_id']."'";
		$result1 = $db->query($sql1);
		$i = 0;
		while ($rows1 = $db->fetch_array($result1)) {
			$checkeds = '';
			if ($date != '') {
				foreach ($date as $val) {
					if ($val['p_rid'] == $rows1['m_id']) {
						$checkeds = 'checked';
					}
				}
			}
			$string .= '<td width="33%" height="25"><input type="checkbox" name="purview'.$rows['m_id'].'[]" value="'.$rows1['m_id'].'" '.$checkeds.' />&nbsp;'.L($rows1['m_name']).'</td>';
			$i++;
			if ($i % 3 == 0) {
				$string .= '</tr><tr>';
			}
		}
		$string .= '</tr><tr><td height="25" colspan="3"><input type="checkbox" name="all'.$rows['m_id'].'" id="all'.$rows['m_id'].'" value="" onclick="allselect('.$rows['m_id'].',this)" />&nbsp;全选</td></tr></table></td></tr>';
	}
	return $string;
}

//显示资源
function ShowPurviewInfo($gid)
{
	global $db;
	$sql = "select p_rid from ".DB_DAEMDB.".db_purview where p_gid='".$gid."'";
	$result = $db->query($sql);
	while ($rows = $db->fetch_array($result)) {
		$array['p_rid'] = $rows['p_rid'];
		$arrays[] = $array;
	}
	return $arrays;
}
