<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
if ($_POST){
  // $address = preg_replace('/\s+/', '', $_POST['address']);
  run_query("insert into customers(name, phone, address, note) values ('".$_POST['name']."', '".$_POST['phone']."', '".$_POST['address']."', '".$_POST['note']."')");
  header('Location: customer.php');
}
?>
<?php require_once 'php/req/header.php'; ?>
<style>
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Add Customer</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-6">
              <div class="form-layout form-layout-4">
                <form action="customer_new.php" id="customerForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Name: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="name" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Phone No: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="phone" id="phone" class="form-control" required>
                    <div class="form-group" style="margin-bottom:2%;display:none;" id="phoneInvalid">
                      <span class="tx-danger" style="padding:1% 2%;color:red" id ="phoneInvalidMsg"> 
                      </span>
                    </div>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Address: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="5" id="address" name ="address" required></textarea>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Note:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" rows="3" id="note" name ="note"></textarea>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Add">
                  <a href="customer.php" class="btn btn-secondary float-right">Cancel</a>
                </div><!-- form-layout-footer -->
                </form>
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
        }
      })

      var phone = document.getElementById("phone");
      var phoneInvalid = document.getElementById("phoneInvalid");
      phone.addEventListener("change",function(e){
        $.ajax({
          type:"POST",
          url:"ajax.php",
          data:{
            "existingCustomer": 1,
            "phone":e.target.value
          },
          success:function(val){
            if(val != 0){
              var customer = JSON.parse(val);
              console.log(customer);
              phone.setCustomValidity("INVALID");
              phoneInvalid.style.display = "block";
              phoneInvalidMsg.innerHTML = "Phone number already registered : "+customer['name'];
            }
            else{
              phone.setCustomValidity("");
              phoneInvalid.style.display = "none";
            }
            
            // var customer = JSON.parse(val);
            // console.log(val);
          }
        });
      })

    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>