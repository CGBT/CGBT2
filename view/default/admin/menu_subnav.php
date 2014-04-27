<?php if (count($data['menu']['subnav_menu']) > 1):?>
<ul class="subnav">
	<?php foreach($data['menu']['subnav_menu'] as $subnav_name => $url): ?>
	<li<?php if ($subnav_name == $data['menu']['subnav_menu_active']):?> class="active"<?php endif;?>><a href="<?=$url?>"><?=$subnav_name?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif;?>