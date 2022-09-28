<?php
    function debug_to_console($data) {
        $count = 0;
        $output = [];
        if (is_array($data)){
            echo "<script>console.log('data is an array');</script>";
            foreach($data as $val){
                if(!is_array($val)){
                    array_push($output,$val);
                }
                else{
                    echo "<script>console.log('value is an array');</script>";
                    $count++;
                }
            }
            $output = implode(",",$output);
            echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
            echo "<script>console.log('Ignored data : ".$count. "');</script>";
        }
        else{
            echo "<script>console.log('Debug Objects: " . $data . "' );</script>";
        }


    }

    function appendVar($variable){
        if (strpos($productName,'(') !== false)
        $productName = $product[$count-1]['name'] = str_replace('(',"",$product[$count-1]['name']);
      if (strpos($productName, ')') !== false)
        $productName = $product[$count-1]['name'] = str_replace(')',"",$product[$count-1]['name']);
      if (strpos($productName, '"') !== false)
        $productName = $product[$count-1]['name'] = str_replace('"',"",$product[$count-1]['name']);
      if (strpos($productName, "'") !== false)
        $productName = $product[$count-1]['name'] = str_replace("'","",$product[$count-1]['name']);
    }
?>