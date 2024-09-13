<?php

require("database.php"); 
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
$getEmailList = "SELECT * FROM emaillist";
$resultEmailList = $conn->query($getEmailList);

if ($resultEmailList->num_rows > 0) {
    $dataEmailList = array();
    while ($rowEmailList = $resultEmailList->fetch_assoc()) {
        $dataEmailList[] = $rowEmailList;
    }

    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success' , 'message' => 'sent Successfully','data'=>$dataEmailList)); 
} else {

    header('Content-Type: application/json');
    echo json_encode([]);
}


?>