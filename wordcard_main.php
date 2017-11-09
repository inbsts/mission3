<?php
require_once('escape_processor.php');

session_start();

//ログインしていないユーザーはホーム画面へ
if(empty($_SESSION["userName"])){
	header("Location:home.php");
}

//bundleName,bundleIdの準備,既存/新規で分岐
if($bundleName = $_GET["name"]){
	$bundleId = $_GET["id"];
}else{
	$bundleName = $_SESSION["bundleName"];
	$bundleId = $_SESSION["bundleId"];
}

//sessionの破棄
unset($_SESSION["bundleName"]);
unset($_SESSION["bundleId"]);

/*
echo "<h3>bundleName : $bundleName</h3>";
echo "<h3>bundleId : $bundleId</h3>";
*/
$time = date('Y/m/d H:i:s');


if(!empty($bundleName)){
	try {
		require_once("db_info&connect.php");

		//bundletableのcountに１たす
		//count取り出し
		$sql = "select count from bundle where id = '$bundleId'";
		$stmt = $dbh->query($sql);
		$result = $stmt->fetch();
		$count = $result[0] + 1;

		//bundletableにcountに１たしたものを追加
		$sql = "update bundle set count = '$count', login_time = '$time' where id = '$bundleId'";
		$stmt = $dbh->prepare($sql);
		$flag = $stmt->execute();
/*
		if($flag){
			echo 'データの追加に成功しました<br>';
		}else{
			echo 'データの追加に失敗しました<br>';
		}
		
*/
//echo "add : ".$_POST['add']."<br>";
		//addの処理
		if(!empty($_POST['add'])){
			require_once('add.php');
		}

		//deleteの処理
		if(!empty($_POST['deleteId'])){
			require_once('delete.php');
		}

		//edit処理
		if(!empty($_POST['editId'])){
			require_once('edit.php');
		}
		
		//bundleId から該当するカードを選び出す
		$sql = "select id, front, back, detail from card where bundle_id = '$bundleId'";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$cardId[] = $result['id'];
			$cardFront[] = $result['front'];
			$cardBack[] = $result['back'];
			$cardDetail[] = $result['detail'];
		}
		

	}catch(PDOException $e){
		echo('Connection failed:'.$e->getMessage());
		die();
	}
}else{
	//bundleNameが空だから不正なアクセス
	//word_card_topに飛ばす
	header("Location: wordcard_top.php");
	//処理終了
	exit();
}



 echo "-------------------------------------------php------------------------------------------------";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>word cards</title>
	<link rel="stylesheet" href="wordcard_main.css">
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

<!------------------------------------------------------------------------------------edit input----------------->
		<?php if(!empty($_POST["editRequire"])):?>
		<div id="edit">
			<div class="close-btn">close</div>
			<div class="container">
				<form method="post" action="">
					<input type="hidden" name ="editId" value="<?php echo $_POST['editRequire']?>">
					<input class="input-text" type="text" name="front" value="<?php echo h($_POST['editFront'])?>" placeholder="front of the card" required><br>
					<input class="input-text" type="text" name="back" value="<?php echo convert($_POST['editBack'])?>" placeholder="back of the card"><br>
					<textarea class="textarea" name="detail" placeholder="additional information"><?php echo h($_POST['editDetail'])?></textarea><br>
					<input class="submit-btn" type="submit" value="edit">
				</form>
			</div>
		</div>
		<?php endif ?>

<!----------------------------------------------------------------------------------------------------cards-->
	<div class="cards">
		<div class="cards-container">
			<div class="title">
				<h1><?php echo $bundleName;?></h1>
				<div class="btns2">
					<div class="container">
						<div id="front" class="btn2">English</div>
						<div id="back" class="btn2">Japanese</div>
						<div id="" class="btn2">シャッフル</div>
						<div id="" class="btn2">ソート</div>
					</div>
				</div>
			</div>	
			<?php for($i=0;$i<count($cardId);$i++):?>
			<div class="card-wrapper">
				<div class="card-container">
					<div class="card front">
						<h3><?php echo h($cardFront[$i]) ;?></h3>
					</div>
					<div class="card back">
						<h3><?php echo h($cardBack[$i]) ;?></h3>
					</div>
				</div>
				<?php if(!empty($cardDetail[$i])):?>
				<div class="card-detail">
					<p><?php echo convert($cardDetail[$i]) ;?></p>
				</div>
				<?php endif?>
			</div>
			<?php endfor?>
		</div>
	</div>


<!--------------------------------------------------------------------------------------------------------------------btns---->
	<div class="btns-wrapper">

	<!---------------------------------------------------------------------------------------------add/edit form-->
		<div id="add">
			<div class="close-btn">close</div>
			<div class="container">
				<form method="post" action="">
					<input class="input-text" type="text" name="front" placeholder="front of the card" required><br>
					<input class="input-text" type="text" name="back" placeholder="back of the card"><br>
					<textarea class="textarea" name="detail" placeholder="additional information"></textarea><br>
					<input class="submit-btn" type="submit" name="add" value="add">
				</form>
			</div>
		</div>
	<!----------------------------------------------------------------------------------------------------btns1-->
		<div class="btns1">
			<div class="container">
				<a class="btn1">test</a>
				<div id="show-add" class="btn1">add</div>
				<div id="show-edit" class="btn1">edit</div>
				<div id="show-delete" class="btn1">delete</div>
			</div>
		</div>
	</div>

	<!----------------------------------------------------------------------------------------------dark-background-------------->
	<div id="delete-mode" class="dark_background">
		<h1>カードを選択してください</h1>
		<div class="close-btn dark-close">close</div>

		<?php for($i=0;$i<count($cardId);$i++):?>
		<div class="card-wrapper">
			<div class="card-container">
				<div class="card front flag">
					<h3><?php echo h($cardFront[$i]) ;?></h3>
				</div>
				<div class="card back delete-back">
					<p id="alarm">本当に削除しますか</p>
					<form class="delete" method="post" action="">
						<input type="hidden" name="deleteId" value="<?php echo $cardId[$i];?>">
						<input class="yes-btn" type="submit" value="YES">
					</form>
				</div>
			</div>
		</div>
		<?php endfor?>
	</div>


	<div id="edit-mode" class="dark_background">
		<h1>カードを選択してください</h1>
		<div class="close-btn dark-close">close</div>

		<?php for($i=0;$i<count($cardId);$i++):?>
		<div class="card-wrapper">
			<div class="card-container">
				<div class="card front flag">
					<h3><?php echo h($cardFront[$i]) ;?></h3>
					<form method="post" action="">
						<input type="hidden" name="editFront" value="<?php echo $cardFront[$i]?>">
						<input type="hidden" name="editBack" value="<?php echo $cardBack[$i]?>">
						<input type="hidden" name="editDetail" value="<?php echo $cardDetail[$i]?>">
						<input type="hidden" name="editRequire" value="<?php echo $cardId[$i];?>">
						<input class="edit-card" type="submit" value="">
					</form>
				</div>
			</div>
		</div>
		<?php endfor?>
	</div>

	<footer>
	</footer>
	<script src="wordcard_main.js"></script>
</body>
</html>
