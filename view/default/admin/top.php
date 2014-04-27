<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<a class="brand" href="/">CGBTSource</a>
		<div class="container wp95">
			<ul class="nav">
				<?php
				include 'menu_top.php';
				?>
			</ul>
			<div class="btn-group pull-right">
				<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
					<?=$data['username']?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li>
						<a href='/'>首页</a>
					</li>
					<li class="divider"></li>
					<li>
						<a href='/user/logout/'>退出</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>