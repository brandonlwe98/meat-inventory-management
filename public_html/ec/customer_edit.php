<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
  $customerDetails = run_query("select * from customers where id = ".$_GET['id']."")[0];
  $latestOrder = run_query("select * from ec_orders where customer_id = ".$_GET['id']." and status=1 order by last_updated asc limit 1");
  if($latestOrder){
    $latestOrder = $latestOrder[0];
  }
  // debug_to_console($latestOrder);
}
if ($_POST){
  $address = preg_replace('/\s+/', '', $_POST['address']);
  debug_to_console($_POST['address']);
  run_query("update customers set name='".$_POST['name']."', phone='".$_POST['phone']."', address='".$_POST['address']."', note='".$_POST['note']."', last_updated='".$current_datetime."' where id = ".$_GET['id']."");
  header('Location: customer.php');
}
?>
<?php require_once 'php/req/header.php'; ?>
<style>
.btn:hover{
  cursor:pointer; 
}
.btn[disabled]:hover{
  cursor:default!important;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Edit Customer Info</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-6">
              <div class="form-layout form-layout-4">
                <form action="customer_edit.php?id=<?php echo $_GET['id'];?>" id="customerForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Name: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="name" class="form-control" value="<?php echo $customerDetails['name'];?>" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Phone No: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $customerDetails['phone'];?>" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="address" name ="address" required><?php echo $customerDetails['address'];?></textarea>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Note:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="note" name ="note"><?php echo $customerDetails['note'];?></textarea>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total Orders Completed:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="totalOrders" id="totalOrders" class="form-control" value="<?php echo $customerDetails['total_orders'];?>" disabled>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Edit">
                  <a href="customer.php" class="btn btn-secondary">Cancel</a>
                  <button type="button" onclick="confirmDelete('<?php echo $customerDetails['name']?>',<?php echo $_GET['id']; ?>)" style="float:right" class="btn btn-danger" id="btnDelete" <?php if($customerDetails['total_orders']>0){echo "disabled";}?>>Delete</button>
                </div><!-- form-layout-footer -->
                </form>
              </div><!-- form-layout -->
            </div><!-- col-6 -->
            <div class="col-xl-6" id="sectionOrder" <?php if (!$latestOrder){echo "hidden";}?>>
              <div class="form-layout form-layout-4">
                <h5 class="tx-gray-800 mg-b-5">Latest Order (<?php echo date_format(new DateTime($latestOrder['last_updated']),"d/m/Y");?>)</h5>
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
                          $orderItems = null;
                          if($latestOrder){
                            $orderItems = run_query("select * from ec_order_items where order_id=".$latestOrder['id']."");
                          }
                          $sum = 0.00;
                          if($orderItems){
                            $i=1;
                            foreach($orderItems as $item){
                              $productId = 0;
                              $unitPrice = 0.00;
                              $sum = $sum + $item['total_price'];
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
                                $unitPrice = nf_view_currency($product['unit_price']);
                              }
                              else{
                                $mainProduct = run_query("select * from ec_products where id = ".$product['product_id'])[0];
                                $productName = $mainProduct['name'];
                                $unit = run_query("select * from units where id =".$mainProduct['unit'])[0];
                                $unitPrice = nf_view_currency($item['unit_price']);
                              }
                            ?>
                              <tr name="itemTableProduct">
                                <td><?php echo $i;?></td>
                                <td name="productName"><?php echo $productName;?></td>
                                <td name="productUnitPrice"><?php echo $unitPrice;?></td>
                                <?php
                                  $qtyUnit = nf_view_currency($item['quantity'])." ".$unit['name'];
                                ?>
                                <td name="productQtyUnit"><?php echo $qtyUnit;?></td>
                                <td name="productTotalPrice"><?php echo nf_view_currency($item['total_price']);?></td>
                              <tr>
                            <?php
                              $i++;
                            }
                          }
                        ?>
                        <?php
                          $customItems = run_query("select * from ec_custom_items where order_id = ".$latestOrder['id']."");
                          if($customItems){
                            foreach($customItems as $customItem){
                              $sum += $customItem['total_price'];
                        ?>
                            <tr name="customItemTable">
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
                            }
                          }
                        ?>
                          <tr>
                            <td style="border:0px;"></td>
                            <td style="border:0px;"></td>
                            <td style="border:0px;"></td>
                            <td style="border:0px;color:black;width:25%">Grand Total :</td>
                            <td style="border:0px;"><b style="color:black;"><?php echo nf_view_currency($sum);?></b></td>
                            <td style="border:0px;"></td>
                          </tr>
                        </tbody>
                    </table>
                  </div>
                </div>
                <div class="row mg-t-20" id="delivery">
                  <label class="col-sm-4 form-control-label">Delivery Method:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="deliveryFee" class="form-control" value="<?php echo $latestOrder['delivery_method'];?>" readonly>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Delivery Fee:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <?php
                      $deliveryFee = 0.00;
                      if($latestOrder['delivery_fee'] > 0){
                        $deliveryFee = $latestOrder['delivery_fee'];
                      }
                    ?>
                    <input type="text" name="deliveryFee" id="deliveryFee" class="form-control" value="<?php echo nf_view_currency($deliveryFee);?>" readonly>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total: <span class="tx-danger"></span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="total" id="total" class="form-control" value="<?php echo nf_view_currency($latestOrder['total']);?>" readonly>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <button type="button" class="btn btn-warning" onclick="viewOrder(<?php echo $latestOrder['id'];?>)">View</button>
                </div><!-- form-layout-footer -->
              </div><!-- form-layout -->
            </div><!-- col-6 -->
          </div><!-- row -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->

    <!-- Importing script to validate form -->
    <script type="text/javascript" src="scripts/form-validate.js"></script>
    <script>
      var address=document.getElementById("address");
      address.addEventListener("change",function(e){
        if (e.target.value.trim() == ""){
          address.setCustomValidity("INVALID");
        }
        else{
          address.setCustomValidity("");
        }
      })
      address.addEventListener("keyup",function(e){
        if (e.target.value.trim() == ""){
          address.setCustomValidity("INVALID");
        }
        else{
          address.setCustomValidity("");
          console.log(address.value);
        }
      })
      function confirmDelete(customer, id){
      var result = confirm("Are you sure you want to remove " + customer + "?");
      if (result){
          window.location.href = "customer_delete.php?id=" + id;
        }
      }

      function viewOrder(orderId){
        setCookie('currentMenuItem','archive',30);
        window.location.href="archive_view.php?id="+orderId;
      }
    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>