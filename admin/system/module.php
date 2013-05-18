<?php
/**
 * 系统设置下的模块管理
 * 
 * @name system/module.php
 * @author BEE LEUNG 2013-03-05
 */

define('IN_DAEM', true);
require(dirname(dirname(__FILE__)) . '/includes/init.php');

//查询所有的模块信息,依次按照父级模块id和order升序排列
$modInfoAry = array();
$sql = "select * from ".DB_DAEMDB.".per_module order by `per_module_id`,`order` asc";
$query = $db->query($sql);
while ($row = $db->fetch_array($query))
{
	if ($row['per_module_id'] == 0)
	{
		$modInfoAry[$row['module_id']][$row['per_module_id']] = $row;
	}
	else 
	{
		$modInfoAry[$row['per_module_id']][$row['order']] = $row;
	}
}

include template('system','module');




















/**
 * 递归显示菜单模块
 * 
 * @param Int $parentId
 * @return String $string
 */
function ShowModuleList($parentId)
{
	global $db;
	$sql = "select * from ".DB_DAEMDB.".per_module where per_module_id='".$parentId."'";
	$result = $db->query($sql);
	while ($row = $db->fetch_array($result)) 
	{
		if ($row['allow_access'] == '1')
			$show = '允许访问';
		else
			$show = '<font color="#ff0000">不允许访问</font>';
			
		//如果是父级分类
		if ($row['per_module_id'] == 0) {
			$moduleString .= '<tr>
							<td>'.$row["module_sign"].'</td>
							<td>'.$row["module_sign"].'</td>
							<td>'.$row["order"].'</td>
							<td>'.$row["allow_access"].'</td>
							<td>
								<a href="edit_menu.php?id='.$row["module_id"].'" title="Edit"><i class="splashy-document_letter_edit"></i></a>
								<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
								<a href="#" title="Remove" onclick="if(confirm(\'你真的要删除《'.$row["module_sign"].'》菜单吗？\')){return true;}else{return false;}"><i class="splashy-document_letter_remove"></i></a>
							</td>
						</tr>';
			$moduleString .= ShowModuleList($row['per_module_id']);
		}
		//如果为子分类
		else {
			$moduleString .= '<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row["module_sign"].'</td>
							<td>'.$row["module_sign"].'</td>
							<td>'.$row["order"].'</td>
							<td>'.$row["allow_access"].'</td>
							<td>
								<a href="edit_menu.php?id='.$row["module_id"].'" title="Edit"><i class="splashy-document_letter_edit"></i></a>
								<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
								<a href="#" title="Remove" onclick="if(confirm(\'你真的要删除《'.$row["module_sign"].'》菜单吗？\')){return true;}else{return false;}"><i class="splashy-document_letter_remove"></i></a>
							</td>
						</tr>';
		}
		echo $moduleString;die;
	}
	return $moduleString;
}