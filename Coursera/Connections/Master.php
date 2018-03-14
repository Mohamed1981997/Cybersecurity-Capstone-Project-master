<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Master = "localhost";
$database_Master = "master";
$username_Master = "root";
$password_Master = "";
$Master = mysql_pconnect($hostname_Master, $username_Master, $password_Master) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
