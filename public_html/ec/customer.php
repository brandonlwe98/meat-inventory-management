<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php' ?>
<?php require_once 'php/req/header.php'; ?>
<?php
  $pageTitle ="Customer";
  $customers = run_query("select * from customers");
?>

<style>
.btn-outline-danger[disabled]:hover{
  cursor:default!important;
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
              <a href="customer_new.php" type="button" id="btnCreate" class="btn btn-primary mg-b-10">Create</a>
            </div>
            <div class="col-sm-6 mg-t-10 mg-sm-t-0">
              <input type="text" name="searchCustomer" id="searchCustomer" class="form-control" placeholder="Search Customer">
            </div>
            <label class="col-sm-2 form-control-label text-right" style="margin-top:auto;margin-bottom:auto">Filter By:</label>
            <div class="col-2 text-right" style="padding:0px;">
            <?php
              $filterVal = null;
              if(isset($_GET['filter'])){
                $filterVal = $_GET['filter'];
              }
            ?>
              <select name="category" class="form-control select2" data-placeholder="Choose one" onchange="filter(this.value)" required>
                <option value="" disabled <?php if(!$filterVal){echo "selected";}?> hidden>Choose one</option>
                <option value="name" <?php if($filterVal == 'name'){echo "selected";}?>>Name</option>
                <option value="last_updated" <?php if($filterVal == 'last_updated'){echo "selected";}?>>Last Updated</option>
              </select>
            </div>
          </div>
          <div class="bd bd-gray-300 rounded table-responsive">
          <?php 
            $totalCustomers = run_query("select * from customers");
            //Get number of pages (10 items per page)
            if(!$totalCustomers){
              $totalCustomers=[];
            }
            $pageAmount = floor(sizeof($totalCustomers)/10);

            //if there are remainder, print an extra page
            if (sizeof($totalCustomers)%10 !== 0){
              $pageAmount +=1;
            }
            
            $pageNo = 1;
            if (isset($_GET['page'])){
              $pageNo = $_GET['page'];
            }
          ?>
            <table class="table table-hover mg-b-0">
              <thead>
                <tr>
                  <th style="width:8%">#</th>
                  <th style="width:15%">NAME</th>
                  <th style="width:15%">PHONE NO</th>
                  <th style="width:20%">ADDRESS</th>
                  <th style="width:15%">NOTE</th>
                  <th style="text-align:right; width:20%">Last Updated</th>
                </tr>
              </thead>
              <tbody id="customerList">
                <?php
                    if ($totalCustomers){
                      $count = 0;
                      $customer = [];

                      // $customers = run_query("select * from customers ORDER BY last_updated desc limit ".(($pageNo-1)*10).", 10");
                      $customers = run_query("select * from customers ORDER BY last_updated desc");
                      if(isset($_GET['filter']) && $_GET['filter'] == 'name'){
                        // $customers = run_query("select * from customers ORDER BY ".$_GET['filter']." asc limit ".(($pageNo-1)*10).", 10");
                        $customers = run_query("select * from customers ORDER BY ".$_GET['filter']." asc");
                      }
                      if($customers){
                        foreach ($customers as $customer_k=>$customer_v){
                            array_push($customer,$customer_v);
                            $count++;
                            ?>
                                <tr class="rowCustomer">
                                    <th scope="row"><?php echo $count+(($pageNo-1)*10); ?></th>
                                    <td><?php echo $customer_v['name']; ?></td>
                                    <td><?php echo $customer_v['phone']; ?></td>
                                    <td style="white-space:pre-wrap;"><?php echo $customer_v['address']; ?></td>
                                    <td style="white-space:pre-wrap;"><?php echo $customer_v['note']; ?></td>
                                    <td style="text-align:right"><?php echo $customer_v['last_updated']; ?>
                                      <br><br>
                                      <button class="btn btn-outline-info" onclick="edit('<?php echo $customer[$count-1]['id']; ?>')" style="cursor:pointer;">
                                        <i class="fa fa-pencil" style="font-size:15px"></i>
                                      </button>
                                      <button class="btn btn-outline-danger" onclick="confirmDelete('<?php echo $customer[$count-1]['name']; ?>', '<?php echo $customer[$count-1]['id']; ?>')" style="cursor:pointer;" <?php if($customer_v['total_orders']>0){echo "disabled";}?>>
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
          <!-- <div class="pagination" id="page" style="float:right"> -->
            <?php
              // $filter="";
              // if(isset($_GET['filter'])){
              //   $filter = "&filter=".$_GET['filter'];
              // }
              // if($pageNo == 1){
              ?>
              <!-- <a class="disabled">&laquo;</a>  -->
              <!-- disabled if 1st page -->
              <?php
                // } else{
              ?>
              <!-- <a href="customer.php?page=<?php //echo ($pageNo-1).$filter; ?>">&laquo;</a> -->
              <?php
              // }
            ?>
            <?php
              // for ($page = 1; $page <= $pageAmount; $page++){
            ?>
            <?php
              // if($page == $pageNo){ ?>
                <!-- <a href='customer.php?page=<?php //echo ($page).$filter; ?>' class="active"><?php //echo $page; ?></a> -->
            <?php
              // }
              // else{ ?>
              <!-- <a href='customer.php?page=<?php //echo ($page).$filter; ?>'><?php //echo $page;?></a> -->
              <?php
              // } 
              ?>
            <?php
              // }
            ?>
            <?php
              // if($pageNo == $pageAmount){
              ?>
              <!-- <a class="disabled">&raquo;</a>  -->
              <!-- disabled if last page -->
              <?php
                // } else{
              ?>
              <!-- <a href="customer.php?page=<?php //echo ($pageNo+1).$filter; ?>">&raquo;</a> -->
              <?php
              // }
            ?>
          <!-- </div> -->
          <!--/pagination-->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
      <script>
        function edit(customerId){
          window.location.href = "customer_edit.php?id=" + customerId;
        }

        function confirmDelete(customer,id) {
          var result = confirm("Are you sure you want to remove " + customer + "?");
          if (result){
            window.location.href = "customer_delete.php?id=" + id;
          }
        }

        function filter(value){
          window.location.href="customer.php?filter="+value;
        }

        $(document).ready(function(){
          $("#searchCustomer").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            // console.log(value);
            $("#customerList").find('.rowCustomer').filter(function() {
              // console.log($(this).toggle($(this).text().toLowerCase()));
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
          });
        });
      </script>
<?php require_once 'php/req/footer.php'; ?>