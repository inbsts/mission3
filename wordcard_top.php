<?php
//escape処理
require_once('escape_processor.php');

session_start();

//ログインしてないユーザーはhome画面へ
if(empty($_SESSION["userName"])){
	header("Location:home.php");
}

$name = $_POST['bundleName'];
$deleteId = $_POST['deleteId'];

//echo "bundleName: $name<br>";

try {
	require_once("db_info&connect.php");

	//新規bundel登録
	if(!empty($name)){
		//bundleテーブルからbundlenameを取り出して照合
		$sql = "select name from bundle";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		
		while($result = $stmt->fetch(PDO::FETCH_NUM)){
			echo var_dump($result).'<br>';
			$bundleNames[] = $result[0];
		}

		//同じものがあったらエラーメッセージに代入
		foreach($bundleNames as $value){
			//既存の名前
			if($value == $name){
				$errorMessage = "errorr : 同名の単語帳が既に存在します。名前を変えてください。";
			}
		}
		//登録可能な名前ならbundletableに登録、session　name,idを入れる
		if(empty($errorMessage)){
			//bundlename登録
			$time = date('Y/m/d H:i:s');
			$sql = "insert into bundle (name, reg_time) values ('$name','$time')";
			$stmt = $dbh->prepare($sql);
			$flag = $stmt->execute();

			if($flag){
				echo 'データの追加に成功しました<br>';
			}else{
				echo 'データの追加に失敗しました<br>';
			}

			//bundleテーブルからbundleidを取り出す
			$sql = "select id from bundle where name = '$name'";
			$stmt = $dbh->prepare($sql);
			$result=$stmt->execute();
			
			$_SESSION['bundleId'] = $result[0];
			$_SESSION['bundleName'] = $bundleName;
			
			echo "<h3>session : </h3>".var_dump($_SESSION['bundleId']);
			

			//なかったらheader でmainに移動
			header("Location: wordcard_main.php");
			//処理終了
			exit();
		}
	}


	//delete処理
	if(!empty($deleteId)){
		$sql = "delete from bundle where id = :id";
		$stmt = $dbh->prepare($sql);
		$paramas = array(':id' => $deleteId);
		$stmt->execute($paramas);

//		echo "deleted successfully";
	}


	//カードの情報をselect
	$sql = "select id, name,reg_time,login_time,count from bundle";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	
	while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		$bundleId[] = $result['id'];
		$bundleName[] = $result['name'];
		$bundleRegTime[] = $result['reg_time'];
		$bundleLoginTime[] = $result['login_time'];
		$count[] = $result['count'];
	}
	
	//単語の例の準備
	$card = array();
	foreach($bundleId as $value){
		$sql = "select front from card where bundle_id = :id";
		$stmt = $dbh->prepare($sql);
		$stmt->execute(array(':id'=>$value));
		
		$front = array();
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$front[] = $result['front'];
		}

		$contents = "";
		for($i=0;$i<=8;$i++){
			if(!empty($front[$i])){
				$contents .= $front[$i]." / ";
			}
		}
		$contents .= ".....etc";
		$card[] = $contents;
	}

}catch(PDOException $e){
	echo('Connection failed:'.$e->getMessage());
	die();
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>word cards</title>
	<link rel="stylesheet" href="wordcard_top.css">
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
		<div class="bundles-wrapper">
			<div class="container">
				<div class="title">
					<h2>Word Cards</h2>
				</div>
				<div class="bundles">
					<?php for($i=0;$i<count($bundleId);$i++):?>
					<div class="bundle">
						<a href="wordcard_main.php?id=<?php echo $bundleId[$i] ;?>&name=<?php echo $bundleName[$i]?>"><?php echo convert($bundleName[$i]);?></a>
						<div class ="card-contents">
							<ul>
								<li><span>登録日時</span>　<?php echo $bundleRegTime[$i];?></li>
								<li><span>前回の学習</span>　<?php echo $bundleLoginTime[$i]?></li>
								<li><span>学習回数</span>　<?php echo $count[$i]?>回</li>
								<li><span>単語</span>　<?php echo $card[$i] ;?></li>
							</ul>
						</div>
					</div>
					<div class="delete">
						<form action="" method="post">
							<input type="hidden" value="<?php echo $bundleId[$i] ;?>" name="deleteId">
							<input type="submit" value="delete">
						</form>
					</div>
					<?endfor?>
				</div>
			</div>
		</div>
		<div class="create-bundle-wrapper">
			<div class="container">
				<div class="title">
					<h2>Create New Bundle</h2>
				</div>
				<p class="error"><?echo $errorMessage;?></p>
				<form action="" method="post" class="create-bundle">
					<input type="text" name="bundleName" required class="create-bundle-text" placeholder="新しい単語帳の名前"><br>
					<input type="submit" value="creat new bundle" class="create-bundle-submit">
				</form>
			</div>
		</div>
	</div>

	<footer>
	</footer>
	<script src="script_main.js"></script>
</body>
</html>
