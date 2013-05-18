<!-- 主加载程序main content start -->
            
<div id="contentwrapper">
<div class="main_content">
    
    <nav>
        <div id="jCrumbs" class="breadCrumb module">
            <ul>
                <li>
                    <a href="#"><i class="icon-home"></i></a>
                </li>
                <li>
                    <a href="#">账号管理</a>
                </li>
                <li>
                    <a href="#">用户列表</a>
                </li>
                <li>show</li>
            </ul>
        </div>
    </nav>
    
	<div class="row-fluid">
		<div class="span12">
			<div class="heading clearfix">
				<h3 class="pull-left">管理员列表</h3>
				<span class="pull-right label label-important">{$countNum} Orders</span>
			</div>
			<table class="table table-striped table-bordered mediaTable" id="dt_d">
				<thead>
					<tr>
						<th class="optional">id</th>
						<th class="optional">用户名</th>
						<th class="essential persist">姓名</th>
						<th class="optional">群组</th>
						<th class="optional">部门</th>
						<th class="essential">工号</th>
						<th class="essential">电话</th>
						
						<th class="essential">最后登录时间</th>
						<th class="essential">最后登录ip</th>
						<th class="essential">是否被禁止</th>
						<th class="essential">注册时间</th>
						<th class="essential">操作</th>
					</tr>
				</thead>
				<tbody>
					{loop $userInfoAry $key=>$val}
					<tr>
						<td>{$val['uid']}</td>
						<td>{$val['username']}</td>
						<td>{$val['name']}</td>
						<td>{$val['rolename']}</td>
						<td>{$val['department']}</td>
						<td>{$val['number']}</td>
						
						<td>{$val['tel']}</td>
						<td>{$val['lastLoginDate']}</td>
						<td>{$val['log_ip']}</td>
						<td>{$val['is_permit']}</td>
						<td>{$val['lastLoginDate']}</td>
						
						<td>
							<a href="#" title="Edit"><i class="splashy-document_letter_edit"></i></a>
							<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
							<a href="#" title="Remove"><i class="splashy-document_letter_remove"></i></a>
						</td>
					</tr>
					{/loop}
					<!--<tr>
						<td>1</td>
						<td>bbeeking</td>
						<td>梁钺锋</td>
						<td>超级管理员</td>
						<td>研发部</td>
						<td>001</td>
						
						<td>13824168827</td>
						<td>2013-02-15</td>
						<td>127.0.0.1</td>
						<td>否</td>
						<td>2013-02-15</td>
						
						<td>
							<a href="#" title="Edit"><i class="splashy-document_letter_edit"></i></a>
							<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
							<a href="#" title="Remove"><i class="splashy-document_letter_remove"></i></a>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td>minileung</td>
						<td>梁敏仪</td>
						<td>策划</td>
						<td>美术部</td>
						<td>002</td>
						
						<td>13824168827</td>
						<td>2013-02-14</td>
						<td>127.0.0.2</td>
						<td>是</td>
						<td>2013-02-15</td>
						
						<td>
							<a href="#" title="Edit"><i class="splashy-document_letter_edit"></i></a>
							<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
							<a href="#" title="Remove"><i class="splashy-document_letter_remove"></i></a>
						</td>
					</tr>
					-->
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>

<!-- 主加载程序main content end -->