<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
    debug_to_console('Deleting file ID - '.$_GET['id']);
    $productInUse = run_query("select * from ec_order_items where product_id = ".$_GET['id']."");
    $preventDelete = 0;
    if($productInUse){
        foreach($productInUse as $i){
            $order = run_query("select * from ec_orders where id = ".$i['order_id']."")[0];
            if($order['status'] == 0){ //product still in use
                $preventDelete = 1;
            }
        }
    }
    if($preventDelete == 0){
        $photo = run_query("select photo from ec_products where id = ".$_GET['id'].""); //get file path
        foreach ($photo[0] as $file){
            $fileName = str_replace("upload/product/","",$file);
        }    
        if(is_dir('upload/product/'.$_GET['id'])){
            $dirPath = 'upload/product/'.$_GET['id'].'/';
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir('upload/product/'.$_GET['id']);
        }
        unlink('upload/product/'.$fileName);
        $result = run_query("delete from ec_products where id = ".$_GET['id']."");
        run_query("delete from ec_products_gallery where product_id = ".$_GET['id']."");
        header('Location: product.php');
    }
    else{
        header('Location: product_edit.php?id='.$_GET['id'].'&failDelete=1');
    }

}
?>