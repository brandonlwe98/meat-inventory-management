<?php
if (!empty($_POST)) {

    // $method = $_POST['request'];

    include 'php/func/func.php';
    include 'php/func/debug.php';

    if (isset($_POST['customer'])){
        $customers = run_query("select * from customers where id= '".$_POST['customer']."'")[0];
        if($customers){
            echo json_encode($customers);
        }
    }

    if(isset($_POST['itemId'])){ //return item based on id
        $product = run_query("select * from ec_products where id = ".$_POST['itemId']."")[0];
        if($product){
            echo json_encode($product);
        }
    }

    if(isset($_POST['viewUnitQty'])){ //
        $obj = new stdClass();
        $value = nf_view_currency($_POST['viewUnitPrice']);
        $obj->unitPrice = $value;
        $value = nf_view_currency($_POST['viewQuantity']);
        $obj->quantity = $value;
        echo json_encode($obj);
    }

    if(isset($_POST['addItem']) && $_POST['addItem'] > 0){ //add item
        $JSON = stripslashes(html_entity_decode($_POST['basket']));
        $basket = json_decode($JSON,TRUE);
        $productId = "null";
        $productGalleryId = "null";
        $productName ="";
        $productUnit = "";
        $price = 0;
        if(isset($basket['product_id'])){ //product gallery item
            //update ec_products
            $masterProduct = run_query("select * from ec_products where id = ".$basket['product_id'])[0];
            $newQty = nf_view_currency($masterProduct['quantity']) - 1;
            $newPrice = nf_view_currency($masterProduct['total_price']) - nf_view_currency($basket['price']);
            $unitPrice = $masterProduct['unit_price'];
            run_query("update ec_products set quantity = '".nf_store_currency($newQty)."', total_price = '".nf_store_currency($newPrice)."' where id = ".$masterProduct['id']."");
            
            //update ec_products_gallery status
            $productGalleryId = $basket['id'];
            run_query("update ec_products_gallery set status = 0 where id = ".$basket['id']);
            $price = nf_view_currency($basket['price']);
            $productName = $masterProduct['name'];
            $productUnit = $masterProduct['unit'];
        }
        else{ //normal product item
            $productId = $basket['id'];
            $price = $_POST['quantity'] * nf_view_currency($basket['unit_price']);
            $newQty = nf_view_currency($basket['quantity']) - $_POST['quantity'];
            $newPrice = nf_view_currency($basket['total_price']) - number_format($price,2);
            $unitPrice = $basket['unit_price'];
            $productName = $basket['name'];
            $productUnit = $basket['unit'];
            run_query("update ec_products set total_price = '".nf_store_currency($newPrice)."', quantity = '".nf_store_currency($newQty)."' where id = ".$basket['id']."");
        }
        $discountPrice = "null";
        $discountUnitPrice = "null";
        if(isset($_POST['discount']) && $_POST['discountPrice'] >= 0){
            $discountUnitPrice = nf_store_currency($_POST['discountUnitPrice']);
            $total = $_POST['total'] + $_POST['discountPrice'];
            $discountPrice = nf_store_currency($_POST['discountPrice']);
        }
        else{
            $total= $_POST['total'] + $price;
        }
        run_query("update ec_orders set total = '".nf_store_currency($total)."' where id = ".$_POST['orderId']."");
        run_query("alter table ec_order_items auto_increment = 1");
        if(isset($_POST['discount']) && $_POST['discountPrice'] >= 0){
            run_query("insert into ec_order_items (order_id, product_id, product_gallery_id, product_name, unit, unit_price, quantity, total_price, discount_price, discount_unit_price, created_datetime) values (".$_POST['orderId'].", ".$productId.", ".$productGalleryId.", '".$productName."', '".$productUnit."', '".$unitPrice."', '".nf_store_currency($_POST['quantity'])."', '".nf_store_currency(number_format($price,2))."', '".$discountPrice."', '".$discountUnitPrice."', '".$current_datetime."')");
        }
        else{
            run_query("insert into ec_order_items (order_id, product_id, product_gallery_id, product_name, unit, unit_price, quantity, total_price, discount_price, discount_unit_price, created_datetime) values (".$_POST['orderId'].", ".$productId.", ".$productGalleryId.", '".$productName."', '".$productUnit."', '".$unitPrice."', '".nf_store_currency($_POST['quantity'])."', '".nf_store_currency(number_format($price,2))."', ".$discountPrice.", ".$discountUnitPrice.", '".$current_datetime."')");
        }
    }

    if(isset($_POST['removeItem'])){ //remove an item
        $order = run_query("select * from ec_orders where id = ".$_POST['orderId'])[0];
        $total = nf_view_currency($order['total']);
        $orderItem = run_query("select * from ec_order_items where id = ".$_POST['orderItemId'])[0];
        if($orderItem['product_gallery_id'] > 0){ //is product gallery item
            //update ec_products
            $productGallery = run_query("select * from ec_products_gallery where id = ".$orderItem['product_gallery_id']."")[0];
            $product = run_query("select * from ec_products where id = ".$productGallery['product_id']."")[0];
            $newQty = nf_view_currency($product['quantity']) + 1;
            $newPrice = nf_view_currency($product['total_price']) + nf_view_currency($orderItem['total_price']);
            run_query("update ec_products set quantity ='".nf_store_currency($newQty)."', total_price='".nf_store_currency($newPrice)."' where id = ".$product['id']."");

            //update order and ec_product_gallery 
            $currentTotal = $total - nf_view_currency($orderItem['total_price']);
            if($orderItem['discount_price'] >= 0){
                $currentTotal = $total - nf_view_currency($orderItem['discount_price']);
            }
            run_query("update ec_products_gallery set status = 1 where id = ".$orderItem['product_gallery_id']."");
            run_query("update ec_orders set total = '".nf_store_currency($currentTotal)."' where id = ".$order['id']."");
            run_query("delete from ec_order_items where id = ".$orderItem['id']);
        }
        else{ //normal product item
            //update ec_products
            $product = run_query("select * from ec_products where id = ".$orderItem['product_id']."")[0];
            $newQty = nf_view_currency($product['quantity']) + nf_view_currency($orderItem['quantity']);
            $newPrice = nf_view_currency($product['total_price']) + nf_view_currency($orderItem['total_price']);
            run_query("update ec_products set quantity ='".nf_store_currency($newQty)."', total_price='".nf_store_currency($newPrice)."' where id = ".$product['id']."");
            //update order
            $currentTotal = $total - nf_view_currency($orderItem['total_price']);
            if($orderItem['discount_price'] >= 0){
                $currentTotal = $total - nf_view_currency($orderItem['discount_price']);
            }
            run_query("update ec_orders set total = '".nf_store_currency($currentTotal)."' where id = ".$order['id']."");
            run_query("delete from ec_order_items where id = ".$orderItem['id']."");
            // echo "delete from ec_order_items where id = ".$orderItem['id']."";
        }
    }

    if(isset($_POST['customItem'])){ //create custom item
        $order = run_query("select * from ec_orders where id = ".$_POST['orderId'])[0];
        run_query("insert into ec_custom_items (order_id,name,unit,unit_price,quantity,total_price,created_datetime) values (".$_POST['orderId'].", '".$_POST['name']."', '".$_POST['unit']."', '".nf_store_currency($_POST['unitPrice'])."', '".nf_store_currency($_POST['quantity'])."', '".nf_store_currency($_POST['total'])."', '".$current_datetime."')");
        $total = $_POST['total'] + nf_view_currency($order['total']);
        run_query("update ec_orders set total = '".nf_store_currency($total)."' where id = ".$order['id']."");
    }

    if(isset($_POST['removeCustomItem'])){ //remove custom item
        $order = run_query("select * from ec_orders where id = ".$_POST['orderId'])[0];
        $customItem = run_query("select * from ec_custom_items where id = ".$_POST['orderItemId']."")[0];
        $total = nf_view_currency($order['total']) - nf_view_currency($customItem['total_price']);
        run_query("delete from ec_custom_items where id = ".$customItem['id']);
        run_query("update ec_orders set total = '".nf_store_currency($total)."' where id = ".$order['id']."");
    }

    if(isset($_POST['archive'])){ //archive order
        $order = run_query("select * from ec_orders where id = ".$_POST['orderId'])[0];
        // $orderItems = run_query("select * from ec_order_items where order_id = ".$order['id']."");
        // $customer = run_query("select * from customers where id = ".$order['customer_id']."")[0];
        $query = "update ec_orders set status=1, last_updated='".$current_datetime."', paid_date = '".$current_datetime."' where id = ".$order['id']."";
        run_query($query);
        // echo json_encode($orderItems);
        // echo "\n";
        // echo $query1;
    }

    if(isset($_POST['delivery'])){ //update delivery status
        $order = run_query("select * from ec_orders where id = ".$_POST['order_id'])[0];
        run_query("update ec_orders set delivery_status=1 where id = ".$order['id']."");
        $customer = run_query("select * from customers where id = ".$order['customer_id']."")[0];
        $totalOrders = $customer['total_orders'];
        $totalOrders = $totalOrders + 1;
        $query = "update customers set total_orders=".$totalOrders." where id = ".$customer['id']."";
        run_query($query);
    }

    if(isset($_POST['existingCustomer'])){ //
        $customer = run_query("select * from customers where phone =".$_POST['phone']."");
        if($customer){
            echo json_encode($customer[0]);
        }
        else{
            echo 0;
        }
    }

    if(isset($_POST['generateReport'])){ //Generate grossing report for the selected month
        $orders = run_query("select * from ec_orders where status = 1");
        $total = 0;
        $delivery = 0;
        $totalOrders = 0;
        if($orders){
            foreach($orders as $order){
                $date = new DateTime($order['created_datetime']);
                $week = $date->format("W");
                $month = $date->format("M");
                $orderMonthNo = $date->format("m");
                $orderYearNo = $date->format("Y");
                if($orderMonthNo == $_POST['reportMonth'] && $orderYearNo == $_POST['reportYear']){
                    $total += nf_view_currency($order['total']) - nf_view_currency($order['delivery_fee']);
                    $delivery += nf_view_currency($order['delivery_fee']);
                    $totalOrders += 1;
                }
            }
            $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
            echo "Sales Report - ".$months[$_POST['reportMonth']-1]." ".$_POST['reportYear']."\n";
            echo "Total Orders : ".$totalOrders."\n";
            echo "Total Sales : RM".number_format($total,2)."\n";
            // echo "Delivery Fees : RM".number_format($delivery,2);
        }
    }

    if(isset($_POST['generateOrderToday'])){ //Generate today's delivery/pickup orders
        $date = new DateTime($current_datetime);
        $query = "select * from ec_orders where delivery_date >= '".$current_date."T00:00' and delivery_date <= '".$current_date."T23:59' and delivery_status = 0";
        // $query = "select * from ec_orders where delivery_date >= '2021-11-25T00:00' and delivery_date <= '2021-11-30T00:00' and delivery_status = 0";
        $orders = run_query($query);
        if($orders){
            echo "Today's orders - ".$date->format("d M Y")."";
            echo "\n----------------------------------------------------\n\n";
            foreach($orders as $order){
                $customer = run_query("select * from customers where id = ".$order['customer_id']."")[0];
                $delDate = new DateTime($order['delivery_date']);
                echo $customer['name']." ".$customer['phone']."\n\n";
                echo "Time: ".$delDate->format("h:i A")."\n\n";
                echo "Address: ".$customer['address']."\n\n";
                $orderItem = run_query("select * from ec_order_items where order_id = ".$order['id']);
                if($orderItem){
                    foreach($orderItem as $item){
                        $unit = run_query("select * from units where id = ".$item['unit']); // get unit name from product unit id
                        if($item['product_gallery_id'] > 0){ //is product gallery item
                            $productGallery = run_query("select * from ec_products_gallery where id = ".$item['product_gallery_id'])[0];
                            $product = run_query("select * from ec_products where id = ".$productGallery['product_id']."")[0];
                            echo "\t\u{25CF}".$product['name']."\n"; //\u{} unicode
                            foreach($unit as $u)
                                echo "\t ".nf_view_currency($item['quantity']).$u['name']." * RM".nf_view_currency($item['unit_price'])." = RM".nf_view_currency($item['total_price'])."\n";
                        }
                        else{ //normal product item
                            $product = run_query("select * from ec_products where id = ".$item['product_id']."")[0];
                            echo "\t\u{25CF}".$product['name']."\n";
                            foreach($unit as $u)
                                echo "\t ".nf_view_currency($item['quantity']).$u['name']." * RM".nf_view_currency($item['unit_price'])." = RM".nf_view_currency($item['total_price'])."\n";
        
                        }
                    }
                }
                $customItem = run_query("select * from ec_custom_items where order_id = ".$order['id']);
                if($customItem){
                    foreach($customItem as $item){
                        echo "\t\u{25CF}".$item['name']."\n";
                        // foreach($unit as $u)
                            echo "\t ".nf_view_currency($item['quantity']).$item['unit']." * RM".nf_view_currency($item['unit_price'])." = RM".nf_view_currency($item['total_price'])."\n";
                    }
                }
                if($order['delivery_method'] == "Self-Pickup"){
                    echo "\n\tSelf Pickup";
                }
                else{
                    echo "\n\tDelivery Fee = RM".nf_view_currency($order['delivery_fee']);
                }
                echo "\n\n\tTotal = RM".nf_view_currency($order['total']);
                echo "\n";

                if($order['remarks'] != ""){
                    echo "\n\tRemarks = ".$order['remarks']."\n";
                }
                echo "\n----------------------------------------------------\n";
                echo "\n";
            }
        }
    }


    //Charts

    if(isset($_POST['pastMonthsData'])){ //index.php - sales performance graph
        $rev = [];
        $numOrders = [];
        $pastMonths = [];
        for($i = 6; $i > 0; $i--){
            $date = new DateTime(date('Y-m', strtotime("-".$i." month")));
            $revenue = 0;
            $noOrders = 0;
            // array_push($pastMonths, $date);
            $orders = run_query("select * from ec_orders where paid_date >= '".$date->format('Y-m-d 00:00:00')."' and paid_date <= '".$date->format('Y-m-t 23:59:59')."'");
            // echo "select * from ec_orders where paid_date >= '".$date->format('Y-m-d 00:00:00')."' and paid_date <= '".$date->format('Y-m-t 23:59:59')."'";
            foreach($orders as $order){
                $revenue += nf_view_currency($order['total']) - nf_view_currency($order['delivery_fee']);
                $noOrders++;
            }
            array_push($rev, ($revenue/1000));
            array_push($numOrders, $noOrders);
            array_push($pastMonths, $date->format('M'));
        }

        // foreach($pastMonths as $month){
        //     $orders = run_query("select * from ec_orders where paid_date >= '".$date->format)
        // }
        $result = array();
        $result['rev'] = $rev;
        $result['numOrders'] = $numOrders;
        $result['pastMonths'] = $pastMonths;
        echo json_encode($result);
        // echo json_encode($numOrders);
        // array_push($pastMonths)
    }

}

?>