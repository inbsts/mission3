<?php


//dataベース情報、読み込み//tryの後でrequireすること


$dbname = 'データベース名';
$host = 'localhost'; 
$user = 'ユーザー名';
$dbpassword = 'パスワード';
$dns = 'mysql:dbname='.$dbname.';host='.$host.';charset=utf8';


$dbh = new PDO($dns, $user, $dbpassword);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
/*
if ($dbh == null) {
	echo'connection failed<br>';
} else {
	echo'connection succeed<br>';
}
*/


?>