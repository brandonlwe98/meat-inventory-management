<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
    debug_to_console('Deleting file ID - '.$_GET['id']);
    run_query("delete from ec_orders where id = ".$_GET['id']."");

}
header('Location: order.php');
?>