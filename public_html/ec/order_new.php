<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
if ($_POST){
  run_query("insert into ec_orders(customer_id, total, status, delivery_date, created_datetime, last_updated) values ('".$_POST['customerId']."', '0', '0', 'To Be Decided', '".$current_datetime."', '".$current_datetime."')");
  header('Location: order.php');
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

.btn-danger:hover, .btn-info:hover{
  cursor:pointer; 
}

.btn-info[disabled]:hover{
  cursor:default;
}

.rowCustomer{
  background-color:#5b6ef8 !important;
  border:2px solid white;
  color:white;
  font-size:16px;  
  border-left:2px solid white!important;
}

.rowCustomer:hover{
  opacity:.8;
  cursor:pointer;
}

.labelCustomer{
  font-weight:bold;
  font-size:16px;
  border-top:2px dotted black;
  border-bottom:2px dotted black;
  color:black;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Make an Order</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-6">
            <h5 class="tx-gray-800 mg-b-5">Select Customer</h5>
              <div class="form-layout form-layout-4">
                <form action="order_new.php" id="orderForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <!-- <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Customer: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <select name="customers" class="form-control select2" data-placeholder="Choose one" id="customers" required>
                      <option value="" selected disabled hidden>Choose one</option>
                        <?php
                          // $customers = run_query("select * from customers order by name asc");
                          // $catName = "";
                          // if ($customers){
                          //   foreach ($customers as $customer){
                          //     $firstLetter = substr($customer['name'],0,1);
                          //     $firstLetter = strtoupper($firstLetter);
                          //         if($catName !== $firstLetter){
                                  ?>
                                    <optgroup label="<?php //echo $firstLetter; ?>" style="background-color:#f1d1fe"></optgroup>
                                  <?php
                                  //   $catName = $firstLetter;
                                  // }
                                ?>
                                  <option value="<?php //echo $customer['id']; ?>"><?php //echo $customer['name']; ?></option>
                                <?php
                          //   }
                          // }
                        ?>
                    </select>
                  </div>
                </div> -->
                <div class="form-layout-footer">
                  <input type="submit" class="btn btn-info" value="Create">
                  <a href="order.php" class="btn btn-secondary float-right">Go Back</a>
                </div><!-- form-layout-footer -->
                <div class="row mg-t-20" hidden>
                  <label class="col-sm-4 form-control-label">ID:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customerId" class="form-control" id="customerId" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Search: <span class="tx-danger" hidden>*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="searchCustomer" id="searchCustomer" class="form-control">
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-12 form-control-label labelCustomer">Customers:</label>
                </div>
                <div class="row mg-t-10" id="customerList">
                  <?php
                    $customers = run_query("select * from customers order by name asc");
                    foreach($customers as $customer){
                  ?>
                      <label class="col-sm-4 form-control-label"></label>
                      <div class="col-sm-12 mg-t-10 mg-sm-t-0 rowCustomer" onclick="fetchCustomer('<?php echo $customer['name'];?>',<?php echo $customer['id'];?>)">
                      <?php echo $customer['name'];?>
                      <span class="float-right">
                        <i class="fa fa-phone"></i>
                        &nbsp
                        <?php echo $customer['phone'];?>
                      </span>
                      </div>
                  <?php
                    }
                  ?>
                </div>
                </form>
              </div><!-- form-layout -->
            </div><!-- col-6 -->
            <div class="col-xl-6">
            <h5 class="tx-gray-800 mg-b-5">Customer Details</h5>
              <div class="form-layout form-layout-4">
                <form class="needs-validation less-width" id="customerDetailForm" method="post" enctype="multipart/form-data" novalidate>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Name:</label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <input type="text" name="customerName" class="form-control" value="" id="customerName" readonly>
                    </div>
                  </div>
                  <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Phone No:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="customerPhone" id="customerPhone" class="form-control" value="" readonly>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Address:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="customerAddress" name ="customerAddress" readonly></textarea>
                    <!-- <div class="invalid-feedback">
                      *Price required
                    </div> -->
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Note:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="customerNote" name ="customerNote" readonly></textarea>
                    <!-- <div class="invalid-feedback">
                      *Price required
                    </div> -->
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

      var searchCustomer = document.getElementById("searchCustomer");
      var customerId = document.getElementById("customerId");
      searchCustomer.setCustomValidity("INVALID");

      function fetchCustomer(name,id){
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
              "customer" : id,
          },
          success: function (result) {
            if(result){
              // console.log("RESULT",JSON.parse(result));
              var customer = JSON.parse(result);
              searchCustomer.setCustomValidity("");
              searchCustomer.value=name;
              document.getElementById("customerId").value=customer['id'];
              document.getElementById("customerName").value=customer['name'];
              document.getElementById("customerPhone").value=customer['phone'];
              document.getElementById("customerAddress").innerHTML=customer['address'];
              document.getElementById("customerNote").innerHTML=customer['note'];
            }
          }
        });
      }

      $(document).ready(function(){
        $("#searchCustomer").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          console.log(value);
          $("#customerList").find('.rowCustomer').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });
    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>