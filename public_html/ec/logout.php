<?php
setcookie("currentMenuItem","", -1,'/');
setcookie("currentProductCat","", -1,'/');
session_start();
session_unset();
session_destroy();
header('Location: /meat_delivery/public_html/ec');
?>