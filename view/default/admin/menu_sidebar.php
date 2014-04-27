<ul>
<?php foreach ($data['menu']['side_menu'] as $side_name => $url): ?>
<li<?php if ($side_name == $data['menu']['side_menu_active']):?> class='active'<?php endif;?>><a href="<?=$url?>"><?=$side_name?></a></li>
<?php endforeach; ?>
</ul>