<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
  $orderDetails = run_query("select * from ec_orders where id = ".$_GET['id']."")[0];
  $customerDetails = run_query("select * from customers where id = ".$orderDetails['customer_id']."")[0];
}
if($_POST){
  $orderItems = run_query("select * from ec_order_items where order_id = ".$_GET['id']."");
  $total = 0;
  if($orderItems){
    foreach($orderItems as $items){
      if($items['discount_price']!= null){
        $total = $total + nf_view_currency($items['discount_price']);
      }
      else{
        $total = $total + nf_view_currency($items['total_price']);
      }
    }
  }
  $customItems = run_query("select * from ec_custom_items where order_id = ".$_GET['id']."");
  if($customItems){
    foreach($customItems as $customItem){
      $total = $total + nf_view_currency($customItem['total_price']);
    }
  }
  if($_POST['deliveryFee']>0 && $_POST['deliveryMethod'] == 'Delivery'){
    $total = $total + $_POST['deliveryFee'];
    debug_to_console($total);
  }
  run_query("update ec_orders set total = '".nf_store_currency($total)."', delivery_method = '".$_POST['deliveryMethod']."',delivery_date = '".$_POST['deliveryDate']."', delivery_fee='".nf_store_currency($_POST['deliveryFee'])."', last_updated = '".$current_datetime."', remarks = '".$_POST['remarks']."' where id = ".$_GET['id']."");
  header('Location: order.php');
}
?>
<?php require_once 'php/req/header.php'; ?>
<style>
.less-width .form-control-label{
  max-width:15%!important;
}

.noStock{
  background-color:#ef5454!important;
  color:white!important;
  font-weight:200!important;
}

.btn-danger:hover, .btn-info:hover, .btn-success, .btn-default, .btn-warning:hover{
  cursor:pointer; 
}

.btn-info[disabled], .btn-warning[disabled], .btn-success[disabled], .btn-danger[disabled]:hover{
  cursor:default;
}
.btn-default:hover{
  transition:.15s ease-in-out;
}
.btn-default:hover{
  border:1px solid black;
  background-color:grey;
  color:white;
}

.discountItem{
  color:#df3050 !important;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Edit Order</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-7">
            <h5 class="tx-gray-800 mg-b-5">Order Details</h5>
              <div class="form-layout form-layout-4">
                <form action="order_edit.php?id=<?php echo $_GET['id'];?>" id="orderForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Basket:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <select name="basket" class="form-control select2" data-placeholder="Choose one" id="basket" onchange="cfgBasket(this.value)">
                      <option value="" selected disabled hidden>Choose one</option>
                        <?php
                          $categories = run_query("select * from ec_categories");
                          if ($categories){
                            foreach ($categories as $category){
                              ?>
                              <optgroup label="<?php echo $category['name']; ?>" style="background-color:#f1d1fe"></optgroup>
                              <?php
                              $products = run_query("select * from ec_products where category =".$category['id']." and status=1");
                              foreach($products as $product){
                                $unit = run_query("select * from units where id = ".$product['unit']."")[0];
                                $noStock=false;
                                if($product['quantity'] == 0){
                                  $noStock=true;
                                }
                                if($unit['id'] == 3){
                                  $items = run_query("select * from ec_products_gallery where product_id=".$product['id']." and status=1");
                                ?>
                                  <optgroup label="<?php echo $product['name'];?> -> <?php echo nf_view_currency($product['quantity']);?> units" class="<?php if($noStock){echo 'noStock';}?>">
                                  <?php
                                    foreach($items as $item){
                                  ?>
                                      <option value='<?php echo json_encode($item); ?>'>
                                        <?php 
                                        echo $product['name']." -> ".nf_view_currency($item['quantity']);?> <?php echo $unit['name'];
                                        ?>
                                      </option>
                                  <?php
                                    }
                                  ?>
                                  </optgroup>
                                <?php
                                }
                                else{
                                  ?>
                                    <option value='<?php echo json_encode($product); ?>' <?php if($noStock){echo 'disabled';}?> class="<?php if($noStock){echo 'noStock';}?>">
                                      <?php 
                                      echo $product['name']." -> ".nf_view_currency($product['quantity']);?> <?php echo $unit['name'];
                                      ?>
                                    </option>
                                  <?php
                                }
                              }
                            }
                          }
                        ?>
                      </select>
                  </div>
                </div>
                <input type="text" value="<?php echo $_GET['id'];?>" id="orderId" name="orderId" hidden>
                <div id="basketDetails">
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Quantity: <span class="tx-danger" hidden>*</span></label>
                    <div class="col-sm-5 mg-t-10 mg-sm-t-0">
                      <input type="text" name="quantity" id="quantity" class="form-control" value="0.00" onchange="updateDiscQty(this.value)" readonly>
                    </div>
                    <div class="col-sm-3 mg-t-10 mg-sm-t-0">
                      <button type="button" class="btn btn-info" id="btnAdd" name ="btnAdd" onclick="addItem(<?php echo $_GET['id'];?>,1)" disabled>Add</button>
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalDiscount" id="btnDiscount" name ="btnDiscount" onclick="addItem(<?php echo $_GET['id'];?>,2)" disabled>Disc.</button>
                    </div>
                  </div>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Unit Price: <span class="tx-danger" hidden>*</span></label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <input type="text" name="unitPrice" id="unitPrice" class="form-control" value="0.00" readonly>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 mg-t-10 mg-sm-t-0">
                    <table class="table">
                        <thead>
                          <tr>
                            <th><b>#</th>
                            <th>Item</th>
                            <th>Unit Price(RM)</th>
                            <th>Quantity</th>
                            <th>Total(RM)</b></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody id ="itemTable" name="itemTable">
                        <?php
                          $orderItems = run_query("select * from ec_order_items where order_id=".$_GET['id']."");
                          $sum = 0.00;
                          $i=1;
                          if($orderItems){
                            foreach($orderItems as $item){
                              $productId = 0;
                              $unitPrice = 0.00;
                              $discounted = false;
                              if($item['discount_price'] != null){
                                $discounted = true;
                                $sum = $sum + $item['discount_price'];
                              }
                              else{
                                $sum = $sum + $item['total_price'];
                              }
                              $table = "ec_products";
                              if($item['product_id'] > 0){
                                $productId = $item['product_id'];
                              }
                              else{
                                $table = "ec_products_gallery";
                                $productId = $item['product_gallery_id'];
                              }
                              $product = run_query("select * from ".$table." where id = ".$productId."")[0];
                              $productName = "";
                              $unit = "";
                              if(isset($product['name'])){
                                $productName = $product['name'];
                                $unit = run_query("select * from units where id =".$product['unit'])[0];
                                $unitPrice = nf_view_currency($item['unit_price']);
                              }
                              else{
                                $mainProduct = run_query("select * from ec_products where id = ".$product['product_id'])[0];
                                $productName = $mainProduct['name'];
                                $unit = run_query("select * from units where id =".$mainProduct['unit'])[0];
                                $unitPrice = nf_view_currency($item['unit_price']);
                              }
                            ?>
                              <tr name="itemTableProduct" <?php if($discounted){echo "class='discountItem'";}?>>
                                <td><?php echo $i;?></td>
                                <td name="productName"><?php echo $productName;?></td>
                                <td name="productUnitPrice">
                                  <?php 
                                    if($discounted){
                                      echo nf_view_currency($item['discount_unit_price']);
                                    }
                                    else{
                                      echo $unitPrice;
                                    }
                                  ?>
                                </td>
                                <?php
                                  $qtyUnit = number_format(nf_view_currency($item['quantity']),3)." ".$unit['name'];
                                ?>
                                <td name="productQtyUnit"><?php echo $qtyUnit;?></td>
                                <td name="productTotalPrice">
                                <?php 
                                  if($discounted){
                                    echo nf_view_currency($item['discount_price']);
                                  }
                                  else{
                                    echo nf_view_currency($item['total_price']);
                                  }
                                ?>
                                </td>
                                <td><button type="button" class="btn btn-danger btn-sm" id="<?php echo $item['id']; ?>" onclick="removeItem('<?php echo $productName;?>','<?php echo $qtyUnit;?>',<?php echo $item['id'];?>,<?php echo $_GET['id'];?>)">x</button></td>
                              <tr>
                            <?php
                              $i++;
                            }
                          }
                        ?>
                        <?php
                          $customItems = run_query("select * from ec_custom_items where order_id = ".$_GET['id']."");
                          if($customItems){
                            foreach($customItems as $customItem){
                              $sum += $customItem['total_price'];
                        ?>
                            <tr name="customItemTable" style="background-color:#0cb4ce;color:white;">
                              <td><?php echo $i;?></td>
                              <td name="productName"><?php echo $customItem['name'];?></td>
                              <td name="productUnitPrice"><?php echo nf_view_currency($customItem['unit_price']);?></td>
                              <?php
                                $qtyUnit = number_format(nf_view_currency($customItem['quantity']),3)." ".$customItem['unit'];
                              ?>
                              <td name="productQtyUnit"><?php echo $qtyUnit;?></td>
                              <td name="productTotalPrice"><?php echo nf_view_currency($customItem['total_price']);?></td>
                              <td><button type="button" class="btn btn-danger btn-sm" id="<?php echo $item['id']; ?>" onclick="removeCustomItem('<?php echo $customItem['name'];?>','<?php echo $qtyUnit;?>',<?php echo $customItem['id'];?>,<?php echo $_GET['id'];?>)">x</button></td>
                          <tr>
                        <?php
                              $i++;
                            }
                          }
                        ?>
                          <tr>
                            <td style="border:0px;">
                              <button type="button" data-toggle="modal" data-target="#modalCustomItem" class="btn btn-info btn-sm" id="btnAddCustom" onclick="">
                                Add Custom
                              </button>
                            </td>
                            <td style="border:0px;color:#df3050"><span style="color:#df3050">*Discount</span><br><span style="color:#0cb4ce">*Custom</span></td>
                            <td style="border:0px;"></td>
                            <td style="border:0px;color:black">Grand Total : </td>
                            <td style="border:0px;"><b style="color:black;"><?php echo nf_view_currency($sum);?></b></td>
                            <td style="border:0px;"></td>
                          </tr>
                        </tbody>
                    </table>
                  </div>
                </div>
                <hr>
                <div class="row mg-t-20" id="delivery">
                  <label class="col-sm-4 form-control-label">Delivery Method: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <div class="form-check form-check-inline">
                    <?php
                      $pickup=false;
                      $delivery=false;
                      if($orderDetails['delivery_method'] == 'Delivery'){
                        $delivery = true;
                      }
                      else if($orderDetails['delivery_method'] == 'Self-Pickup'){
                        $pickup=true;
                      }
                    ?>
                      <input class="form-check-input" style="margin-left:0px" type="radio" name="deliveryMethod" id="delivery1" value="Self-Pickup" <?php if($pickup){echo "checked";}?> required>
                      <label class="form-check-label" style="color:black">Self-Pickup</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" style="margin-left:0px" type="radio" name="deliveryMethod" id="delivery2" value="Delivery" <?php if($delivery){echo "checked";}?> required>
                      <label class="form-check-label" style="color:black">Delivery</label>
                    </div>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Delivery/Pickup Date: <span class="tx-danger">*</span></label>
                  <div class="col-sm-5 mg-t-10 mg-sm-t-0">
                  <?php 
                    $setDate = 0;
                    $inputDateReadonly = "";
                    $inputDateType = 'datetime-local';
                    if((bool)strtotime($orderDetails['delivery_date']) == TRUE) { //valid date
                      $setDate = 1;
                    }
                    else{
                      $setDate = 2;
                      $inputDateType = 'text';
                      $inputDateReadonly = "readonly";
                      $orderDetails['delivery_date'] = "To Be Decided";
                    }
                    debug_to_console($orderDetails['delivery_date']);
                  ?>
                    <input type="<?php echo $inputDateType;?>" name="deliveryDate" id="deliveryDate" class="form-control" value="<?php echo $orderDetails['delivery_date'];?>" <?php echo $inputDateReadonly;?> >
                  </div>
                  <div class="col-sm-3 mg-t-10 mg-sm-t-0">
                    <button type="button" class="btn btn-info" id="btnDate" name ="btnDate" data-toggle="button" aria-pressed="false" onclick="unsetDate(<?php echo $setDate;?>)">Toggle Date</button>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Delivery Fee: <span class="tx-danger" id="requiredFee" hidden>*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <?php
                      $deliveryFee = 0.00;
                      if($orderDetails['delivery_fee'] > 0){
                        $deliveryFee = $orderDetails['delivery_fee'];
                      }
                    ?>
                    <input type="text" name="deliveryFee" id="deliveryFee" class="form-control" value="<?php echo nf_view_currency($deliveryFee);?>" onchange="updateTotal(this.value)" <?php if($orderDetails['delivery_method']!=='Delivery'){echo "readonly";}?> >
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Remarks: <span class="tx-danger"></span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="remarks" id="remarks" class="form-control" value="<?php echo $orderDetails['remarks'];?>">
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total: <span class="tx-danger"></span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="total" id="total" class="form-control" value="<?php echo nf_view_currency($orderDetails['total']);?>" readonly>
                  </div>
                </div>
                <div class="row mg-t-20" id="quotationDiv" hidden>
                  <label class="col-sm-2 form-control-label">Quotation: </label>
                  <div class="col-sm-2 mg-t-10 mg-sm-t-0" style="display:flex;align-items:center;">
                    <button type="button" class="btn btn-warning" id="copyQtn" onclick="copyQuotation()">Copy</button>
                  </div>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="quotation" name ="quotation" onchange="qtnChange(this.value)"></textarea>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Update">
                  <button type="button" class="btn btn-success" id="btnPay" onclick="pay(<?php echo $orderDetails['id'];?>)" <?php if($orderDetails['delivery_method'] == "" || (!$orderItems && !$customItems)){echo "disabled";}?>>Pay <i class="fa fa-money" style="font-size:15px"></i></button>
                  <button type="button" class="btn btn-warning" id="btnQuotation" onclick="generateQuotation('<?php echo $customerDetails['name'];?>')" <?php if((!$orderItems && !$customItems) || $orderDetails['delivery_method'] == ""){echo "disabled";}?> >Generate Quotation <i class="fa fa-commenting-o" style="font-size:15px"></i></button>
                  <button type="button" onclick="confirmDelete(<?php echo $_GET['id']; ?>)" class="btn btn-danger" id="btnDelete" <?php if($orderItems){echo "disabled";}?>><i class="fa fa-trash-o" style="font-size:15px;"></i></button>
                  <a href="order.php" class="btn btn-secondary float-right">Back</a>
                </div><!-- form-layout-footer -->
                </form>
              </div><!-- form-layout -->
            </div><!-- col-7 -->
            <div class="col-xl-5">
              <h5 class="tx-gray-800 mg-b-5">Customer Details</h5>
              <div class="form-layout form-layout-4">
                <form class=" less-width" enctype="multipart/form-data" novalidate>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Name:</label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <input type="text" name="customerName" class="form-control" value="<?php echo $customerDetails['name'];?>" id="customerName" disabled>
                    </div>
                  </div>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Phone No:</label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <input type="text" name="customerPhone" id="customerPhone" class="form-control" value="<?php echo $customerDetails['phone'];?>" disabled>
                    </div>
                  </div>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Address:</label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <textarea class="form-control" rows="5" style="resize:none;" id="customerAddress" name ="customerAddress" disabled><?php echo $customerDetails['address'];?></textarea>
                    </div>
                  </div>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Note:</label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <textarea class="form-control" rows="4" style="resize:none;" id="customerNote" name ="customerNote" disabled><?php echo $customerDetails['note'];?></textarea>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div><!-- row -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
    <!-- Modal -->
    <div class="modal fade" id="modalCustomItem">
      <div class="modal-dialog modal-lg" style="width:125%;">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="align-self:center;padding-bottom:5px;">
            <h4 class="modal-title tx-gray-800 mg-b-5">Add Custom Item</h4>
          </div>
          <div class="modal-body">
            <div class="form-layout form-layout-4">
              <form onchange="customModalChange()" action="" id="customItemForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Name: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customName" id="customName" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customUnit" id="customUnit" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit Price: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customUnitPrice" id="customUnitPrice" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Quantity: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customQuantity" id="customQuantity" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total: <span class="tx-danger"></span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customTotal" id="customTotal" class="form-control" readonly>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info" id="btnModalAdd" onclick="addCustomItem(<?php echo $_GET['id'];?>)" disabled>Add</button>
            <button type="button" class="btn btn-default" id="btnModalClose" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDiscount">
      <div class="modal-dialog modal-lg" style="width:125%;">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" style="align-self:center;padding-bottom:5px;">
            <h4 class="modal-title tx-gray-800 mg-b-5">Input Discount</h4>
          </div>
          <div class="modal-body">
            <div class="form-layout form-layout-4">
              <form onchange="" action="" id="inputDiscountForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Discount Unit Price: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="discountUnitPrice" id="discountUnitPrice" class="form-control" onkeyup="discountFormChange(event)" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Discount Quantity:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="discountQty" id="discountQty" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Discount Price:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="discountPrice" value="0.00" id="discountPrice" class="form-control" readonly>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info" id="btnDiscountAdd" onclick="addDiscountItem(<?php echo $_GET['id'];?>)" disabled>Add</button>
            <button type="button" class="btn btn-default" id="btnModalClose" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /Modal-->

    <!-- Importing script to validate form -->
    <script type="text/javascript" src="scripts/form-validate.js"></script>
    <script>

      var btnAdd = document.getElementById("btnAdd");
      var total = document.getElementById("total");
      var unitPrice = document.getElementById("unitPrice");
      var quantity = document.getElementById("quantity");
      var orderId = document.getElementById("orderId").value;
      var quotationDiv = document.getElementById("quotationDiv");
      var quotation = document.getElementById("quotation");
      var initialQty = 0;
      var basket= "";

      quantity.addEventListener("keydown",function(event){
        if (event.keyCode === 13) { //If "ENTER" is pressed,
          // Cancel the default action, if needed
          event.preventDefault();
          // Trigger the button element with a click
          addItem(orderId);
        }
      });

      function cfgBasket(value){
        basket = value;
        var item = JSON.parse(value);
        // console.log(item);
        console.log("BASKET VAL",document.getElementById("basket").value);
        if(item.product_id){ //product gallery item
          $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
                "itemId" : item.product_id,
            },
            success: function (result) {
              if(result){
                // console.log("RESULT",JSON.parse(result));
                var product = JSON.parse(result);
                console.log("PARENT PRODUCT",product);
                productName = product.name;
                $.ajax({
                  type:"POST",
                  url:"ajax.php",
                  data:{
                    "viewUnitQty": 1,
                    "viewUnitPrice": product.unit_price,
                    "viewQuantity": item.quantity,
                  },
                  success:function(val){
                    var view = JSON.parse(val);
                    unitPrice.value = view.unitPrice;
                    quantity.value = view.quantity;
                    discountQty.value = view.quantity;
                    initialQty = view.quantity;
                    quantity.readOnly = true;
                    btnAdd.disabled = false;
                    btnDiscount.disabled = false;
                  }
                });
              }
            }
          });
        }
        else{
          $.ajax({
            type:"POST",
            url:"ajax.php",
            data:{
              "viewUnitQty": 1,
              "viewUnitPrice": item.unit_price,
              "viewQuantity": item.quantity,
            },
            success:function(val){
              var view = JSON.parse(val);
              unitPrice.value = view.unitPrice;
              quantity.value = view.quantity;
              discountQty.value = view.quantity;
              initialQty = view.quantity;
              quantity.readOnly = false;
              btnAdd.disabled = false;
              btnDiscount.disabled = false;
            }
          });
        }
      }

      function addItem(orderId,type){
        if(type == 1){ //Normal
          if(parseFloat(quantity.value) > parseFloat(initialQty)){
            alert("Not Enuf Stock Lah!! Maximum is " + initialQty + " leh.");
          }
          else{
            $.ajax({
              type:"POST",
              url:"ajax.php",
              data:{
                "addItem" : 1,
                "orderId": orderId,
                "basket": basket,
                "quantity": quantity.value,
                "total": total.value,
              },
              success:function(result){
                location.reload();
                // console.log(result);
              }
            });
          }
        }
        else{ //Discount
          if(parseFloat(quantity.value) > parseFloat(initialQty)){
            alert("Not Enuf Stock Lah!! Maximum is " + initialQty + " leh.");
            document.getElementById("discountUnitPrice").readOnly = true;
            document.getElementById("discountUnitPrice").value = "";
            document.getElementById("btnDiscountAdd").disabled = true;
          }
          else{
            document.getElementById("discountUnitPrice").readOnly = false;
          }
        }
      }
      
      var deliveryFee = document.getElementById("deliveryFee");
      var requiredFee = document.getElementById("requiredFee");
      document.getElementsByName("deliveryMethod")[0].addEventListener("change",function(e){
        deliveryFee.readOnly = true;
        deliveryFee.required = false;
        requiredFee.hidden = true;
        deliveryFee.value = 0.00;
        updateTotal(deliveryFee.value);
      })
      document.getElementsByName("deliveryMethod")[1].addEventListener("change",function(e){
        deliveryFee.readOnly = false;
        deliveryFee.required = true;
        requiredFee.hidden = false;
        deliveryFee.value = 0.00;
        updateTotal(deliveryFee.value);
      })

      function updateTotal(deliveryFee){
        // console.log(deliveryFee);
      }

      function removeItem(product,qtyUnit,orderItemId,orderId){
        var result = confirm("Delete " + product + " : " + qtyUnit + "?");
        if (result){
          $.ajax({
            type:"POST",
            url:"ajax.php",
            data:{
              "removeItem" : 1,
              "orderItemId": orderItemId,
              "orderId": orderId,
            },
            success:function(result){
              location.reload();
              // console.log(result);
            }
          });
        }
      }

      function generateQuotation(customer){
        var itemTable = document.getElementById("itemTable").rows;
        document.getElementById("copyQtn").innerText = "Copy";
        quotation.innerHTML = "";
        quotation.innerHTML = "*"+customer+"*\n\n";
        var grandTotal = 0;
        for(var i = 0; i < itemTable.length; i++){
          var td = itemTable[i].getElementsByTagName("td");
          if(td.length > 0 && td[0].parentElement.getAttribute("name") == "itemTableProduct" || td.length > 0 && td[0].parentElement.getAttribute("name") == "customItemTable"){
            // console.log("TD",td[0].parentElement.getAttribute("name"));
            var productName = td['productName'].innerHTML;
            var unitPrice = td['productUnitPrice'].innerHTML;
            var unitQty = td['productQtyUnit'].innerHTML;
            var total = td['productTotalPrice'].innerHTML;
            var unitQty = unitQty.split(" ");
            var isDiscount = "";
            if(itemTable[i].className == "discountItem"){
              isDiscount = "(Discount)";
            }
            // console.log(unitQty);
            var quantity = unitQty[0];
            var unit = unitQty[1];
            if(unit == "kg/pkt"){
              unit = "kg";
            }
            console.log("~ " + productName + " : " + quantity + " x " + "RM" + unitPrice + "/" + unit + " = " + "RM" + total);
            var newLine= "&#9679; " + productName + " : " + quantity + " x " + "RM" + unitPrice.trim() + "/" + unit + " = " + "RM" + total.trim() + isDiscount + "\n";
            quotation.innerHTML += newLine;
            grandTotal += parseFloat(total);
            // console.log(itemTable[i]);
          }
        }
        quotation.innerHTML += "Total : " + "RM" + parseFloat(grandTotal).toFixed(2) + "\n";
        grandTotal = parseFloat(grandTotal) + parseFloat(deliveryFee.value);
        grandTotal = parseFloat(grandTotal).toFixed(2);
        var deliveryMethod = document.querySelector('input[name="deliveryMethod"]:checked').value;
        var lineDelivery="";
        if(deliveryMethod == "Delivery"){
          lineDelivery = lineTotal = "\n&#x1f69a " + deliveryMethod + " : " + "RM" + deliveryFee.value + "\n";
        }
        else{
          lineDelivery = lineTotal = "\n&#x1F45C " + deliveryMethod + "\n"; //unicode character
        }
        var lineTotal = "\n" + "*" + "Grand Total : " + "RM" + grandTotal + "*\n";
        quotation.innerHTML += lineDelivery;
        quotation.innerHTML += lineTotal;
        quotation.innerHTML += "\n--------------------------------------------------------------------\n";

        quotation.innerHTML += "\nBank: Maybank\n";
        quotation.innerHTML += "Account: 512232049598\n";
        quotation.innerHTML += "Name: Andante F&B Supplies\n";
        
        quotation.innerHTML += "\nKindly bank in to secure your order ya.\n";
        quotation.innerHTML += "\nEugene - https://wa.me/60122948217\n\n";
        // console.log(quotation.innerHTML);
        quotationDiv.hidden=false;
      }

      function copyQuotation(){
        var copyText = document.getElementById("quotation");
        document.getElementById("copyQtn").innerText = "Copy";
        console.log(copyText);
        copyText.select();
        var inputValue = copyText.value;
        console.log("INPUTVAL",inputValue)
        if (inputValue) {
          navigator.clipboard.writeText(inputValue)
            .then(() => {
              // alert("Quotation copied to clipboard!");
              var btnCopyQtn = document.getElementById("copyQtn");
              btnCopyQtn.innerText = "Copied";
              btnCopyQtn.class = "btn btn-warning active"; 
            })
            .catch(err => {
              console.log('Something went wrong', err);
            })
        }
      }

      function qtnChange(val){
        console.log("CHANGE",val);
        document.getElementById("quotation").innerHTML = val;
      }

      function pay(orderId){
        console.log(orderId);
        var res = confirm("Confirm to pay order? Once paid, order will be archived.");
        if(res){
          $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{
            "archive":1,
            "orderId": orderId,
          },
          success:function(result){
            // console.log(JSON.parse(result));
            // console.log(result);
            setCookie("currentMenuItem", "archive", 30);
            window.location.href="archive.php";
          }
        });
        }
      }

      function confirmDelete(orderId){
        var res = confirm("Are you sure you want to delete order?");
        if(res){
          window.location.href = "order_delete.php?id=" + orderId;
        }
      }

      var delivery = document.getElementById("deliveryDate");
      var customItemName = document.getElementById("customName");
      var customItemUnit = document.getElementById("customUnit");
      var customItemUnitPrice = document.getElementById("customUnitPrice");
      var customItemQty = document.getElementById("customQuantity");
      var customItemTotal = document.getElementById("customTotal");
      var btnCustomAdd = document.getElementById("btnModalAdd");
      customItemUnitPrice.addEventListener("change",function(e){
        if(parseFloat(e.target.value)>0 && parseFloat(customItemQty.value)>0){
          customItemTotal.value = parseFloat(e.target.value)*parseFloat(customItemQty.value);
          customItemTotal.value = parseFloat(customItemTotal.value).toFixed(2);
        }
      });
      customItemQty.addEventListener("change",function(e){
        if(parseFloat(e.target.value)>0 && parseFloat(customItemUnitPrice.value)>0){
          customItemTotal.value = parseFloat(e.target.value)*parseFloat(customItemUnitPrice.value);
          customItemTotal.value = parseFloat(customItemTotal.value).toFixed(2);
        }
      });

      function customModalChange(){
        if(parseFloat(customUnitPrice.value) > 0){
          customUnitPrice.setCustomValidity("");
        }
        else{
          customUnitPrice.setCustomValidity("INVALID");
        }
        if(parseFloat(customItemQty.value) > 0){
          customItemQty.setCustomValidity("");
        }
        else{
          customItemQty.setCustomValidity("INVALID");
        }
        // console.log(parseFloat(customItemQty.value)>0);
        console.log(customItemName.validity.valid);
        console.log(customItemUnit.validity.valid);
        console.log(customItemUnitPrice.validity.valid);
        console.log(customItemQty.validity.valid);
        if(customItemName.validity.valid && customItemUnit.validity.valid && customItemUnitPrice.validity.valid && customItemQty.validity.valid){
          btnCustomAdd.disabled = false;
        }
        else{
          btnCustomAdd.disabled = true;
        }

      }
      function addCustomItem(orderId){
        console.log("FOO");
        // document.getElementById("btnModalAdd").setAttribute("data-dismiss","modal");
        $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{
            "customItem":1,
            "orderId": orderId,
            "name": customItemName.value,
            "unit": customItemUnit.value,
            "unitPrice": customItemUnitPrice.value,
            "quantity": customItemQty.value,
            "total": customItemTotal.value
          },
          success:function(result){
            window.location.href="order_edit.php?id="+orderId;
            // console.log(result);
          }
        });
      }

      function removeCustomItem(item,qtyUnit,orderItemId,orderId){
        var result = confirm("Delete " + item + " : " + qtyUnit + "?");
        if (result){
          $.ajax({
            type:"POST",
            url:"ajax.php",
            data:{
              "removeCustomItem" : 1,
              "orderItemId": orderItemId,
              "orderId": orderId,
            },
            success:function(result){
              location.reload();
              // console.log(result);
            }
          });
        }
      }

      var discountQty = document.getElementById("discountQty");
      var discountUnitPrice = document.getElementById("discountUnitPrice");
      var discountPrice = document.getElementById("discountPrice");

      function updateDiscQty(quantity){
        discountQty.value = quantity;
      }

      function addDiscountItem(orderId){
        console.log("FOO");
        $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{
            "addItem":1,
            "orderId": orderId,
            "discount":1,
            "discountUnitPrice": discountUnitPrice.value,
            "discountPrice": discountPrice.value,
            "basket": basket,
            "quantity": quantity.value,
            "total": total.value,
          },
          success:function(result){
            location.reload();
            // console.log(result);
          }
        });
      }

      function discountFormChange(e){
        if(discountUnitPrice.validity.valid && discountUnitPrice.value >= 0){
          var totalDiscountPrice = discountQty.value * discountUnitPrice.value;
          document.getElementById("discountPrice").value = totalDiscountPrice.toFixed(3);
          document.getElementById("btnDiscountAdd").disabled = false;
          if(e.keyCode == 13){ //Enter
            e.preventDefault();
            document.getElementById("btnDiscountAdd").click();
          }
        }
        else{
          document.getElementById("btnDiscountAdd").disabled = true;
          if(e.keyCode == 13){ //Enter
            e.preventDefault();
          }
        }
        console.log(e.keyCode);
      }

      var setDate = 0;
      function unsetDate(val){
        console.log("UNSET",val);
        if(setDate == 0){
          setDate = val;
        }
        if(setDate == 1){ //is a valid date
          delivery.type = "text";
          delivery.value = "To Be Decided";
          delivery.readOnly = true;
          setDate = 2;
        }
        else{
          delivery.type = "datetime-local";
          delivery.readOnly = false;
          setDate = 1;
        }
      }
    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>