<?php
include 'header.php';
?>
<body>
<?php
include 'top.php';
?>
<!--top-->

<script type="text/javascript" src="/static/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/static/js/jquery-powerFloat-min.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/datepicker.css" />
<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>
		<div class="form_box">
		<form action="/admin/index/logscron/" method="get" name='form1' id="form1" class="form-inline" style="margin:10px;">
			<div>
			controller 
			<select id="controller" name="controller" class="input-medium">
			<option value="" <?php if (empty($data['controller'])) echo "selected='selected'"; ?>>全部</option>
			<option value="cron_controller" <?php if($data['controller'] == 'cron_controller') echo "selected='selected'"; ?>>cron_controller</option>
			</select> 
			method 
			<select id="method" name="method" class="input-large">
			<option value="" <?php if (empty($data['method'])) echo "selected='selected'"; ?>>全部</option>
			<?php foreach ($data['dict_method'] as $method): ?>
			<option value="<?=$method?>" <?php if($data['method']==$method) echo "selected='selected'"; ?>><?=$method?></option>
			<?php endforeach;?>
			</select> 
			<input type="checkbox" name="real_exec" value="1" <?php if ($data['real_exec']) echo "checked='checked'"; ?>"> 确实执行 </input>
			<button class="btn btn-success" id="btn_submit" type="submit">搜索</button>
			</div>
		</form>
		</div>
		<div style="height:28px;margin-top:10px;">
		<div class="table_title" style="float:left;width:100px;"><?=$data['list_count']?>个记录</div>
		<div class="pager pull-left" style="margin:0">
				<?php echo $data['pager']->output; ?>
		</div>
		</div>


		<div class="form_box">
		
		</div>
				
		<table class="datalist table-hover bd_line" >
			<tr>
				<th>controller</th>
				<th>method</th>
				<th>开始时间</th>
				<th>结束时间</th>
				<th>执行时长</th>
				<th>执行结果</th>
				<th>强制执行</th>
				<th>执行间隔</th>
				<th>确实执行</th>
				<th>手工执行</th>
			</tr>
			<?php foreach ($data['list_rows'] as $row):?>
			<tr>

				<td><?=$row['controller']?></td>
				<td><?=$row['method']?></td>
				<td><?=date("Y-m-d H:i:s", $row['createtime'])?></td>
				<td><?=date("Y-m-d H:i:s", $row['endtime'])?></td>
				<td><?=$row['endtime'] - $row['createtime']?></td>
				<td><?=$row['exec_result']?></td>
				<td><?=$row['force']?></td>
				<td><?=$row['interval']?></td>
				<td><?=$row['real_exec']?></td>
				<td><a href='<?='/'.str_replace("_controller", '', $row['controller']).'/?force_run='.$row['method']?>' target='_blank'>执行</a></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<div class="pager">
				<?php echo $data['pager']->output; ?>
		</div>

	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->

<?php
include 'footer.php';
?>