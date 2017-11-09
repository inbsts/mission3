<?php
$editId =$_POST["editId"];
$front =$_POST["front"];
$back = $_POST["back"];
$detail = $_POST["detail"];
$time = date('Y/m/d H:i:s');

$sql = "update card set front = '$front',back = '$back',detail = '$detail',reg_time = '$time' where id = '$editId'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
//		echo '<h4>edit done successfully</h4>';		

//echo "deleted successfully";

?>