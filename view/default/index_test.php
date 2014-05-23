<?php
include 'include_header.php';
?>
<body>
<?php
foreach ($data['setting'] as $key => $value)
{
	echo "$key:$value";
}
echo $this->lang('forums_user_not_exist', 'abcde');
?>
<?php

include 'include_footer.php';
?>
