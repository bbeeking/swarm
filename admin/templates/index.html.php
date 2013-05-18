<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gebo Admin Panel</title>
    
        <!-- Bootstrap framework -->
            <link rel="stylesheet" href="./templates/bootstrap/css/bootstrap.min.css" />
            <link rel="stylesheet" href="./templates/bootstrap/css/bootstrap-responsive.min.css" />
        <!-- gebo blue theme-->
            <link rel="stylesheet" href="./templates/css/blue.css" id="link_theme" />
        <!-- breadcrumbs-->
            <link rel="stylesheet" href="./templates/lib/jBreadcrumbs/css/BreadCrumb.css" />
        <!-- tooltips-->
            <link rel="stylesheet" href="./templates/lib/qtip2/jquery.qtip.min.css" />
        <!-- notifications -->
            <link rel="stylesheet" href="./templates/lib/sticky/sticky.css" />
		<!-- colorbox -->
            <link rel="stylesheet" href="./templates/lib/colorbox/colorbox.css" />
	    <!-- notifications -->
            <link rel="stylesheet" href="./templates/lib/sticky/sticky.css" />    
        <!-- splashy icons -->
            <link rel="stylesheet" href="./templates/img/splashy/splashy.css" />
		
        <!-- main styles -->
            <link rel="stylesheet" href="./templates/css/style.css" />
            
<!-- 屏蔽加载缓慢的样式 -->
<!--            <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans" />-->
	
        <!-- Favicon -->
            <link rel="shortcut icon" href="./templates/favicon.ico" />
		
        <!--[if lte IE 8]>
            <link rel="stylesheet" href="./templates/css/ie.css" />
            <script src="./templates/js/ie/html5.js"></script>
			<script src="./templates/js/ie/respond.min.js"></script>
        <![endif]-->
		
		<script>
			//* hide all elements & show preloader
			document.documentElement.className += 'js';
		</script>
    </head>
    <body>
		<div id="loading_layer" style="display:none"><img src="./templates/img/ajax_loader.gif" alt="" /></div>
		
		<!-- 加载颜色风格选择器 -->
		{template "","style_switcher"}
		<!-- 以下方法同样能加载，但偏向于脚本文件的加载 -->
		<!-- {php include "./templates/style_switcher.html.php"} -->
		
		<div id="maincontainer" class="clearfix">
			
			<!-- 顶部导航栏 header start -->
            {php include "./templates/header.html.php"}
            <!-- 顶部导航栏 header end -->
            
            
            <!-- 主加载程序main content start -->
            <!--
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
								<span class="pull-right label label-important">2 Orders</span>
							</div>
							<table class="table table-striped table-bordered mediaTable" id="dt_d">
								<thead>
									<tr>
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
									<tr>
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
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <!-- 主加载程序main content end -->
            
            {php include $mainPage}
            
            
			<!-- 左侧菜单导航栏 sidebar start -->
            {php include "./sidebar.php"}
            <!-- 左侧菜单导航栏 sidebar end -->
            
            <script src="./templates/js/jquery.min.js"></script>
			<!-- smart resize event -->
			<script src="./templates/js/jquery.debouncedresize.min.js"></script>
			<!-- hidden elements width/height -->
			<script src="./templates/js/jquery.actual.min.js"></script>
			<!-- js cookie plugin -->
			<script src="./templates/js/jquery.cookie.min.js"></script>
			<!-- main bootstrap js -->
			<script src="./templates/bootstrap/js/bootstrap.min.js"></script>
			<!-- bootstrap plugins -->
			<script src="./templates/js/bootstrap.plugins.min.js"></script>
			<!-- tooltips -->
			<script src="./templates/lib/qtip2/jquery.qtip.min.js"></script>
			<!-- jBreadcrumbs -->
			<script src="./templates/lib/jBreadcrumbs/js/jquery.jBreadCrumb.1.1.min.js"></script>
			<!-- sticky messages -->
            <script src="./templates/lib/sticky/sticky.min.js"></script>
			<!-- fix for ios orientation change -->
			<script src="./templates/js/ios-orientationchange-fix.js"></script>
			<!-- scrollbar -->
			<script src="./templates/lib/antiscroll/antiscroll.js"></script>
			<script src="./templates/lib/antiscroll/jquery-mousewheel.js"></script>
            <!-- common functions -->
			<script src="./templates/js/gebo_common.js"></script>
    
			<!-- colorbox -->
			<script src="./templates/lib/colorbox/jquery.colorbox.min.js"></script>
			<!-- datatable -->
			<script src="./templates/lib/datatables/jquery.dataTables.min.js"></script>
			<!-- datatable functions -->
            <script src="./templates/js/gebo_datatables.js"></script>
			<!-- additional sorting for datatables -->
			<script src="./templates/lib/datatables/jquery.dataTables.sorting.js"></script>
			<!-- tables functions -->
			<script src="./templates/js/gebo_tables.js"></script>
	
			<script>
				$(document).ready(function() {
					//* show all elements & remove preloader
					setTimeout('$("html").removeClass("js")',1000);
				});
			</script>
		
		</div>
	</body>
</html>