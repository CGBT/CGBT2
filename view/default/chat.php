<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div style="clear:both"></div>
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a><?=$data['room_name']?></a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<div style="clear:both"></div>

				<?php include 'include_chat.php'; ?>
			<!-- end #mainContent -->

		
		</div>
	</div>
	<!--wp-->


<?php
include 'include_chat_js.php';
include 'footer.php';
?>