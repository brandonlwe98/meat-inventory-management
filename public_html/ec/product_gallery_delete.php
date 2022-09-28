<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
    $galleryItem = run_query("select * from ec_products_gallery where id = ".$_GET['id']."")[0];
    run_query("delete from ec_products_gallery where id = ".$_GET['id']."");
    unlink($galleryItem['file']);
}
header("Location: product_edit.php?id=".$_GET['productId']."");
?>