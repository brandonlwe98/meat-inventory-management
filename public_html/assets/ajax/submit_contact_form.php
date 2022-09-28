<?php require_once '../php/func/functions.php'; ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    $error_name = 0;
    $error_email = 0;
    $error_phone = 0;
    $error_company = 0;
    $error_message = 0;
    if (isset($_POST['name']) && $_POST['name'] != ''){ } else { $error_name = 1; }
    if (isset($_POST['email']) && $_POST['email'] != ''){ } else { $error_email = 1; }
    if (isset($_POST['phone']) && $_POST['phone'] != ''){ } else { $error_phone = 1; }
    if (isset($_POST['message']) && $_POST['message'] != ''){ } else { $error_message = 1; }
    if (!$error_name && !$error_email && !$error_phone && !$error_company && !$error_message){
        run_query("alter table contact_form auto_increment = 1");
        $insert = run_query("insert into contact_form (name, email, phone, company, message, created_datetime) values ('".$_POST['name']."', '".$_POST['email']."', '".$_POST['phone']."', '".$_POST['company']."', '".$_POST['message']."', '".$current_datetime."')");
        if ($insert){
            $data = 'success';
        } else {
            $data = 'error';
        }
    } else {
        $data = 'required';
    }
}
header('Content-Type: application/json');
if (isset($data)){
    echo json_encode($data);
}
?>