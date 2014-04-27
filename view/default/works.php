<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css">
<script type="text/javascript" src="/static/js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/datepicker.css" />
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div style="clear: both"></div>
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a>管理统计(默认显示当前月份数据)</a></li>
					</ul>
					<form action="/list/works">
						用户名
						<input type="text" name="username" maxlength="30">
						开始时间
						<input type="text" name="start" id="date-start" maxlength="30">
						结束时间
						<input type="text" name="end" id="date-end" maxlength="30">
						<input type="submit" class="searchbutton button button-blue" value="查询">
					</form>
				</div>
				<?php foreach ($data['log_action'] as $gkey => $guser){ ?>
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a><?=$gkey?></a></li>
					</ul>
				</div>
				<table cellpadding="0" cellspacing="0" class="torrenttable">
					<tr>
						<th>用户名</th>
						<th>删种</th>
						<th>发种</th>
						<th>播种机发种</th>
						<th>机器人转种</th>
						<th>改种</th>
						<th>审核通过</th>
						<th>审核不通过</th>
						<th>分数</th>
					</tr>
				<?php  foreach ($guser as $ikey => $iuser):?>
					<tr>
						<td><?=$ikey?></td>
						<td><?=$iuser['delete_torrent']?></td>
						<td><?=$iuser['insert_torrent']?></td>
						<td><?=$iuser['api_insert_torrent']?></td>
						<td><?=$iuser['update_bot_torrent']?></td>
						<td><?=$iuser['update_torrent']?></td>
						<td><?=$iuser['audit_pass']?></td>
						<td><?=$iuser['audit_nopass']?></td>
						<td><?=$iuser['score']?></td>
					</tr>
				<?php  endforeach;  ?>
				</table>
				<?php } ?>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
	<!--wp-->
<script>
$(function(){
	$('#date-start,#date-end').datepicker({
		format: 'yyyy-m-d'
	});
	function getMonthFirstDay(){
    	var d=new Date();
		var month=d.getMonth() + 1;
		var year=d.getFullYear();
		current = year + "-" + month + "-01";
    	return current;
   	}
	function getMonthLastDay(){
    	var d=new Date();
    	var currentMonth=d.getMonth();
    	var nextMonth=currentMonth+1;
    	var nextMonthDayOne =new Date(d.getFullYear(),nextMonth,1);
    	var minusDate=1000*60*60*24;
    	d =  new Date(nextMonthDayOne.getTime()-minusDate);
    	month = d.getMonth()+1;
    	current = d.getFullYear() + "-" + month + "-" + d.getDate();
    	return current;
    }
    if (!location.search||location.search.indexOf("start")<0) {
    	$("#date-start").val(getMonthFirstDay());
    	$("#date-end").val(getMonthLastDay());
    };
})
</script>

<?php
include 'footer.php';
?>