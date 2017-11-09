<?php
//escape処理
require_once('escape_processor.php');

session_start();
/*
echo "<pre>";
echo var_dump($_SESSION);
echo "</pre>";
*/
echo $_SESSION["userName"];

if(empty($_SESSION["userName"])){
	header("Location:home.php");
}

if(!empty($_GET["key"])){
	if($_GET["key"] == "logout"){
		//sessionの破棄
		$_SESSION = array();
		session_destroy();
		//homeのページに推移
		header("Location:home.php");
	}
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>word cards</title>
	<link rel="stylesheet" href="user.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
	<header>
		<div class="header-left">
			<a href="user.php"><?php echo $_SESSION['userName'] ?></a>
		</div>
		<div class="header-right">
			<a href="wordcard_top.php">wordcard</a>
			<a href="bbs.php">bulletin bord</a>
			<a href="user.php?key=logout">logout</a>
		</div>
	</header>

	<div class="main-wrapper">
		<div class="main-container">
			<div class="heading">
				<div class="heading-container">
					<h1>What are you studying today?</h1>
					<h3>今日は何する？</h3>
				</div>
			</div>
			<div class="boxes">
				<div class="box box1">
					<a href="wordcard_top.php">wardcard</a>
					<p class="circle">単語帳</p>
					<p class="explanation">単語帳をカスタマイズ</p>
				</div>
				<div class="box box2">
					<a href="">reading</a>
					<p class="circle"> 英文</p>
					<p class="explanation">準備中</p>
				</div>
				<div class="box box3">
					<a href="bbs.php">BBS</a>
					<p class="circle">掲示板</p>
					<p class="explanation">英語学習の悩みを相談</p>
				</div>
			</div>
		</div>
	</div>

	<footer>
	</footer>
	<script src="user.js"></script>
</body>
</html>
