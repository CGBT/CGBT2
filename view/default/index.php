<?php
include 'header.php';
?>
<div id="wp" class="wp">
<div id="ct" class="ct2_a wp cl box-inside">

<?php if (!empty($data['kaohe_msg'])):?>
<div style="text-align:center;padding:20px">
<?=$data['kaohe_msg']?>
</div>
<?php endif;?>

<ul class="nav">
    <li class="active"><a>聊天广场</a></li>
</ul>

<?php include 'include_chat.php'; ?>

<ul class="nav">
    <li class="active"><a href="#">站点数据（更新时间：<?=date("Y-m-d H:i", $data['site_stat']['createtime'])?>）</a></li>
	<li><?php if ($data['is_ipv6']): ?>
	<a href="#">您正在使用IPv6地址 <?=$data['ip']?> 访问本站!</a>
	<?php else: ?>
	<a href="#" style="color:red;font-weight:bold;">提示：您的ip为 <?=$data['ip']?> , 您没有正确安装IPv6网络。</a>
	<?php endif; ?></li>
</ul>

<table cellspacing="0" cellpadding="0" class="tfm" style="border:1px;">
    <tr>
    <th>用户总数</th>
    <td><?=$data['site_stat']['total_user_count']?></td>
    <th>种子总数</th>
    <td><?=$data['site_stat']['torrent_count']?></td>
    </tr>
    <tr>
    <th>在线用户数</th>
    <td><?=$data['site_stat']['online_user']?></td>
    <th>当前种子数</th>
    <td><?=$data['site_stat']['active_torrent_count']?></td>
    </tr>
    <tr>
    <th>上传总数</th>
    <td><?=$data['site_stat']['seed_peer_count']?></td>
    <th>下载总数</th>
    <td><?=$data['site_stat']['leech_peer_count']?></td>
    </tr>
    <tr>
    <th>保种用户数</th>
    <td><?=$data['site_stat']['seeder_count']?></td>
    <th>种子总容量</th>
    <td><?=$data['site_stat']['total_size_text']?></td>
    </tr>
    <tr>
    <?php 
    $i=0;
    foreach($data['users'] as $user):
    $i++;
    ?>
    <th><?=$user['name']?></th>
    <td>
        <div class="progress progress-blue" style="width:90%;" title="<?=$user['name']?>：<?=$user['count']?>人">
            <span data-wait="<?=100*$user['count']/$data['site_stat']['total_user_count']?>"></span>
        </div>
    </td>
    <?php if ($i % 2 == 0): ?>
    </tr><tr>
    <?php endif;?>
    <?php endforeach;?>
    </tr>
</table>

</div>
</div><!--wp-->

<script type="text/javascript">
$(function(){
    $(".progress span").each(function(){
        var value = $(this).attr("data-wait");
        $(this).animate({width:value+"%"},1500);
    })
})
</script>


<?php
include 'include_chat_js.php';
include 'footer.php';
?>