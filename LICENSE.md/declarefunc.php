<?php
function page_header($color,$title) {
print '<html><head><title>Welcome to ' .$title . '</title></head>'; 
print '<body bgcolor="' . $color . '">';
}
error_reporting(E_ALL & ~E_NOTICE);

if($_POST['user']){
page_header('cc00cc','form.');
print "Welcome,". $_POST['user']; 
}else{
page_header('33CAFF','my site !!!');
print <<<_HTML_
<form method="post" action="$_SERVER[PHP_SELF]">
Your Name: <input type="text" name="user" />
<br/>
<button type="submit">Welcome</button>
</form>
_HTML_;
}

page_footer();
function page_footer() {
print '<hr>Thanks for visiting.'; 
print '</body></html>';
}
?>
</b>
</body>
</html> 