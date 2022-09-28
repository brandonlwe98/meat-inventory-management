<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
  $orderDetails = run_query("select * from ec_orders where id = ".$_GET['id']."")[0];
  $customerDetails = run_query("select * from customers where id = ".$orderDetails['customer_id']."")[0];
}

if($_POST){
  // debug_to_console($_POST['remarks']);
  run_query("update ec_orders set delivery_date = '".$_POST['deliveryDate']."', remarks = '".$_POST['remarks']."', last_updated = '".$current_datetime."' where id = ".$_GET['id']."");
  header("Location: archive.php?month=".$_COOKIE['archiveMonth']."&year=".$_COOKIE['archiveYear']."");
}
?>
<?php require_once 'php/req/header.php'; ?>
<style>
.less-width .form-control-label{
  max-width:15%!important;
}

.noStock{
  background-color:#ef5454;
  color:white;
  font-weight:200!important;
}

.btn:hover{
  cursor:pointer; 
}

.btn-info[disabled], .btn-warning[disabled]:hover{
  cursor:default;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Archive</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-7">
            <h5 class="tx-gray-800 mg-b-5">Order Details</h5>
              <div class="form-layout form-layout-4">
                <form action="archive_view.php?id=<?php echo $_GET['id'];?>" id="orderForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
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
                              // debug_to_console($table." - ".$productId);
                              $product = run_query("select * from ".$table." where id = ".$productId."");
                              if($product){
                                $product = $product[0];
                              }
                              $productName = "";
                              $unit = "";
                              if($item['product_name'] !== null){
                                $productName = $item['product_name'];
                              }
                              if($item['unit'] !== null){
                                $unit = run_query("select * from units where id = ".$item['unit']."")[0];
                              }
                              else{
                                if(isset($product['name'])){
                                  $productName = $product['name'];
                                  $unit = run_query("select * from units where id =".$product['unit'])[0];
                                }
                                else{
                                  $mainProduct = run_query("select * from ec_products where id = ".$product['product_id']);
                                  if($mainProduct){
                                    $mainProduct=$mainProduct[0];
                                    $productName = $mainProduct['name'];
                                    $unit = run_query("select * from units where id =".$mainProduct['unit'])[0];
                                  }
                                  else{
                                    $productName = "ITEM DELETED";
                                    $unit = "UNDEFINED";
                                  }
                                }
                              }
                              $unitPrice = nf_view_currency($item['unit_price']);
                            ?>
                              <tr name="itemTableProduct" <?php if($discounted){echo "style='color:#df3050'";}?>>
                                <td><?php echo $i;?></td>
                                <td name="productName"><?php echo $productName;?></td>
                                <td name="productUnitPrice"><?php echo $unitPrice;?></td>
                                <?php
                                  $qtyUnit = nf_view_currency($item['quantity'])." ".$unit['name'];
                                ?>
                                <td name="productQtyUnit"><?php echo $qtyUnit;?></td>
                                <td name="productTotalPrice" <?php if($discounted){echo "style='color:#df3050;'";}?>>
                                <?php 
                                  if($discounted){
                                    echo nf_view_currency($item['discount_price']);
                                  }
                                  else{
                                    echo nf_view_currency($item['total_price']);
                                  }
                                ?>
                                </td>
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
                                $qtyUnit = nf_view_currency($customItem['quantity'])." ".$customItem['unit'];
                              ?>
                              <td name="productQtyUnit"><?php echo $qtyUnit;?></td>
                              <td name="productTotalPrice"><?php echo nf_view_currency($customItem['total_price']);?></td>
                          <tr>
                        <?php
                            $i++;
                            }
                          }
                        ?>
                          <tr>
                            <td style="border:0px;"></td>
                            <td style="border:0px;"><span style="color:#df3050">*Discount</span><br><span style="color:#0cb4ce">*Custom</span></td>
                            <td style="border:0px;"></td>
                            <td style="border:0px;color:black;width:25%;">Grand Total : </td>
                            <td style="border:0px;"><b style="color:black;"><?php echo nf_view_currency($sum);?></b></td>
                            <td style="border:0px;"></td>
                          </tr>
                        </tbody>
                    </table>
                  </div>
                </div>
                <hr>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Payment Date:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <?php
                      if($orderDetails['paid_date']){
                        $paymentDate = date_create($orderDetails['paid_date']);
                        $paymentDate = date_format($paymentDate,"d/m/Y H:i A");
                      }
                      else{
                        $paymentDate = null;
                      }
                    ?>
                    <input type="text" name="deliveryFee" class="form-control" value="<?php echo $paymentDate;?>" readonly>
                  </div>
                </div>
                <div class="row mg-t-20" id="delivery">
                  <label class="col-sm-4 form-control-label">Delivery Method:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="deliveryFee" class="form-control" value="<?php echo $orderDetails['delivery_method'];?>" readonly>
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
                  ?>
                    <input type="<?php echo $inputDateType;?>" name="deliveryDate" id="deliveryDate" class="form-control" value="<?php echo $orderDetails['delivery_date'];?>" <?php echo $inputDateReadonly;?> >
                  </div>
                  <div class="col-sm-3 mg-t-10 mg-sm-t-0">
                    <button type="button" class="btn btn-info" id="btnDate" name ="btnDate" data-toggle="button" aria-pressed="false" onclick="unsetDate(<?php echo $setDate;?>)">Toggle Date</button>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Delivery Fee:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <?php
                      $deliveryFee = 0.00;
                      if($orderDetails['delivery_fee'] > 0){
                        $deliveryFee = $orderDetails['delivery_fee'];
                      }
                    ?>
                    <input type="text" name="deliveryFee" id="deliveryFee" class="form-control" value="<?php echo nf_view_currency($deliveryFee);?>" readonly>
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
                    <textarea class="form-control" rows="5" id="quotation" name ="quotation" disabled></textarea>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Update">
                  <button type="button" class="btn btn-secondary float-right" onclick="goBack()"><i class="fa fa-reply"></i> Back</button>
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
                    <!-- <div class="invalid-feedback">
                      *Price required
                    </div> -->
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

    <!-- Importing script to validate form -->
    <script type="text/javascript" src="scripts/form-validate.js"></script>
    <script>

      var btnAdd = document.getElementById("btnAdd");
      var total = document.getElementById("total");
      var unitPrice = document.getElementById("unitPrice");
      var quantity = document.getElementById("quantity");
      var quotationDiv = document.getElementById("quotationDiv");
      var quotation = document.getElementById("quotation");
      var delivery = document.getElementById("deliveryDate");
      var initialQty = 0;
      var basket= "";

      var deliveryFee = document.getElementById("deliveryFee");
      var requiredFee = document.getElementById("requiredFee");

      function generateQuotation(){
        var itemTable = document.getElementById("itemTable").rows;
        quotation.innerHTML += "Bank: Maybank\n";
        quotation.innerHTML += "Account: 164164987405\n";
        quotation.innerHTML += "Name: Low Eu Gene\n";
        
        quotation.innerHTML += "\nKindly bank in to secure your order ya.\n";
        quotation.innerHTML += "\nEugene - https://wa.me/60122948217\n\n";
        var grandTotal = 0;
        for(var i = 0; i < itemTable.length; i++){
          var td = itemTable[i].getElementsByTagName("td");
          if(td.length > 0 && td[0].parentElement.getAttribute("name") == "itemTableProduct"){
            // console.log("TD",td[0].parentElement.getAttribute("name"));
            var productName = td['productName'].innerHTML;
            var unitPrice = td['productUnitPrice'].innerHTML;
            var unitQty = td['productQtyUnit'].innerHTML;
            var total = td['productTotalPrice'].innerHTML;
            var unitQty = unitQty.split(" ");
            // console.log(unitQty);
            var quantity = unitQty[0];
            var unit = unitQty[1];
            console.log("~ " + productName + " : " + quantity + " x " + "RM" + unitPrice + " per " + unit + " = " + "RM" + total);
            var newLine= "~ " + productName + " : " + quantity + " x " + "RM" + unitPrice + " per " + unit + " = " + "RM" + total + "\n";
            quotation.innerHTML += newLine;
            grandTotal += parseFloat(total);
            // console.log(itemTable[i]);
          }
        }
        quotation.innerHTML += "Total : " + "RM" + parseFloat(grandTotal).toFixed(2) + "\n";
        grandTotal = parseFloat(grandTotal) + parseFloat(deliveryFee.value);
        grandTotal = parseFloat(grandTotal).toFixed(2);
        var deliveryMethod = document.querySelector('input[name="deliveryMethod"]:checked').value;
        var lineDelivery = lineTotal = "\nDelivery Method : " + deliveryMethod + "\n" + "Delivery Fee : " + "RM" + deliveryFee.value + "\n";
        var lineTotal = "\n" + "*" + "Grand Total : " + "RM" + grandTotal + "*";
        quotation.innerHTML += lineDelivery;
        quotation.innerHTML += lineTotal;
        // console.log(quotation.innerHTML);
        quotationDiv.hidden=false;
      }

      function copyQuotation(){
        var copyText = document.getElementById("quotation");
        console.log(copyText);
        copyText.select();
        const inputValue = copyText.innerHTML.trim();
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
          console.log("UNSET2",setDate);
          delivery.type = "datetime-local";
          delivery.readOnly = false;
          setDate = 1;
        }
      }

      function goBack(){
        var oldURL = document.referrer;
        if(oldURL.includes("customer")){
          setCookie('currentMenuItem','customer',30);
        }
        else if(oldURL.includes("index")){
          setCookie('currentMenuItem','dashboard',30);
        }
        else if(oldURL.includes("archive")){
          setCookie('currentMenuItem','archive',30);
        }
        window.location.href=document.referrer;
      }
    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>