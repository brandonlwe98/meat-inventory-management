<?php
$server_hostname = 'localhost';
$server_username = 'root';
$server_password = '';
$server_db_name = 'meat_delivery';
function run_query($query, $info = false){
    $conn = new mysqli($GLOBALS['server_hostname'],$GLOBALS['server_username'],$GLOBALS['server_password'],$GLOBALS['server_db_name']);
    if ($conn->connect_error){
        die('Connection Failed: ' . $conn->connect_error);
    }
    $result = $conn->query($query);
    if (gettype($result) != 'boolean'){
        if ($result->num_rows > 0){
            $i = 0;
            while ($row = $result->fetch_assoc()){
                $info[$i] = $row;
                $i++;
            }
        }
    } else {
        if ($result === true){
            $info = $conn->insert_id;
            if (!$info){
                $info = true;
            }
        } else {
            $info = false;
        }
    }
    $conn->close();
    return $info;
}
?>