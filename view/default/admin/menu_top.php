<?php foreach($data['menu']['top_menu'] as $top_name => $url): ?>
<li <?php if ($top_name == $data['menu']['top_menu_active']): ?>class="active"<?php endif;?>><a href="<?=$url?>"><?=$top_name?></a></li>
<?php endforeach; ?>