<?php
$connect=mysqli_connect('localhost','root','123','testtable');
if(mysqli_connect_errno($connect)){
	print 'Failed to connect';
}