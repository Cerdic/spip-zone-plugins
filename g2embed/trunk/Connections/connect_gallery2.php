<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_gallery2 = "localhost";
$database_gallery2 = "philgris";
$username_gallery2 = "root";
$password_gallery2 = "";
$connect_gallery2 = mysql_pconnect($hostname_gallery2, $username_gallery2, $password_gallery2) or trigger_error(mysql_error(),E_USER_ERROR); 
?>