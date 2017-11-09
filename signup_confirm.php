<?php

session_start();

//urlのuniqueIdを獲得
$url_uniqueId = $_GET['uniqueId'];

//エラーメッセージを初期化
$errorMessage = "";

//urlにuniqueIdが付与されていた場合
if(!empty($url_uniqueId)){

	try {	//db 接続
		require_once("db_info&connect.php");

		//urlのidと等しいものをセレクト
		$sql = 'select * from practice2 where uniqueId = :id';
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array(':id'=>$url_uniqueId));
		
		//userIdと一致するものがあったら
		if($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			//sessionに入れとく
			$_SESSION['userId'] = $result['id'];
			$_SESSION['userName'] = $result['name'];
			$_SESSION['userPassword'] = $result['password'];
			$_SESSION['userEmail'] = $result['email'];
			
			//flagとregistered_timeをアップデート
			$sql = "update practice2 set flag = 1,registered_time = now() where id = $_SESSION[userId]";
			$dbh->query($sql);
			
			echo '<h4>edit done successfully</h4>';
			
			
		}else{
			$errorMessage = "不正なアクセスです";
		}

	}catch(PDOException $e){
		echo('Connection failed:'.$e->getMessage());
		die();
	}
}else{
	$errorMessage = "送信したurlとは異なります";
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>ログイン</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="stylesheet.css"><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
	<?php require_once("home.php");?>

	<?php if(empty($errorMessage)):?>
	<div class="signup-wrapper">
		<div class="signup-container">
			<div class="close-modal">
				<a href="home.php" class="fa fa-2x fa-times"></a>
			</div>
			<h3 class="signup-heading">登録完了</h3>
			<p class="signup-info">username : <?php echo $_SESSION['userName'];?></p>
			<p class="signup-info">email: <?php echo $_SESSION['userEmail'];?></p>
			<a href="main.php" class="email-btn">ログイン</a>
		</div>
	</div>
	<?php else:?>
	<div class = "signup-wrapper">
		<div class ="signup-container">
			<div class="close-modal">
				<i class="fa fa-2x fa-times"></i>
			</div>
			<p class="warning"><?php echo $errorMessage;?></p>
			<a href="signup_email.php" class="input-btn display-inlineblock">もう一度登録する</a>
		</div>
	</div>
	<?php endif?>
</body>
</html>


