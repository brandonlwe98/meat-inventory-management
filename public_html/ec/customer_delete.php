<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
    debug_to_console('Deleting customer ID - '.$_GET['id']);
    run_query("delete from customers where id = ".$_GET['id']."");

}
header('Location: customer.php');
?>