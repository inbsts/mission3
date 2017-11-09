<?php

session_start();

//post定義
$email=$_POST[email];
$password=$_POST[password];
$login=$_POST[login];

//エラーメッセージの初期化
$errors = array();

if(isset($login)){

	//emailcheck
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
		$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
	}else{

		try {
			require_once("db_info&connect.php");
		
			//投稿されたemailの情報をselect
			$sql = "select id, name, password,flag from practice2  where email = '{$email}'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();

echo "<pre>".var_dump($stmt)."</pre>";

			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
				$userId = $result['id'];
				$userName = $result['name'];
				$userPass = $result['password'];
				$flag = $result['flag'];
			}

			//投稿passとユーザーpassの比較
			if(empty($userId)){
				$errors['register'] ="メールアドレスが間違っています";
			}elseif($flag != 1){
				$errors['flag'] = "仮登録中です。<br>登録を完了させてください。";
			}elseif($password != $userPass){
				$errors['password'] = "パスワードが間違っています";
			}else{
				//セッションにユーザーネームを持たせる
				$_SESSION['userId'] = $userId;
				$_SESSION['userName'] = $userName;
				$_SESSION['userPassword'] = $userPass;
				//メイン画面へ
				header("Location: user.php");
				//処理終了
				exit();
			}

		}catch(PDOException $e){
			echo('Connection failed:'.$e->getMessage());
			die();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>ログイン</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="stylesheet.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
	<?php require_once("home.php");?>

	<div class="login-wrapper">
		<div class="login-container">
			<div class="close-modal">
				<a  href="home.php" class="fa fa-2x fa-times"></a>
			</div>
			<div class="login-form">
				<h2 class="login-heading">ログイン</h2>
				<form method="post" action ="#">
					<input class="login-input" type="text" name="email" required placeholder="email"><br>
					<input class="login-input" type="password" name="password" required placeholder="password"><br>
					<input class="login-btn" type="submit" name="login" value="ログイン">
				</form>
			</div>
			<?php if(!empty($errors)):?>
			<div class="login-warning">
				<?php foreach($errors as $error):?>
				<p><?php echo $error ;?></p>
				<?php endforeach ?>
			</div>
			<?php endif?>
		</div>
	</div>
</body>
</html>


