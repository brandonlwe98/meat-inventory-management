<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>

<?php
$image_error_messages = [];
$image_error = 0;
if ($_POST){
    $fileInfo = $_FILES['photo']['name'];
    debug_to_console("SUBMITTING FORM...");
    if (isset($_FILES['photo']) && ($_FILES['photo']['error'] == 0 || $_FILES['photo']['error'][0] == 0)){
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

        if ($image_error == 0){
          run_query("alter table ec_products auto_increment = 1");
          $unit_price = nf_store_currency($_POST['unit_price']);
          debug_to_console("QUANTITY".$_POST['quantity']);
          $quantity = nf_store_currency($_POST['quantity']);
          $total_price = nf_store_currency($_POST['total']);
          $insert = run_query("insert into ec_products (name, category, unit_price, unit, quantity, total_price, created_datetime, last_updated) values ('".$_POST['name']."', '".$_POST['category']."', '".$unit_price."', '".$_POST['unit']."', '".$quantity."', '".$total_price."', '".$current_datetime."', '".$current_datetime."')");
          debug_to_console($insert);
          $uploaded_filename = $insert.'.'.$ext;
          $move_upload = move_uploaded_file($_FILES['photo']['tmp_name'][0], 'upload/product/'.$uploaded_filename);
          run_query("update ec_products set photo = 'upload/product/".$uploaded_filename."' where id = ".$insert."");

          // if(is_array($_POST['productList']) && sizeof($_POST['productList']) > 0){
          if($_POST['unit'] == 3){
            $quantity = 0;
            if(is_array($_POST['productList']) && sizeof($_POST['productList']) > 0){
              $quantity = sizeof($_POST['productList']);
              run_query("alter table ec_products_gallery auto_increment = 1");
              foreach($_POST['productList'] as $item=>$item_v){
                debug_to_console("ITEM #");
                debug_to_console($item_v);
                $price = $item_v*$_POST['unit_price'];
                $price = number_format($price,2);
                $price = nf_store_currency($price);
                run_query("insert into ec_products_gallery (product_id,quantity,price,created_datetime) values ('".$insert."', '".nf_store_currency($item_v)."', '".$price."', '".$current_datetime."')");
              }
            }
            run_query("update ec_products set quantity = '".nf_store_currency($quantity)."' where id =".$insert."");
          }

          // if (file_exists('upload/product/'.$insert) && is_dir('upload/product/'.$insert)) {
          //   $dirPath = 'upload/product/'.$insert;
          //   $files = glob($dirPath . '*', GLOB_MARK);
          //   foreach ($files as $file) {
          //       unlink($file);
          //   }
          //   rmdir('upload/product/'.$insert);
          // }
          // mkdir('upload/product/'.$insert, 0775, true);
          // chmod('upload/product/'.$insert, 0775);
          // $checkEmptyGallery = 0;
          // foreach ($_FILES['gallery']['name'] as $galleryItem){
          //   if (empty($galleryItem)){
          //     $checkEmptyGallery = 1;
          //   }
          // }
          // if($checkEmptyGallery == 0){
          //   $file_count = count($_FILES['gallery']['name']);
          //   $imgFormat = array('JPG','JPEG','PNG','GIF');
          //   $vidFormat = array('MP4','MOV','AVI');
          //   for ($i=0; $i<$file_count;$i++){
          //     $filename = $_FILES['gallery']['name'][$i];
          //     $ext = pathinfo($filename, PATHINFO_EXTENSION);
          //     $fileType = "UNKNOWN";
          //     if (in_array(strtoupper($ext),$imgFormat)){
          //       $fileType = "image";
          //     }
          //     else if (in_array(strtoupper($ext),$vidFormat)){
          //       $fileType = "video";
          //     }
          //     run_query("alter table ec_products_gallery auto_increment = 1");
          //     $insertGallery = run_query("insert into ec_products_gallery (product_id, created_datetime, type) values (".$insert.", '".$current_datetime."','".$fileType."')");
          //     $galleryFilePath = 'upload/product/'.$insert.'/'.$insert.'_'.$insertGallery.'.'.$ext;
          //     move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $galleryFilePath);
          //     $update_query = "update ec_products_gallery set file = ".$galleryFilePath." where id =".$insertGallery."";
          //     run_query("update ec_products_gallery set file = '".$galleryFilePath."' where id =".$insertGallery."");
          //   }
          // }
          header('Location: product.php');
        }
        else{
          debug_to_console($errorMessage);
        }
    }
    else{
      $image_error == 1;
      $image_error_messages[] = 'Unable to upload image';
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
        <h4 class="tx-gray-800 mg-b-5">Create Product</h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row">
            <div class="col-xl-6">
              <div class="form-layout form-layout-4">
                <form action="product_new.php" id="productForm" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
                <div class="row">
                  <label class="col-sm-4 form-control-label">Category: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <select name="category" class="form-control select2" data-placeholder="Choose one" required>
                    <option value="" selected disabled hidden>Choose one</option>
                        <?php
                            $categories = run_query("select * from ec_categories");
                            if ($categories){
                                foreach ($categories as $categories_k=>$categories_v){
                                    ?>
                                        <option value="<?php echo $categories_v['id']; ?>"><?php echo $categories_v['name']; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>
                    <!-- <div class="invalid-feedback">
                      *Site required
                    </div> -->
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Name: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="name" class="form-control" required>
                    <!-- <div class="invalid-feedback">
                      *Name required
                    </div> -->
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <select name="unit" class="form-control select" data-placeholder="Choose one" required onchange="unitSelect(this.value)">
                      <option value="" selected disabled hidden>Choose one</option>
                      <?php
                        $units = run_query("select * from units");
                        foreach($units as $unit){
                      ?>
                          <option value="<?php echo $unit['id'];?>"><?php echo $unit['name'];?></option>;
                      <?php
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Unit Price: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" name="unit_price" id="unitPrice" class="form-control" required>
                  </div>
                </div>
                <div class="row mg-t-20" id="qty1">
                  <label class="col-sm-4 form-control-label">Quantity: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="text" id="qty1Val" name="quantity" class="form-control" required>
                  </div>
                </div>
                <div id="qty2" style="display:none;">
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
                    <button type="button" class="btn btn-info btn-sm" id="btnReset" name ="btnReset" onclick="resetTable()">Reset Table</button>
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
                          <tbody id ="itemTable"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Total:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input type="number" name="total" id="total" value="0.00" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Photo: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <img src="" alt="" width="200" id="productImage" style="margin-bottom : 5%">
                    <?php if($image_error == 1){ ?>
                      <div class="row mg-l-1">
                        <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                          <p class="text-danger" id="fileError">
                            <?php
                              for ($i = 0; $i < count($image_error_messages); $i++){
                                echo "*".$image_error_messages[$i]."<br>";
                              }
                            ?>
                          </p>
                        </div>
                      </div>
                    <?php } ?>
                    <label class="custom-file">
                      <input type="file" name="photo[]" id="file" accept="image/*" class="custom-file-input" required>
                      <span class="custom-file-control"></span>
                    </label>
                  </div>
                </div>
                <!-- <div class="row mg-t-20">
                  <label class="col-sm-4 form-control-label">Additional media: </label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <label class="custom-file">
                      <input type="file" name="gallery[]" accept="image/*,video/*" class="gallery-file-input" multiple>
                      <span class="custom-file-control"></span>
                    </label>
                  </div>
                </div> -->
                <!-- <div class="row mg-t-20">
                  <div class="col-sm-4"></div>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <p class="small" id="galleryList"></p>
                  </div>
                </div> -->
                <div class="form-layout-footer mg-t-30">
                  <input type="submit" class="btn btn-info" value="Add">
                  <a href="product.php" class="btn btn-secondary float-right">Cancel</a>
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
      var productForm = document.getElementById("productForm");

      var imgFormat = ['JPG','JPEG','PNG','GIF'];
      var vidFormat = ['MP4','MOV','AVI'];

      document.querySelector('.custom-file-input').addEventListener('change',function(e){
        var fileName = document.getElementById("file").files[0].name;
        var ext = (fileName).split('.').pop();
        if (!imgFormat.includes(ext.toUpperCase())){
          e.target.value = "";
          e.target.nextElementSibling.innerHTML = "Invalid File Format";
        }
        else{
          var nextSibling = e.target.nextElementSibling;
          nextSibling.innerHTML = fileName;

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
    total.value= 0.00;
    qty1.addEventListener("change", function(){
      var unitPrice = document.getElementById("unitPrice").value;
      total.value = (qty1Val.value)*unitPrice;
      total.value = parseFloat(total.value).toFixed(2);
    })

    document.getElementById("unitPrice").addEventListener("change", function(e){
      if(qty1Val.value >0){
        total.value = (qty1Val.value)*e.target.value;
        total.value = parseFloat(total.value).toFixed(2);
      }
    })

    function unitSelect(value){
      var unitPrice = document.getElementById("unitPrice").value;
      if (value == '3'){
        qty1Val.required = false;
        qty1Val.setCustomValidity("");
        qty2.style.display="block";
        qty1.style.display="none";
        qty1.value = 0.00;
        if (products.length > 0){
          total.value=0.00;
          for (var i = 0; i < products.length; i++){
            var totalVal = products[i] * parseFloat(unitPrice);
            total.value = parseFloat(total.value) + parseFloat(totalVal);
            total.value = parseFloat(total.value).toFixed(2);
          }
        }
        else{
          // quantity.setCustomValidity("INVALID");
          total.value = 0.00;
        }
      }
      else{
        qty1Val.required = true;
        qty2.style.display="none";
        qty1.style.display="flex";
        quantity.setCustomValidity("");
        if (qty1Val.value && unitPrice){
          total.value = parseFloat(qty1Val.value)*parseFloat(unitPrice);
          total.value = parseFloat(total.value).toFixed(2);
        }
      }
    }

    var products = [];
    var quantity = document.getElementById("quantity");
    // quantity.setCustomValidity("INVALID");
    var productList = document.createElement("input");
    productList.setAttribute("name","productList");
    productList.setAttribute("type","hidden");
    productForm.appendChild(productList);

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
            var totalVal = parseFloat(total.value) - parseFloat(itemPrice);
            total.value = totalVal.toFixed(2);
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
          quantity.value = "";
          var totalValue = parseFloat(total.value) + parseFloat(itemTotal);
          total.value = parseFloat(totalValue).toFixed(2);
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

    function addProduct(value){
      var productList = document.createElement("input");
      productList.setAttribute("name","productList[]");
      productList.setAttribute("type","hidden");
      productList.setAttribute("value",value);
      productForm.appendChild(productList);
      var array = document.getElementsByName("productList[]");
      console.log(array);
    }

    function deleteProduct(value){
      var productList = document.getElementsByName("productList[]");
      for (var i =0; i < productList.length; i++){
        if(productList[i].value == value){
          console.log("DELETING PRODUCT");
          productList[i].remove();
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

      // document.querySelector('.gallery-file-input').addEventListener('change',function(e){
      //   var gallery = e.target;
      //   console.log(e);
      //   var galleryFiles = gallery.files;
      //   var allowedFiles = 1;
      //   gallery.nextElementSibling.innerText="";
      //   document.getElementById("galleryList").innerText="";
      //   var galleryListFiles = "";
      //   for(var i=0; i < galleryFiles.length;i++){
      //     var ext = (galleryFiles[i].name).split('.').pop();
      //     galleryListFiles += galleryFiles[i].name;
      //     galleryListFiles += "\n";
      //     if (!imgFormat.includes(ext.toUpperCase()) && !vidFormat.includes(ext.toUpperCase())){
      //       allowedFiles = 0;
      //     }
      //   }
      //   if (allowedFiles == 0){
      //     gallery.nextElementSibling.innerText= "Invalid File Format";
      //     document.getElementById("galleryList").innerText = 
      //     "Supported File Formats : \n" +
      //     "Image : " + imgFormat.toString() + "\n" +
      //     "Video : " + vidFormat.toString();
      //     e.target.value = "";
      //   }
      //   else{
      //     document.getElementById("galleryList").innerText = galleryListFiles;
      //     gallery.nextElementSibling.innerText= galleryFiles.length + " file(s) selected";
      //   }
      //   console.log(document.getElementById("galleryList"));

      // })


    </script>
    

      
<?php require_once 'php/req/footer.php'; ?>