<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php' ?>
<?php require_once 'php/req/header.php'; ?>
<?php
  $pageTitle ="Product";
  $categories = run_query("select * from ec_categories order by created_datetime desc");
?>

<style>
.btnCategory{
  border-radius:100%;
  border-color:grey;
}

.btnCategory:hover{
  cursor:pointer;
  opacity:70%;
}
#btnCategoryAll{
  background-color:#212529;
}
#btnPork{
  background-color:#910707;
}
#btnBeef{
  background-color:#c65a03;
}
#btnSeafood{
  background-color:#084684;
}
.btn-warning.disabled{
  color:black!important;
  border-color:black!important;
}
.disabled:hover{
  opacity:.65;
  cursor:default;
}
.noStock{
  background-color:#7f2222!important;
  color:white!important;
}
.noStock:hover{
  background-color:#454545!important;
}
.nav-tabs .nav-link{
  color:black!important;
}
.nav-tabs .nav-link.active{
  background-color:#aecfef !important;
  color:white!important;
}
.nav-item:hover{
  background-color:#5ba7f1;
  border-radius:4px!important;
  color:white;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5"><?php echo $pageTitle; ?></h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class = "row" style="margin:0px;">
            <div class="col-2" style="padding:0px;">
              <a href="product_new.php" type="button" id="btnCreate" class="btn btn-primary mg-b-10">Create</a>
            </div>
          </div>
          <ul class="nav nav-tabs justify-content-end">
            <li class="nav-item">
              <a class="nav-link <?php if($_COOKIE["currentProductCat"] == 0){echo "active";}?>" href="javascript:setCookie('currentProductCat','0','30');window.location.href='product.php';">All</a>
            </li>
            <?php
              foreach($categories as $category){
            ?>
                <li class="nav-item">
                  <a class="nav-link <?php if($_COOKIE["currentProductCat"] == $category['id']){echo "active";}?>" href="javascript:setCookie('currentProductCat','<?php echo $category['id'];?>','30');window.location.href='product.php';"><?php echo $category['name'];?></a>
                </li>
            <?php
              }
            ?>
          </ul>
          <div class="bd bd-gray-300 rounded table-responsive">
          <?php 
            $totalProducts = run_query("select * from ec_products");
            if ($_COOKIE["currentProductCat"]>0){
              $totalProducts = run_query("select * from ec_products where category =".$_COOKIE["currentProductCat"]."");
            }
            //Get number of pages (10 items per page)
            if(!$totalProducts){
              $totalProducts=[];
            }
            $pageAmount = floor(sizeof($totalProducts)/10);

            //if there are remainder, print an extra page
            if (sizeof($totalProducts)%10 !== 0){
              $pageAmount +=1;
            }
            
            $pageNo = 1;
            if (isset($_GET['page'])){
              $pageNo = $_GET['page'];
            }
            $col3="CATEGORY";
            $col4="NAME";
          ?>
            <table class="table table-hover mg-b-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th style="width:30%;"></th>
                  <th><?php echo $col3; ?></th>
                  <th><?php echo $col4; ?></th>
                  <th>UNIT PRICE</th>
                  <th>QUANTITY</th>
                  <th>UNIT</th>
                  <th style="text-align:right">Last Updated</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($totalProducts){
                      $count = 0;
                      $product = [];

                      if($_COOKIE['currentProductCat']>0){
                        $products = run_query("select * from ec_products where category=".$_COOKIE['currentProductCat']." ORDER BY last_updated desc limit ".(($pageNo-1)*10).", 10");
                      }
                      else{
                        $products = run_query("select * from ec_products ORDER BY last_updated desc limit ".(($pageNo-1)*10).", 10");
                      }
                      if($products){
                        foreach ($products as $product_k=>$product_v){
                            $category = run_query("select * from ec_categories where id =".$product_v['category']."")[0];
                            array_push($product,$product_v);
                            $count++;
                            ?>
                                <tr <?php if(nf_view_currency($product_v['quantity']) == 0){
                                  echo "class='noStock'";
                                }?>>
                                    <td><?php echo $count+(($pageNo-1)*10); ?></td>
                                    <td><img src="<?php echo $product_v['photo'].'?'.uniqid().rand(); ?>" alt="" width="200"></td>
                                    <td><?php echo $category['name']?></td>
                                    <td><?php echo $product_v['name']; ?></td>
                                    <td><?php echo nf_view_currency($product_v['unit_price']); ?></td>
                                    <?php
                                      $unit = run_query("select * from units where id = ".$product_v['unit']."")[0];
                                    ?>
                                    <td><?php echo nf_view_currency($product_v['quantity']); ?></td>
                                    <td><?php echo $unit['name']; ?></td>
                                    <td style="text-align:right"><?php echo $product_v['last_updated']; ?>
                                      <br><br>
                                      <button class="btn btn-outline-info" onclick="edit('<?php echo $product[$count-1]['id']; ?>')" style="cursor:pointer;">
                                        <i class="fa fa-pencil" style="font-size:15px"></i>
                                      </button>
                                      <button class="btn btn-outline-danger" onclick="confirmDelete('<?php echo $product[$count-1]['name']; ?>', '<?php echo $product[$count-1]['id']; ?>')" style="cursor:pointer;">
                                        <i class="fa fa-close" style="font-size:15px"></i>
                                      </button>
                                    </td>
                                </tr>
                            <?php
                        }
                      }
                    }
                  ?>
              </tbody>
            </table>
          </div><!-- bd -->

          <!--pagination-->
          <div class="pagination" id="page" style="float:right">
            <?php
              if($pageNo == 1){
              ?>
              <a class="disabled">&laquo;</a> <!-- disabled if 1st page -->
              <?php
                } else{
              ?>
              <a href="product.php?page=<?php echo $pageNo-1; ?>">&laquo;</a>
              <?php
              }
            ?>
            <?php
              for ($page = 1; $page <= $pageAmount; $page++){
            ?>
            <?php
              if($page == $pageNo){ ?>
                <a href='product.php?page=<?php echo $page ?>' class="active"><?php echo $page; ?></a>
            <?php
              }
              else{ ?>
              <a href='product.php?page=<?php echo $page ?>'><?php echo $page;?></a>
              <?php
              } 
              ?>
            <?php
              }
            ?>
            <?php
              if($pageNo == $pageAmount){
              ?>
              <a class="disabled">&raquo;</a> <!-- disabled if last page -->
              <?php
                } else{
              ?>
              <a href="product.php?page=<?php echo $pageNo+1;?>">&raquo;</a>
              <?php
              }
            ?>
          </div><!-- pagination -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
      <script>
        function edit(productId, siteId){
          var editLink = "product_edit.php?id=" + productId;
          if (siteId == 6){
            editLink = "finaz_event_edit.php?id=" + productId;
          }
          window.location.href = editLink;
        }

        function confirmDelete(product,id,siteId) {
          var result = confirm("Are you sure you want to remove " + product + "?");
          if (result){
            window.location.href = "product_delete.php?id=" + id+"&siteId="+siteId;
          }
        }
      </script>
<?php require_once 'php/req/footer.php'; ?>