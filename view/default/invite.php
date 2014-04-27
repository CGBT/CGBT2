<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div id="wp" class="wp"><div id="ct" class="ptm wp w cl">

<div class="mn" id="main_message">
	<div class="bm">
		<div class="bm_h bbs">
		<h3 class="xs2">我的邀请码</h3>
		</div>
	<div>

	<div id="main_messaqge_layer">
		<div id="layer_login">
			<form method="post" name="frm_invite" id="frm_invite" class="cl" onsubmit="" action="/invite/buy">
				<div class="c cl">
					<div class="rfm">
					<table>
					<tr>
					<th><label for="username">说明:</label></th>
					<td>保种积分兑换邀请码，当前兑换比率为 <?=$data['current_price']?> 保种积分可兑换一个邀请码。</td>
					<td class="tipcol"></td>
					</tr>
					</table>
					</div>

					<div class="rfm">
					<table>
					<tr>
					<th><label for="username">兑换数量:</label></th>
					<td>
					<select name="invitecount" id="invitecount" style="width:50px;">
					<option value='1'>1</option>
					</select></td>
					<td class="tipcol">
					<input type="hidden" name="issubmit" value="1">
					<button class="pn pnc" type="button" name="btn" onclick="submit_form('frm_invite');"><strong>提交</strong></button>
					</td>
					</tr>
					</table>
					</div>					
				</div>
			</form>

<div id="mainContent">
<table cellspacing="0" cellpadding="0" class="torrenttable">
	<tr>
		<th width="30">ID</th>
		<th width="50">用户</th>
		<th width="200">邀请码</th>
		<th width="80">兑换价格</th>
		<th width="80">兑换时间</th>
		<th width="80">过期时间</th>
		<th width="50">使用人</th>
		<th width="80">使用时间</th>
		<th width="80">通过新手考核</th>
	</tr>
	<?php foreach ($data['rows_invite'] as $key => $row): ?>
	<tr>
		<td><?=$key+1?></td>
		<td><?=$row['username']?></td>
		<td><?=$row['code']?></td>
		<td><?=$row['price']?></td>
		<td><?=date("Y-m-d H:i:s", $row['createtime'])?></td>
		<td><?=date("Y-m-d H:i:s", $row['expiretime'])?></td>
		<td><a target='_blank' href='/user/<?=$row['used_uid']?>/'><?=$row['used_username']?></a></td>
		<td><?=$row['updatetime']>0 ? date("Y-m-d H:i:s", $row['updatetime']) : '';?></td>
		<td>
		<?php if (!empty($row['used_username'])):?>
		<?php if ($data['kaohe_data']['pass_data'][$row['used_username']] != '-1' && !empty($row['used_uid']) ):?>
		<?php if (!empty($data['kaohe_data']['pass_data'][$row['used_username']])):?>是<?php else:?>否<?php endif; endif;?>
		<?php endif;?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
</div>

		</div><!--layer_login-->
	</div><!--main_message_layer-->
</div><!--main_message-->
</div></div><!--wp-->

<?php
include 'footer.php';
?>