<meta charset="utf-8" />
<a href="javascript:void(0)" class="sidebar_switch on_switch ttip_r" title="Hide Sidebar">Sidebar switch</a>
<div class="sidebar">				
	<div class="antiScroll">
		<div class="antiscroll-inner">
			<div class="antiscroll-content">
				<div class="sidebar_inner">
					<form action="index.php?uid=1&amp;page=search_page" class="input-append" method="post" >
						<input autocomplete="off" name="query" class="search_query input-medium" size="16" type="text" placeholder="Search..." /><button type="submit" class="btn"><i class="icon-search"></i></button>
					</form>
					<div id="side_accordion" class="accordion">
						
						<!--
						<div class="accordion-group">
							<div class="accordion-heading">
								<a href="#collapseThree" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
									<i class="icon-user"></i> 账号管理
								</a>
							</div>
							<div class="accordion-body collapse in" id="collapseThree">
								<div class="accordion-inner">
									<ul class="nav nav-list">
										<li><a href="javascript:void(0)">成员列表</a></li>
										<li><a href="javascript:void(0)">成员组</a></li>
										<li class="active"><a href="javascript:void(0)">用户列表</a></li>
										<li><a href="javascript:void(0)">用户组</a></li>
									</ul>
									
								</div>
							</div>
						</div>
						-->
						
						<!-- 偏向于jquery的输出方法1 -->
						<!-- {$sidebarDataStr} -->
						
						<!-- 更为适合于模版分离的输出办法 -->
						{php $i=1}
						{loop $authModAry $key=>$sonModAry}
						<div class="accordion-group">
							<div class="accordion-heading">
								<a href="#{$i}" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
									<i class="{$sidebarIconGlyphs[$key]}"></i> {L($key)}
								</a>
							</div>
							
							<div class="accordion-body collapse in" id="{$i}">
								<div class="accordion-inner">
									<ul class="nav nav-list">
									{loop $sonModAry $k=>$v}
										{if $k==$_SESSION[menuChoose][mod]}
										<li class="active">
										{else}
										<li>
										{/if}
											<!-- <a href="./sidebar.php?Mod={$key}_{$k}">{L($k)}</a></li> -->
											<a href="?Mod={$key}_{$k}">{L($k)}</a></li>
									{/loop}
									</ul>
								</div>
							</div>
						</div>
						{php $i++}
						{/loop}
						
					</div>
					
					<div class="push"></div>
				</div>
				   
				<div class="sidebar_info">
					<ul class="unstyled">
						<li>
							<span class="act act-warning">65</span>
							<strong>新评论</strong>
						</li>
						<li>
							<span class="act act-success">10</span>
							<strong>新文章</strong>
						</li>
						<li>
							<span class="act act-danger">85</span>
							<strong>新会员注册人数</strong>
						</li>
					</ul>
				</div> 
			
			</div>
		</div>
	</div>
</div>