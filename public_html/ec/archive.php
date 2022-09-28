<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php' ?>
<?php require_once 'php/req/header.php'; ?>
<?php
  $pageTitle ="Archive";
  $currentDate = new DateTime($current_datetime);
  debug_to_console($current_datetime);
  $latestYear = $currentDate->format("Y");
  if(isset($_GET['month']) && isset($_GET['year'])){
    $currentDate = new DateTime('1-'.$_GET['month'].'-'.$_GET['year']);
  }
  $pageTitle ="Archive";
  $startYear = "2020";
  $currentMonth = $currentDate->format("M");
  $currentMonthNo = $currentDate->format("m");
  $currentYear = $currentDate->format("Y");
  $currentMonthDays = cal_days_in_month(CAL_GREGORIAN,$currentMonthNo,$currentYear);
  $startMonth = new DateTime('1-'.$currentMonthNo.'-'.$currentYear);
  $startMonth = $startMonth->format('Y-m-d H:i');
  $endMonth = new DateTime($currentMonthDays.'-'.$currentMonthNo.'-'.$currentYear.' 23:59:59');
  $endMonth = $endMonth->format('Y-m-d H:i');
  // debug_to_console($startMonth);
  // debug_to_console($endMonth);
?>

<style>
.activeFilter{
  color:#2fa0ef !important;
}
.btn:hover{
  cursor:pointer;
}
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5"><?php echo $pageTitle; ?></h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class="row mg-t-20" style="padding-bottom:1%">
          <label class="col-sm-1 form-control-label" style="margin-top:auto;margin-bottom:auto">Month:</label>
            <div class="col-sm-2 mg-t-10 mg-sm-t-0">
              <select name="archiveMonth" class="form-control select2" data-placeholder="Choose one" required id="archiveMonth" style="padding-right:0%">
                <option value="" selected disabled hidden>Month</option>
                  <?php
                    for($i=1;$i <= 12;$i++){
                  ?>
                    <option value="<?php echo $i; ?>" <?php if($currentMonthNo == $i){echo "selected";}?>>
                      <?php 
                        $dateObj = DateTime::createFromFormat('!m', $i);
                        $month = $dateObj->format('F'); // March
                        echo $month;
                      ?>
                    </option>
                  <?php
                    }
                  ?>
              </select>
            </div>
            <label class="col-sm-1 form-control-label" style="margin-top:auto;margin-bottom:auto">Year:</label>
            <div class="col-sm-2 mg-t-10 mg-sm-t-0">
              <select name="archiveYear" class="form-control select2" data-placeholder="Choose one" required id="archiveYear" style="padding-right:0%">
                <option value="" selected disabled hidden>Month</option>
                  <?php
                    for($i=$startYear;$i <= $latestYear;$i++){
                  ?>
                    <option value="<?php echo $i; ?>" <?php if ($i == $currentYear){echo "selected";}?>>
                      <?php echo $i; ?>
                    </option>
                  <?php
                    }
                  ?>
              </select>
            </div>
            <div class="col-sm-1" style="margin-left:0%;">
              <button type="button" id="btnCreate" class="btn btn-primary mg-b-10" onclick="filterArchive()">Go</button>
            </div>
            <div class="col-sm-4" style="margin-left:8%;text-align:right;">
              <button type="button" style="cursor:pointer" class="btn btn-primary mg-b-10" id="btnReport" name ="btnReport" onclick="generateReport('<?php echo $current_datetime;?>')">Today's orders</button>
            </div>
          </div>
          <div class="bd bd-gray-300 rounded table-responsive">
          <?php 
            $archived = run_query("select * from ec_orders where status=1 and paid_date>='".$startMonth."' and paid_date<='".$endMonth."' order by last_updated desc");
            if(!$archived){
              $archived = [];
            }
            else{
              // debug_to_console($archived);
            }
            //Get number of pages (10 items per page)
            $pageAmount = floor(sizeof($archived)/10);

            //if there are remainder, print an extra page
            if (sizeof($archived)%10 !== 0){
              $pageAmount +=1;
            }
            
            $pageNo = 1;
            if (isset($_GET['page'])){
              $pageNo = $_GET['page'];
            }
          ?>
            <table class="table table-hover mg-b-0">
              <thead>
                <?php
                  $activeFilter="";
                  if(isset($_GET['filter']) && $_GET['filter'] == 'delivery_status'){
                    $activeFilter = 1;
                  }
                  if(isset($_GET['filter']) && $_GET['filter'] == 'delivery_date'){
                    $activeFilter = 2;
                  }
                ?>
                <tr>
                  <th>#</th>
                  <th>CUSTOMER</th>
                  <!-- <th>ITEMS</th> -->
                  <th>Contact</th>
                  <th>DELIVERY METHOD</th>
                  <th>DELIVERY DATE <a href="archive.php?filter=delivery_date&page=<?php echo $pageNo;?><?php if(isset($_GET['month'])){echo '&month='.$_GET['month'].'&year='.$_GET['year'];}?>" style="color:black;margin-left:5%" class="<?php if($activeFilter == 2){echo "activeFilter";}?>"><i class="fa fa-navicon"></i></a></th>
                  <th>TOTAL</th>
                  <th>REMARKS</th>
                  <th>DELIVERY STATUS <a href="archive.php?filter=delivery_status&page=<?php echo $pageNo;?><?php if(isset($_GET['month'])){echo '&month='.$_GET['month'].'&year='.$_GET['year'];}?>" style="color:black;margin-left:3%" class="<?php if($activeFilter == 1){echo "activeFilter";}?>"><i class="fa fa-navicon"></i></a></th>
                  <th style="text-align:right">Date of Payment</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if (sizeof($archived)>0){
                      $count = 0;
                      $order = [];
                      $orders = run_query("select * from ec_orders where status=1 and paid_date>='".$startMonth."' and paid_date<='".$endMonth."' order by delivery_status asc, last_updated desc limit ".(($pageNo-1)*10).", 10");
                      if($activeFilter !== ""){
                        $category = $_GET['filter'];
                        $direction = "asc";
                        if($category == 'delivery_date'){
                          $direction = "desc";
                        }
                        $orders = run_query("select * from ec_orders where status=1 and paid_date>='".$startMonth."' and paid_date<='".$endMonth."' ORDER BY ".$category." ".$direction." limit ".(($pageNo-1)*10).", 10");
                      }
                      if($orders){
                        foreach ($orders as $order_k=>$order_v){
                            $customer = run_query("select * from customers where id =".$order_v['customer_id'])[0];
                            $items = run_query("select * from ec_order_items where order_id=".$order_v['id']);
                            $customItems = run_query("select * from ec_custom_items where order_id=".$order_v['id']."");
                            $itemQty=0;
                            if($items){
                              $itemQty = sizeof($items);
                            }
                            if($customItems){
                              $itemQty += sizeof($customItems);
                            }
                            array_push($order,$order_v);
                            $count++;
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $count+(($pageNo-1)*10); ?></th>
                                    <td><?php echo $customer['name']; ?></td>
                                    <!-- <td><?php echo $itemQty; ?></td> -->
                                    <td><?php echo $customer['phone']?></td>
                                    <td>
                                      <?php 
                                        if($order_v['delivery_method'] != null){
                                          echo $order_v['delivery_method'];
                                        }
                                      ?>
                                    </td>
                                    <td>
                                    <?php 
                                        $deliveryDate = $order_v['delivery_date'];
                                        if((bool)strtotime($order_v['delivery_date']) == 1) { //valid date
                                          $deliveryDate = date_create($order_v['delivery_date']);
                                          echo date_format($deliveryDate,"d/m/Y H:i A");
                                        }
                                        else{
                                          echo $deliveryDate;
                                        }
                                      ?>
                                    </td>
                                    <td><?php echo nf_view_currency($order_v['total']); ?></td>
                                    <td><?php echo $order_v['remarks']; ?></td>
                                    <td>
                                      <?php
                                        if($order_v['delivery_status'] == 0){
                                      ?>
                                        <span class="tx-danger">Not Delivered</span>
                                      <?php
                                        }
                                        else{
                                      ?>
                                        <span class="tx-success">Delivered</span>
                                      <?php
                                        }
                                      ?>
                                    </td>
                                    <?php
                                      $paymentDate = date_create($order_v['paid_date']);
                                    ?>
                                    <td style="text-align:right"><?php echo date_format($paymentDate,"d/m/Y H:i A"); ?>
                                      <br><br>
                                      <button class="btn btn-outline-success" onclick="deliver('<?php echo $order[$count-1]['id']; ?>','<?php echo $customer['name'];?>','<?php echo $itemQty;?>')" style="cursor:pointer;" <?php if($order[$count-1]['delivery_status']==1){echo "hidden";}?>>
                                        <i class="fa fa-truck" style="font-size:15px"></i>
                                      </button>
                                      <button class="btn btn-outline-info" onclick="view('<?php echo $order[$count-1]['id']; ?>')" style="cursor:pointer;">
                                        <i class="fa fa-folder-open-o" style="font-size:15px"></i>
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
              } 
              else{
                $pageRef1 = "archive.php?page=".($pageNo-1);
                if(isset($_GET['month'])&&isset($_GET['year'])){
                  $pageRef1 = "archive.php?page=".($pageNo-1)."&month=".$_GET['month']."&year=".$_GET['year'];
                }
              ?>
                <a href="<?php echo $pageRef1;?>">&laquo;</a>
              <?php
              }
            ?>
            <?php
              for ($page = 1; $page <= $pageAmount; $page++){
            ?>
            <?php
              if($page == $pageNo){ ?>
                <a href='' class="active"><?php echo $page; ?></a>
            <?php
              }
              else{
                $pageRef2 = "archive.php?page=".$page;
                if(isset($_GET['month'])&&isset($_GET['year'])){
                  $pageRef2 = "archive.php?page=".$page."&month=".$_GET['month']."&year=".$_GET['year'];
                }
            ?>
                <a href='<?php echo $pageRef2; ?>'><?php echo $page;?></a>
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
              }
              else{
                $pageRef3 = "archive.php?page=".($pageNo+1);
                if(isset($_GET['month'])&&isset($_GET['year'])){
                  $pageRef3 = "archive.php?page=".($pageNo+1)."&month=".$_GET['month']."&year=".$_GET['year'];
                }
              ?>
                <a href="<?php echo $pageRef3; ?>">&raquo;</a>
              <?php
              }
              ?>
          </div><!-- pagination -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
      <script>
        function view(orderId){
          window.location.href = "archive_view.php?id=" + orderId;
        }

        function deliver(orderId,customer,itemQty){
          var res=confirm("Confirm order delivery for " + customer + " - " + itemQty + " items?");
          if(res){
            $.ajax({
              type:"POST",
              url:"ajax.php",
              data:{
                "delivery": 1,
                "order_id": orderId,
              },
              success:function(val){
                window.location.href = "archive.php";
              }
            });
          }
        }

        function filterArchive(){
          var month = document.getElementById("archiveMonth").value;
          var year = document.getElementById("archiveYear").value;
          setCookie("archiveMonth", month, 30);
          setCookie("archiveYear", year, 30);
          window.location.href="archive.php?month="+month+"&year="+year;
        }

        function generateReport(currentDate){
          $.ajax({
            type:"POST",
            url:"ajax.php",
            data:{
              "generateOrderToday":1,
            },
            success:function(result){
              console.log(result);
              var data = new Blob([result], {type: 'text/plain'});
              textFile = window.URL.createObjectURL(data);
              var a = document.createElement("a");
              a.href = textFile;
              a.download = "Today-Orders.txt";
              document.body.appendChild(a);
              a.click();
              setTimeout(function() {
                  document.body.removeChild(a);
                  window.URL.revokeObjectURL(textFile);  
              }, 0); 
              console.log(textFile);
              
            }
          });
        }

        // document.getElementById("customers").addEventListener("change",function(e){
        //   console.log(e.target.value);
        //   window.location.href="archive.php?customer="+e.target.value;
        // })
      </script>
<?php require_once 'php/req/footer.php'; ?>