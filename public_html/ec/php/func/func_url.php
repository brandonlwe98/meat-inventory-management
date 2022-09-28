<?php
function current_url(){
    return $_SERVER['PHP_SELF'];
}
function current_file(){
    return basename(current_url(), '.php');
}
?>