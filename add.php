<?php

$front =$_POST["front"];
$back = $_POST["back"];
$detail = $_POST["detail"];
$time = date('Y/m/d H:i:s');

//一番後ろのidとる
$sql = 'select max(id) from card';
$stmt = $dbh->prepare($sql);
$stmt->execute();

while($result = $stmt->fetch(PDO::FETCH_NUM)){
	echo var_dump($result).'<br>';
	$lastId = $result[0];
}

//cardテーブルからfrontを取り出して照合
$sql = "select front,back, detail from card where id = '$lastId'";
$stmt = $dbh->prepare($sql);
$stmt->execute();

while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
	$data[] = $result;
}
/*
echo "<pre>";
echo var_dump($data);
echo "</pre>";
*/
//同じものがあったらエラーメッセージに代入
foreach($data as $data){
	//既存の名前
	if($data['front'] == $front && $data['back'] == $back && $data['detail'] == $detail){
		$errorMessage = "既に存在する名前です。";
	}
}
//登録可能な名前ならcardに登録 front back timeを入れる
if(empty($errorMessage)){

	//front/back/detai/reg_timeを保存
	$sql = "insert into card (front,back,detail,reg_time,bundle_id) values ('$front','$back','$detail', '$time','$bundleId')";
	$stmt = $dbh->prepare($sql);
	$flag = $stmt->execute();
/*
	if($flag){
		echo 'データの追加に成功しました<br>';
	}else{
		echo 'データの追加に失敗しました<br>';
	}
*/
}

//echo "errorMessage : $errorMessage<br>";

?>