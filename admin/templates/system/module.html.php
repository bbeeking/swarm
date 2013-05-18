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
			<h3 class="heading">模块管理</h3>
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab_br1" data-toggle="tab">模块列表</a></li>
					<li><a href="#tab_br2" data-toggle="tab">模块添加</a></li>
					<li><a href="#tab_br3" data-toggle="tab">Section 3</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_br1">
						 <table class="table table-bordered mediaTable" >
							<thead>
								<tr>
									<th class="essential">模块名称</th>
									<th class="essential">模块标识</th>
									<th class="essential">显示位置</th>
									<th class="essential">是否显示</th>
									<th class="essential">操作</th>
								</tr>
							</thead>
							<tbody>
								{loop $modInfoAry $modInfo}
									{loop $modInfo $key=>$val}
									<tr>
										<td>{if $key == 0}<i class="{$sidebarIconGlyphs[$val['module_sign']]}"></i>&nbsp;&nbsp;{else}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/if}{L($val['module_sign'])}</td>
										<td>{$val['module_sign']}</td>
										<td>{$val['order']}</td>
										<td>{$val['allow_access']}</td>
										<td>
											<a href="#" title="Edit"><i class="splashy-document_letter_edit"></i></a>
											<a href="#" title="Accept"><i class="splashy-document_letter_okay"></i></a>
											<a href="#" title="Remove"><i class="splashy-document_letter_remove"></i></a>
										</td>
									</tr>
									{/loop}
								{/loop}
							</tbody>
						</table>
					</div>
					<div class="tab-pane" id="tab_br2">
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et tellus felis, sit amet interdum tellus. Suspendisse sit amet scelerisque dui. Vivamus faucibus magna quis augue venenatis ullamcorper. Proin eget mauris eget orci lobortis luctus ac a sem. Curabitur feugiat, eros consectetur egestas iaculis,
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et tellus felis, sit amet interdum tellus. Suspendisse sit amet scelerisque dui. Vivamus faucibus magna quis augue venenatis ullamcorper. Proin eget mauris eget orci lobortis luctus ac a sem. Curabitur feugiat, eros consectetur egestas iaculis,
						</p>
					</div>
					<div class="tab-pane" id="tab_br3">
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et tellus felis, sit amet interdum tellus. Suspendisse sit amet scelerisque dui. Vivamus faucibus magna quis augue venenatis ullamcorper. Proin eget mauris eget orci lobortis luctus ac a sem. Curabitur feugiat, eros consectetur egestas iaculis,
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
</div>

<!-- 主加载程序main content end -->