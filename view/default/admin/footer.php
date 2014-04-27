<div id="footer">
<?=$this->lang('page_execute_time')?>:<?=$data['page_execute_time']?><br />
Query SQL Count: <?php echo  $data['all_sql_count'];?><br />
Query SQL: <?php print_r($data['all_sql']);?>
</div>
<a href="javascript:void(0);" class="back-to-top" title="回到顶部">▲</a>
<script>
$(function(){
	$(window).scroll(function() {
		if ($(this).scrollTop() > 150) {
			$('.back-to-top').fadeIn(100);
		} else {
			$('.back-to-top').fadeOut(100);
		}
	});
	$('.back-to-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, 500);
	})
})
</script>
</body>
</html>