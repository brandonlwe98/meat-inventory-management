<?php
function nf_store_currency($v){
    return str_replace(',', '', $v) * 100000;
}
function nf_view_currency($v){
    $amount = number_format($v / 100000, 5,'.',"");
    $decimals = explode('.', $amount)[1];
    $loops = strlen($decimals);
    for ($i = $loops; $i > 0; $i--){
        if ($i > 2){
            if ($decimals[$i - 1] == 0){
                $amount = substr($amount, 0, -1);
            } else {
                $i = 0;
            }
        }
    }
    return $amount;
}
function nf_stripe_currency($v){
    $amount = $v / 1000;
    return $amount;
}
?>