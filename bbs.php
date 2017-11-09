<?php

session_start();

//echo "<pre>".var_dump($_SESSION)."</pre>";

//ポスト定義
$name = $_POST['name'];
$comment = $_POST['comment'];
$time = date('Y/m/d H:i:s');
$deleteId = $_POST['deleteId'];
$editId = $_POST['editId'];
$editName = $_POST['editName'];
$editComment = $_POST['editComment'];
$completeEditId = $_POST['completeEditId'];

//file定義
$tempfile = $_FILES['file']['tmp_name'];
$filename = $_FILES['file']['name'];

$fileExtension = pathinfo($filename,PATHINFO_EXTENSION);

$imageExtensions = array("gif","jpg","jpeg","png","tif","tiff","bmp");
$videoExtensions = array("mp4","m4a");



//faleをサーバーにアップロード
if(is_uploaded_file($tempfile)){
	//拡張子をチェック、画像
	foreach($imageExtensions as $imageExtension){
		if($fileExtension == $imageExtension){
			if(move_uploaded_file($tempfile,"image/".$filename)){
//				echo $filename."をアップロードしました<br>";
				//$imageに画像のファイル名を入れる
				$image = $filename;
//echo $image."<br>";
			}else{
//				echo "ファイルをアップロードできません<br>";
			}
		}else{
			$i++;
		}
	}
	if(count($videoExtensions)==$i){
//		echo "拡張子が対応していません。<br>";
	}
	$i=0;

	//拡張子をチェック、動画
	foreach($videoExtensions as $videoExtension){
		if($fileExtension == $videoExtension){
			if(move_uploaded_file($tempfile,"video/".$filename)){
//				echo $filename."をアップロードしました<br>";
				//$videoに動画のファイル名を入れる
				$video = $filename;
			}else{
//				echo "ファイルをアップロードできません<br>";
			}
		}else{
			$i++;
		}
	}
	if(count($videoExtensions)==$i){
//		echo "拡張子が対応していません。<br>";
	}

}else{
//	echo "ファイルが選択されていません。";
}





//エスケープ処理の関数
function h($value){
	return htmlspecialchars(stripslashes($value),ENT_QUOTES);
}

function convert($comment){
	$comment = stripslashes($comment);
	$comment = htmlspecialchars($comment,ENT_QUOTES);
	return nl2br($comment);
}

try {	//db接続
	require_once("db_info&connect.php");

	$sql = 'select max(id) from users order by id desc';
	$stmt = $dbh->prepare($sql);
	$stmt->execute();

	while($result = $stmt->fetch(PDO::FETCH_NUM)){
		$lastId = $result[0];
	}

	$sql = 'select name, comment from users  where id = :lastId';
	$stmt = $dbh->prepare($sql);
	$paramas = array('lastId'=>$lastId);
	$stmt->execute($paramas);

	while($result = $stmt->fetch(PDO::FETCH_NUM)){
		$lastName = $result[0];
		$lastComment = $result[1];
	}

	//idに１をたす
	if(empty($lastId)){
		$id=1;
	}else{
		$id=$lastId +1;
	}

	//再読み込み防止
	if($name == $lastName && $comment == $lastComment){
		$samePost="reload";
	}

	//postが送られたときの動作
	if(isset($name) && empty($samePost)){

		//データベースに渡す
		$sql = "insert into users (id,name,comment,time,userId,image,video) values (:id,:name,:comment,:time,:userId,:image,:video)";
		$stmt = $dbh->prepare($sql);
		$paramas = array(':id'=>$id,':name'=>$name,':comment'=>$comment,':time'=>$time,':userId'=>$_SESSION['userId'],':image'=>$image,':video'=>$video);
		$flag = $stmt->execute($paramas);
//echo $image;
/*		if($flag){
			echo 'データの追加に成功しました<br>';
		}else{
			echo 'データの追加に失敗しました<br>';
		}
*/
	}
	
	//deleteの処理
	if(isset($deleteId)){
		$sql = 'delete from users where id = :deleteId';
		$stmt = $dbh->prepare($sql);
		$paramas = array(':deleteId'=>$deleteId);
		$stmt->execute($paramas);
		
//		echo "<h2>delete done successfully in DB</h2>";
	}elseif(isset($deleteId)){
		$falsePass = "false";
	}

	//edit処理
	if(isset($editName)){
/*
echo "editName : $editName<br>";
echo "editComment : $editComment<br>";
echo "completeEditId : $completeEditId<br>";
*/
		$sql = "update users set name = :name, comment = :comment, time = :time where id = :id";
		$stmt = $dbh->prepare($sql);
		$params = array(':name'=>$editName,':comment'=>$editComment,':time'=>$time,':id'=>$completeEditId);
		$stmt->execute($params);
//		echo '<h4>edit done successfully</h4>';		
	}
	

	//データの受け取り
	$sql ="select * from users order by id";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();

	while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		$data[]= $result;
		$array_id[] = $result['id'];
		$array_name[] = $result['name'];
		$array_comment[] = $result['comment'];
		$array_time[] = $result['time'];
		$array_userId[] = $result['userId'];
		$array_image[] = $result['image'];
		$array_video[] = $result['video'];
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
	<title>main</title>
	<link rel="stylesheet" href="bbs.css">
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

	<div class="title">
		<div class="heading">
			<h1>掲示板</h1>
		</div>
	</div>
	
	<div id="post-show">
		<p>投稿する</p>
	</div>
	
	<div class ="post-wrapper">
		<div id="close">
			閉じる
		</div>
		<div class="post-container">
			<form action="" method="post" enctype="multipart/form-data">
				<input class="name-post" type="text" name="name" placeholder="name" required value ="<?php echo $_SESSION['userName'];?>">
				<textarea id="comment-post" name="comment" placeholder="コメントを入力してください" required></textarea>
				<input class="file" type="file" name="file" id="file">
				<input class="post-btn" type="submit" value="send">
			</form>
		</div>
	</div>

<div class="edit-wrapper">
	<div class="edit-container">
	<?php for($i=0;$i<count($data);$i++):?>
		<?php if($editId == $array_id[$i]):?>
			<form class="edit-form" method="post">
				<p class="id"><?php echo $array_id[$i];?></p>
				<input type="hidden" value="<?php echo $array_id[$i];?>" name="completeEditId">
				<input class="editName" type="text" value="<?php echo h($array_name[$i]);?>" name="editName" required>
				<p class="time"><?php echo $array_time[$i];?></p>
				<textarea class="comment-input" name="editComment" required><?php echo h($array_comment[$i]);?></textarea>
				<input class="edit-input" type="submit" value="edit">
			</form>
		<?php endif?>
	<?php endfor?>
	</div>
</div>

	<div class ="contents-wrapper">
		<div class="container">
			<?php for($i=0;$i<count($data);$i++):?>
				
				<div class="comments">
					<div class="comments-container">
						<p class="id"><?php echo $array_id[$i];?></p>
						<p class="name"><?php echo h($array_name[$i]);?></p>
						<p class="time"><?php echo $array_time[$i];?></p>
					<!------------------------------------------------------------------------------------deletebtn editbtn--------------->
						<?php if($_SESSION['userId'] == $array_userId[$i]):?>
						<div class="btns">
							<form class="btn-form" method="post">
								<div class="btn-option">
									<button type="button" class="delete-open">delete</button>
									<button type="submit" class="edit btn" value="<?php echo $array_id[$i];?>" name="editId">edit</button>
								</div>
								<div class="delete-alarm">
									<p>本当に削除しますか？</p>
									<button type="submit" class="delete btn" value="<?php echo $array_id[$i];?>" name="deleteId">YES</button>
									<button type="button" class="btn delete-close">NO</button>
								</div>
							</form>
						</div>
						<div class="warning">
							<?php if(isset($falsePass) && ($deleteId == $array_id[$i] or $editId == $array_id[$i])):?>
								<p>your password was wrong</p>
							<?php endif?>
						</div>
						<?php endif ?>
					<!--------------------------------------------------------------------------------------------------------------------------->

						<p class="comment"><?php echo convert($array_comment[$i]);?></p>
						<?php if(!empty($array_image[$i])):?>	
							<img src="<?php echo "image/".$array_image[$i];?>">
						<?php endif ?>
						<?php if(!empty($array_video[$i])):?>
							<video autoplay loop muted controls>
								<source src="video/<?php echo $array_video[$i] ;?>">
							</video>
						<?php endif?>
					</div>
				</div>
			<?php endfor?>
		</div>
	</div>

	<footer>
	</footer>
	<script src="bbs.js"></script>
</body>
</html>
