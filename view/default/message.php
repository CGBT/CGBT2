<?php
include 'header.php';
?>

<div id="wp" class="wp">
<div id="ct" class="ptm wp w cl">

	<div class="nfl">
	<div class="f_c altw">
		<div id="messagetext" class="alert_<?php if ($msg_data['error']===false){echo "right";}elseif($msg_data['error']===true) {echo "error";}else{echo "info";}?>">
		<p><?php echo $msg_data['msg']; ?></p>
		<?php if ($msg_data['error']): ?>
		<p class="alert_btnleft">
			<?php if (!isset($msg_data['return_url'])): ?>
			<a href="javascript:history.back()">[ 点击这里返回上一页 ]</a>
			<?php elseif ($msg_data['return_url'] === false): ?>
			<?php else:?>
			<a href="<?=$msg_data['return_url']?>">[ 点击跳转 ]</a>
			<?php endif;?>
		</p>
		<?php else:?>
			<?php if (!isset($msg_data['return_url']) || $msg_data['return_url']=='default'): ?>
			<a href="javascript:history.back()">[ 点击这里返回上一页 ]</a>
			<?php elseif ($msg_data['return_url'] === false): ?>
			<?php else:?>
			<a href="<?=$msg_data['return_url']?>">[ 点击跳转 ]</a>
			<?php endif;?>
		<?php endif;?>
		</div>
	</div></div>

</div></div><!--wp-->

<?php
include 'footer.php';
?>