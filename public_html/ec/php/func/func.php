<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'func_database_connection.php';
require_once 'func_to_safe_variable.php';
require_once 'func_datetime.php';
require_once 'func_number_format.php';
require_once 'func_url.php';
?>
<?php
if (!in_array(current_file(), array('login', 'logout'))){

    if (!isset($_SESSION['user_id'])){
        header('Location: login.php');
    }
    $user_data = run_query("select * from users where id = ".$_SESSION['user_id']."")[0];
}

?>