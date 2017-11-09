<?php

// セッション開始
session_start();

//post定義
$signup = $_POST['signup'];
$userName = $_POST['userName'];
$password = $_POST['password'];
$passCheck = $_POST['passCheck'];
//メール
$email = $_POST["email"];

//エラーメッセージの初期化
$errors = array();
$signupMessage = "";


//登録ボタンが押されたときの処理
if(isset($signup)){
	//登録情報記入
	if(empty($_POST["confirmation"])){

		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		//echo "<p>if preg_match</p>";
		}elseif($password != $passCheck){
			//パスワードが異なる場合のエラー表示
			$errors['password'] = "!パスワードが異なります!";
		//echo "<p>if password differ</p>";
		//echo $errors['password'];
		}else{
			$confirmation="confirmed";
			//sessionに入れとく
			$_SESSION[userName] = $userName;
			$_SESSION[password] = $password;
			$_SESSION[email] = $email;
	
		}
	}else{

	//確認画面語

		//確認画面の下りでPOSTが消えたからもとに戻す
		$userName = $_SESSION[userName];
		$password = $_SESSION[password];
		$email = $_SESSION[email];

		//メールを日本語で送るための準備
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");

		//ユニークなIDを生成
		$uniqueId = uniqid(rand());
		//urlを指定
		$url = "http://co-923.it.99sv-coco.com/practice2/signup_confirm.php?uniqueId=".$uniqueId;

		//メール送る準備

		$subject = "新規登録用URL";
		$message = "下記サイトにアクセスし、登録を完了させてください。\r\n".$url;

		//メール送信
		mb_send_mail($email,$subject,$message);



		//データベース接続
		try {
			require_once("db_info&connect.php");

			//入力情報をデータベースにinsert
			$sql = 'insert into practice2 (name,password,uniqueId,email,registered_time) values (?, ?, ?, ?, now())';
			$stmt = $dbh->prepare($sql);
			$flag = $stmt->execute(array($userName,$password,$uniqueId,$email));
/*
			if($flag){
				echo 'データの追加に成功しました<br>';
			}else{
				echo 'データの追加に失敗しました<br>';
			}
*/
			//登録メッセージ
			$signupMessage = "メールを送信しました";

		}catch(PDOException $e){
			echo('Connection failed:'.$e->getMessage());
			die();
		}
		
		//session破棄
		$_SESSION = array();
		session_destroy();
	}
}



?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>新規登録</title>
	<link rel="stylesheet" href="stylesheet.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
	<?php require_once("home.php");?>

	<?php if(!empty($signupMessage)):?>
	<div class="signup-wrapper">
		<div class="signup-container">
			<div class="close-modal">
				<a href="home.php" class="fa fa-2x fa-times"></a>
			</div>
			<h3 class="signup-heading">完了</h3>
			<p class="email-info">メールを送信しました。<br>２４時間以内に<br>登録をお済ませください。</p>
		</div>
	</div>
	

	<?php elseif(!empty($confirmation)):?>
	<div class="signup-wrapper">
		<div class="signup-container">
			<div class="close-modal">
				<a href="home.php" class="fa fa-2x fa-times"></a>
			</div>
			<h3 class="signup-heading">確認</h3>
			<form action="" method="post">
				<p class="signup-info">username : <?php echo $userName;?></p>
				<p class="signup-info">email : <?php echo $email;?></p>
				<input type="hidden" name="confirmation" value ="confirmed">
				<input class="email-btn" type="submit" name="signup" value="メールを送信">
			</form>
		</div>
	</div>
	<?php else:?>
	<div class="signup-wrapper">
		<div class="signup-container">
			<div class="close-modal">
				<a href="home.php" class="fa fa-2x fa-times"></a>
			</div>
			<h3 class="signup-heading">新規登録</h3>
			<form action="" method="post">
				<input class="signup-input" type="text" name="email" placeholder="email" required value="<?php echo $email ;?>"><br>
				<input class="signup-input" type="text" name="userName" placeholder="ユーザーネーム" required value="<?php echo $userName ;?>"><br>
				<input class="signup-input" type="password" name="password" placeholder="パスワード" required value="<?php echo $password ;?>"><br>
				<input class="signup-input" type="password" name="passCheck" placeholder="もう一度パスワード" required><br>
				<input class="signup-btn" type="submit" name="signup">
			</form>
			<div class="warning">
				<?php foreach($errors as $error):?>
				<p><?php echo $error;?><p>
				<?php endforeach?>
			</div>
		</div>
	</div>

	<?php endif?>
</body>
</html>