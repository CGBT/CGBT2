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
						<li class="active"><a href="###">发出的奖励积分排行</a></li>
					</ul>
					<form action="/list/topaward">
						开始时间
						<input type="text" name="start" id="date-start" maxlength="30">
						结束时间
						<input type="text" name="end" id="date-end" maxlength="30">
						<input type="submit" class="searchbutton button button-blue" value="查询">
					</form>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable"
					style="margin-bottom: 20px;">
					<tr>
						<th>用户名</th>
						<th>时间</th>
						<th>积分类型</th>
						<th>积分数量</th>
					</tr>
					<?php foreach ($data['rows_credits'] as $key => $row): ?>
					<tr>
						<td><?=$row['operator_username']?></td>
						<td><?php echo date("Y-m-d H:i:s",$row['createtime']); ?></td>
						<td><?=$row['field']?></td>
						<td><?=$row['total']?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<!-- end #mainContent -->


		</div>
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