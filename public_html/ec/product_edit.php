<?php require_once 'php/func/debug.php'; ?>
<?php require_once 'php/func/func.php'; ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != ''){
  $productDetails = run_query("select * from ec_products where id = ".$_GET['id']."")[0];
}

if(isset($_GET['failDelete'])){
  echo "<script>alert('Product failed to be deleted. It is still currently being in use la! Check your current orders bugger.')</script>";
}

$image_error_messages = [];
$image_error = 0;
if($_POST){
    $quantity = nf_store_currency($_POST['quantity']);
    $total_price = nf_store_currency($_POST['total']);
    $query = "update ec_products set name='".$_POST['name']."',unit_price = '".nf_store_currency($_POST['unit_price'])."', quantity='".$quantity."',total_price='".$total_price."',last_updated = '".$current_datetime."' where id = ".$_GET['id']."";
    run_query($query);
    if (isset($_FILES['photo']) && ($_FILES['photo']['error'] == 0 || $_FILES['photo']['error'][0] == 0)){
      unlink($productDetails['photo']);
      $allowed = array('jpg'=>'image/jpg', 'jpeg'=>'image/jpeg', 'gif'=>'image/gif', 'png'=>'image/png', 'JPG'=>'image/jpg', 'JPEG'=>'image/jpeg', 'GIF'=>'image/gif', 'PNG'=>'image/png');
      
      $filename = $_FILES['photo']['name'][0];
      $filetype = $_FILES['photo']['type'][0];
      $filesize = $_FILES['photo']['size'][0];
      
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if (!array_key_exists($ext, $allowed)){
        $image_error = 1;
        $image_error_messages[] = 'Only allowed JPG, PNG & GIF format';
      }
      $maxsize = 100 * 1024 * 1024;
      if ($filesize > $maxsize){
          $image_error = 1;
          $image_error_messages[] = 'Maximum upload size is 100 MB';
      }
      if ($image_error == 1) {
          $image_error_messages[] = 'Unable to upload image.';
      }
      else{
        run_query("alter table ec_products auto_increment = 1");
        $uploaded_filename = $_GET['id'].'.'.$ext;
        $move_upload = move_uploaded_file($_FILES['photo']['tmp_name'][0], 'upload/product/'.$uploaded_filename);
        run_query("update ec_products set photo = 'upload/product/".$uploaded_filename."' where id = ".$_GET['id']."");
      }
    }
    if ($image_error == 0){
      debug_to_console($productDetails['unit']);
      if($productDetails['unit'] == 3){
        $quantity = 0;
        run_query("delete from ec_products_gallery where product_id =".$_GET['id']." and status = 1");
        if(is_array($_POST['productList']) && sizeof($_POST['productList']) > 0){
          $quantity = sizeof($_POST['productList']);
          run_query("alter table ec_products_gallery auto_increment = 1");
          foreach($_POST['productList'] as $item=>$item_v){
            debug_to_console("ITEM #");
            debug_to_console($item_v);
            $price = $item_v*$_POST['unit_price'];
            $price = number_format($price,2);
            $price = nf_store_currency($price);
            run_query("insert into ec_products_gallery (product_id,quantity,price,created_datetime) values ('".$_GET['id']."', '".nf_store_currency($item_v)."', '".$price."', '".$current_datetime."')");
          }
        }
        run_query("update ec_products set quantity = '".nf_store_currency($quantity)."' where id =".$_GET['id']."");
      }
      header('Location: product.php');
    }
}
?>
<?php require_once 'php/req/header.php'; ?>

<style>
.input-group-addon{
  color:black;
  border-right:1px solid rgb(0 0 0 /0.15);
  border-radius: 0px 3px 3px 0px;
  background-color:white;
  transition:0.15s;
}

.form-control + .input-group-addon:not(:first-child):hover{
  cursor:pointer;
  color:white;
  background-color:black;
}

.btn-danger:hover, .btn-info:hover{
  cursor:pointer; 
}

.table th, .table td{
  vertical-align:middle!important;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5">Edit Product</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-6">
              <div class="form-layout form-layout-4">
                <form action="product_edit.php?id=<?php echo $productDetails['id']; ?>" id="productForm" class="needs-validation" method="post" enctype="multipart/form-data" onsubmit="return loadSpinner(this)" novalidate>
                <div class="row">
                  <label class="col-sm-4 form-control-label">Category: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <select disabled name="category" class="form-control select2" data-placeholder="Choose one" required>
                    <option value="" selected disabled hidden>Choose one</option>
                        <?php
                            $categories = run_query("select * from ec_categories");
                            if ($categories){
                                foreach ($categories as $categories_k=>$categories_v){
                                    ?>
                                      <option value="<?php echo $categories_v['id']; ?>" <?php if($categories_v['id'] == $productDetails['category']){ echo "selected"; }?>><?php echo $categories_v['name']; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Name: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="name" class="form-control" value="<?php echo $productDetails['name']; ?>" required>
                    <!-- <div class="invalid-feedback">
                      *Name required
                    </div> -->
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <?php
                      $unit = run_query("select * from units where id =".$productDetails['unit']."")[0];
                    ?>
                    <select name="unit" class="form-control select" data-placeholder="Choose one" disabled>
                      <option value="<?php echo $productDetails['unit'];?>" selected disabled hidden><?php echo $unit['name'];?></option>
                    </select>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit Price: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="unit_price" id="unitPrice" value="<?php echo nf_view_currency($productDetails['unit_price']);?>" class="form-control" required>
                  </div>
                </div>
                <?php
                  $displayTable = false;
                  if($productDetails['unit'] == '3'){ 
                    $displayTable = true;
                  } 
                
                ?>
                <div class="row mg-t-20" id="qty1" <?php if($displayTable){ echo "hidden";} ?>>
                  <label class="col-sm-4 form-control-label">Quantity: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" id="qty1Val" name="quantity" value="<?php echo nf_view_currency($productDetails['quantity']); ?>" class="form-control" required>
                  </div>
                </div>
                <div id="qty2" <?php if(!$displayTable){ echo "hidden";} ?>>
                  <div class="row mg-t-20">
                    <label class="col-sm-4 form-control-label">Quantity: <span class="tx-danger">*</span></label>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0 input-group">
                      <input type="text" name="quantity2" id="quantity" class="form-control">
                      <div class="input-group-addon" id="addItem" onclick="addItem()">
                        <i class="fa fa-plus"></i>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <span class="col-sm-4">
                      <button type="button" class="btn btn-info btn-sm" id="btnReset" name ="btnReset" style="margin-bottom:5%" onclick="resetTable()">Reset Table</button>
                      <button type="button" class="btn btn-info btn-sm" id="btnUpdate" name ="btnUpdate" onclick="updateTable()">Update Table</button>
                    </span>
                    <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                      <table class="table">
                          <thead>
                            <tr>
                              <th><b>#</th>
                              <th>Weight(kg)</th>
                              <th>Price(RM)</b></th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody id ="itemTable">
                            <?php
                              $productGallery = run_query("select * from ec_products_gallery where product_id = ".$productDetails['id']." and status = 1");
                              $i = 1;
                              if($productGallery){
                                $totalWeight = 0.00;
                                foreach($productGallery as $item){
                                  $totalWeight += $item['quantity'];
                            ?>
                              <tr>
                                <td><?php echo $i;?></td>
                                <td name="itemQty"><?php echo nf_view_currency($item['quantity']);?></td>
                                <td name="itemPrice"><?php echo nf_view_currency($item['price']);?></td>
                                <td><button type="button" class="btn btn-danger btn-sm" id="<?php echo nf_view_currency($item['quantity']); ?>" onclick="removeItem(this.id)">x</button></td>
                              </tr>
                            <?php
                                $i++;
                                }
                              }
                            ?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total(RM):</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="total" id="total" value="<?php echo nf_view_currency($productDetails['total_price']); ?>" class="form-control" readonly>
                  </div>
                </div>
                <?php
                  if($productGallery){
                ?>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total(kg):</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="totalWeight" id="totalWeight" value="<?php echo nf_view_currency($totalWeight); ?>" class="form-control" readonly>
                  </div>
                </div>
                <?php
                  }
                ?>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Photo: </label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                  <img src="<?php echo $productDetails['photo'].'?'.uniqid().rand(); ?>" alt="" width="200" id="productImage">
                  <br><br>
                  <label class="custom-file">
                      <input type="file" name="photo[]" id="file" accept="image/*" class="custom-file-input">
                      <span class="custom-file-control"></span>
                      <!-- <div class="invalid-feedback">
                        *Photo required
                      </div> -->
                  </label>
                  </div>
                </div>
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Update" style="cursor:pointer;" id="btnSubmit">
                  <a href="<?php echo $_SERVER['HTTP_REFERER'];?>" class="btn btn-secondary" type="button" id="btnCancel">Cancel</a>
                  <button type="button" onclick="confirmDelete('<?php echo $productDetails['name']?>',<?php echo $_GET['id']; ?>)" style="float:right" class="btn btn-danger" id="btnDelete">Delete</button>
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
      var imgFormat = ['JPG','JPEG','PNG','GIF'];
      var vidFormat = ['MP4','MOV','AVI'];
      var productForm = document.getElementById("productForm");

      document.querySelector('.custom-file-input').addEventListener('change',function(e){
        var fileName = document.getElementById("file").files[0].name;
        var ext = (fileName).split('.').pop();
        if (!imgFormat.includes(ext.toUpperCase())){
          e.target.value = "";
          e.target.nextElementSibling.innerHTML = "Invalid File Format";
        }
        else{
          var nextSibling = e.target.nextElementSibling;
          nextSibling.innerText = fileName;
          
          var productImg = document.getElementById("productImage");
          const reader = new FileReader();
          
          reader.addEventListener("load", function () {
          // convert image file to base64 string
            productImg.src = reader.result;
          }, false);

          reader.readAsDataURL(document.getElementById("file").files[0]);
        }

      })

    var qty1=document.getElementById("qty1");
    var qty1Val=document.getElementById("qty1Val");
    var qty2=document.getElementById("qty2");
    var total = document.getElementById("total");
    var totalWeight = document.getElementById("totalWeight");
    qty1.addEventListener("change", function(){
      var unitPrice = document.getElementById("unitPrice").value;
      total.value = (qty1Val.value)*unitPrice;
      total.value = parseFloat(total.value).toFixed(2);
    })

    document.getElementById("unitPrice").addEventListener("change", function(e){
      var array = document.getElementsByName("productList[]");
      if(qty1Val.value >0 && array.length == 0){
        total.value = (qty1Val.value)*e.target.value;
        total.value = parseFloat(total.value).toFixed(2);
      }
    })
    var products = [];
    var quantity = document.getElementById("quantity");
    var productList = document.createElement("input");
    productList.setAttribute("name","productList");
    productList.setAttribute("type","hidden");
    productForm.appendChild(productList);
    var data = document.getElementsByTagName("tbody")[0].rows;
    for(var i = 0; i < data.length; i++){
      var dataQty = data[i].getElementsByTagName("td");
      products.push(dataQty[1].innerText);
      addProduct(dataQty[1].innerText);
    }

    quantity.addEventListener("keydown",function(event){
      if (event.keyCode === 13) {
        // Cancel the default action, if needed
        event.preventDefault();
        // Trigger the button element with a click
        addItem();
      }
    })

    var itemTable = document.getElementById("itemTable");
    function addItem(){
      var unitPrice = document.getElementById("unitPrice").value;
      if(unitPrice >0){
        if(quantity.value > 0){
          // itemTable.innerHTML="";
          products.push(quantity.value);
          var totalPrice = 0;
          var newRow = document.createElement("tr");
          var cell1 = document.createElement("td");
          cell1.style.fontWeight="900";
          var cell2 = document.createElement("td");
          var cell3 = document.createElement("td");
          var cell4 = document.createElement("td");
          var deleteItem = document.createElement("button");
          deleteItem.type="button";
          deleteItem.className = "btn btn-danger btn-sm";
          deleteItem.innerHTML = "x";
          deleteItem.id = quantity.value;
            deleteItem.addEventListener("click", function(e) {
              var index = products.indexOf(e.target.id);
              products.splice(index,1);
              var data = document.getElementsByTagName("tbody")[0].rows;
              console.log(data);
              var isDeleted = 0;
              for(var i = 0; i < data.length; i++){
                var td = data[i].getElementsByTagName("td")[1];
                if (td.innerHTML == e.target.id && isDeleted == 0){
                  itemTable.removeChild(data[i]);
                  isDeleted = 1;
                }
                if (isDeleted == 1 && i !== data.length){
                  data[i].getElementsByTagName("td")[0].innerHTML -= 1;
                }
              }
              var itemPrice = unitPrice*e.target.id;
              // var totalVal = parseFloat(total.value) - parseFloat(itemPrice).toFixed(2);
              // total.value = totalVal.toFixed(2);
              if (data.length == 0){
                // quantity.setCustomValidity("INVALID");
              }
              // productList.setAttribute("value",quantity.value);
              deleteProduct(e.target.id);
              console.log(productList);
            }, false);
          cell4.appendChild(deleteItem);
          var data = document.getElementsByTagName("tbody")[0].rows.length;
          cell1.innerHTML = data+1;
          cell2.innerHTML = quantity.value;
          var itemTotal = parseFloat(quantity.value*unitPrice).toFixed(2);
          cell3.innerHTML = itemTotal;
          newRow.appendChild(cell1);
          newRow.appendChild(cell2);
          newRow.appendChild(cell3);
          newRow.appendChild(cell4);
          itemTable.appendChild(newRow);
          addProduct(quantity.value);
          var totalWeightVal = parseFloat(totalWeight.value) + parseFloat(quantity.value);
          var totalVal = parseFloat(total.value) + parseFloat(itemTotal);
          total.value = totalVal.toFixed(2);
          totalWeight.value = totalWeightVal.toFixed(2);
          quantity.value = "";
          quantity.setCustomValidity("");
        }
        else{
          alert("Quantity must be more than 0!");
        }
      }
      else{
        alert("Please input unit price");
      }

    }
    
    function removeItem(itemId){
      var result = confirm("Are you sure to remove this item?");
      if(result){
        var unitPrice = document.getElementById("unitPrice").value;
        console.log(itemId);
        var index = products.indexOf(itemId);
        products.splice(index,1);
        var data = document.getElementsByTagName("tbody")[0].rows;
        var isDeleted = 0;
        for(var i = 0; i < data.length; i++){
          var td = data[i].getElementsByTagName("td")[1];
          if (td.innerHTML == itemId && isDeleted == 0){
            itemTable.removeChild(data[i]);
            isDeleted = 1;
          }
          if (isDeleted == 1 && i !== data.length){
            data[i].getElementsByTagName("td")[0].innerHTML -= 1;
          }
        }
        if (data.length == 0){
          // quantity.setCustomValidity("INVALID");
        }
        // productList.setAttribute("value",quantity.value);
        deleteProduct(itemId);
      }
    }

    function addProduct(value){
      var productList = document.createElement("input");
      productList.setAttribute("name","productList[]");
      productList.setAttribute("type","hidden");
      productList.setAttribute("value",value);
      productForm.appendChild(productList);
      var array = document.getElementsByName("productList[]");
      console.log("ARRAY",array);
    }

    function deleteProduct(value){
      var productList = document.getElementsByName("productList[]");
      var unitPrice = document.getElementById("unitPrice").value;
      for (var i =0; i < productList.length; i++){
        if(productList[i].value == value){
          console.log("DELETING PRODUCT");
          productList[i].remove();
          console.log("TOTAL VAL",total.value);
          var itemPrice = parseFloat(value)*unitPrice;
          console.log(parseFloat(total.value).toFixed(2));
          console.log(itemPrice.toFixed(2));
          total.value = parseFloat(total.value).toFixed(2) - parseFloat(itemPrice).toFixed(2);
          console.log("TOTAL VAL AFTER REMOVE",total.value);
          break;
        }
      }
      console.log(productList);
    }

    function resetTable(){
      var data = document.getElementsByTagName("tbody")[0].rows;
      while (itemTable.firstChild){
        itemTable.removeChild(itemTable.lastChild);
      }
      var productList = document.getElementsByName("productList[]");
      for (var i=productList.length-1;i>=0; i--){
        productList[i].parentNode.removeChild(productList[i]);
      }
      console.log(productList);
      products = [];
      // quantity.setCustomValidity("INVALID");
      total.value = 0.00;
      console.log("FINAL PRODUCTS",products);
    }

    function updateTable(){
      var data = document.getElementsByTagName("tbody")[0].rows;
      var unitPrice = document.getElementById("unitPrice").value;
      var productListArray = document.getElementsByName("productList[]");
      var totalPrice = 0;
      for(var i=0; i < data.length;i++){
        // var price = data[i].cell[2];
        var price = data[i].cells["itemPrice"].innerHTML;
        var qty = data[i].cells["itemQty"].innerHTML;
        // console.log(price + "-" + qty);
        // console.log(productListArray[i]);
        price = parseFloat(unitPrice) * parseFloat(qty);
        price = parseFloat(price).toFixed(2);
        data[i].cells["itemPrice"].innerHTML = price;
        totalPrice += parseFloat(price);
        console.log(price);
      }
      console.log("TOTAL",totalPrice);
      total.value = parseFloat(totalPrice).toFixed(2);
    }

    function confirmDelete(product, id){
      var result = confirm("Are you sure you want to remove " + product + "?");
      if (result){
        window.location.href = "product_delete.php?id=" + id;
      }
    }
    </script>

    <!-- script for other essentials -->
    <script>
      function loadSpinner(form){
        // var spinner = document.createElement("SPAN");
        // spinner.className="spinner-border spinner-border-sm";
        console.log(form);
        var isValid = 1;
        for (var i = 0; i < form.elements.length; i++){
          var e = form.elements[i];
          if(!e.checkValidity()){
            isValid=0;
          }
        }
        if (isValid ==1){
          var btnSubmit = document.getElementById("btnSubmit");
          btnSubmit.disabled="true";
          btnSubmit.style.cursor="default";
          document.getElementById("btnCancel").removeAttribute("href");
          document.getElementById("btnGallery").disabled="true";
          document.getElementById("btnDelete").disabled="true";
          btnSubmit.value="Updating...";
          // btnSubmit.appendChild(spinner);
          return true;
        }
        else{
          return false;
        }

      }
    </script>
  
<?php require_once 'php/req/footer.php'; ?>