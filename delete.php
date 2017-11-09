<?php

$sql = "delete from card where id = :id";
$stmt = $dbh->prepare($sql);
$paramas = array(':id' => $_POST['deleteId']);
$stmt->execute($paramas);

//echo "deleted successfully";



?>