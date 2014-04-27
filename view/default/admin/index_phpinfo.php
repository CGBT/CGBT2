<?php
include 'header.php';
?>
<body>
<?php
include 'top.php';
?>
<!--top-->
<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>		
		<div class="form_title w100">phpinfo</div>		
		<div class="form_box w1020">		

<?php if ($data['user']['is_admin']): ?>
<iframe src="/admin/index/iframe_phpinfo" style="width:100%;height:600px;border:0"></iframe>
<?php else: ?>
<?php endif;?>
			
		</div><!--form_box-->
		<div class="blank_box20"></div>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>