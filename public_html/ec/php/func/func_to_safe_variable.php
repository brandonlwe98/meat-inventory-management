<?php
function to_safe_var($unsafe_var){
    if (is_array($unsafe_var)){
        foreach ($unsafe_var as $k=>$v){
            $unsafe_var[$k] = to_safe_var($v);
        }
        unset($k);
        unset($v);
        return $unsafe_var;
    } else {
        $conn = new mysqli($GLOBALS['server_hostname'],$GLOBALS['server_username'],$GLOBALS['server_password'],$GLOBALS['server_db_name']);
        if ($conn->connect_error){
            die('Connection Failed: ' . $conn->connect_error);
        }
        $safe_var = $conn->real_escape_string($unsafe_var);
        $conn->close();
        return $safe_var;
    }
}
if (isset($_GET)){
    foreach ($_GET as $k=>$v){
        $_GET[$k] = to_safe_var($v);
    }
    unset($k);
    unset($v);
}
if (isset($_POST)){
    foreach ($_POST as $k=>$v){
        $_POST[$k] = to_safe_var($v);
    }
    unset($k);
    unset($v);
}
?>