<?php
include 'header.php';
?>
<body>
<?php
include 'top.php';
?>
<!--top-->

<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>		
		<div class="form_title w150">分类规则</div>
		<div class="form_box w1020">
			<?php if ($data['action_name']=='edit_action'): ?>
			<form action="/admin/category_options/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/category/rules_update/" method="post" name='form1' id="form1">
			<?php endif; ?>
			<table class="table_form">
				<tr>
					<th>分类</th>
					<td>
						<?php if ($data['action_name'] == 'edit_action'): ?>
						<select name="category" id="category">
						<?php else: ?>
						<select name="category" id="category" onchange="location.href='?category='+this.value">
						<?php endif; ?>
							<option value="">=请选择=</option>
							<?php foreach ($data['all_category'] as $category): ?>
							<option value="<?=$category['name_en']?>"<?php if (($data['current_category'] == $category['name_en']) || ($data['action_name'] == 'edit_action' && $data['current_row']['category'] == $category['name_en'])): ?>selected='selected'<?php endif;?>><?=$category['name']?></option>
							<?php endforeach;?>
						</select>
					</td>
					<td></td>
				</tr>
				<?php if (!empty($data['current_category'])): ?>
				<tr>
					<th>规则</th>
					<td><textarea id="rules" name="rules" class="textarea" style="width:700px;height:350px;"><?=$data['current_rules']?></textarea></td>
					<td></td>
				</tr>				
				<tr>
					<th></th>
					<td>		
					<input type="hidden" value="<?=$data['current_id']?>" name="categoryid">
					<a class="btn btn-success" id="submit" href="javascript:void();" onclick="submit_upload()">保存</a>
					</td>
					<td></td>
				</tr>				
				<?php endif; ?>
			</table>
			</form>
		</div><!--form_box-->
		<div class="blank_box20"></div>
		

	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->

<script type="text/javascript" src="/static/xheditor/xheditor.min.js"></script>
<script type="text/javascript" src="/static/xheditor/xheditor_plugins/ubb.min.js"></script>
<script type="text/javascript" src="/static/xheditor/xheditor_lang/zh-cn.js"></script>

<script type="text/javascript">
function change_category()
{
	category = $('#category').val();
	location.href="/admin/category_options/index"
}
function submit_upload()
{
	$.post("/admin/category/rules_update/",$("#form1").serialize(), function(e){
		alert(e);
	});

}
var editor;
$(function(){
	function editor_init()
	{
		var tools = 'Fontface,FontSize,Bold,Italic,Underline,Strikethrough,|,FontColor,BackColor,|,SelectAll,Removeformat,|,Align,List,Outdent,Indent,|,Link,Unlink,Img,Hr,Table,Source,Preview,About,newupload';
		var upload = '';
		editor = $('#rules').xheditor({tools:tools,forcePtag:false,beforeSetSource:ubb2html,beforeGetSource:html2ubb,shortcuts:{'ctrl+enter':submit_upload},html5Upload:false});				
	}
	editor_init();
})
</script>

<?php
include 'footer.php';
?>
