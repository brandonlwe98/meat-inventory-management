<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php' ?>
<?php require_once 'php/req/header.php'; ?>
<?php
  $pageTitle ="Order";
?>

<style>
</style>
    <div class="br-mainpanel">
      <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
        <h4 class="tx-gray-800 mg-b-5"><?php echo $pageTitle; ?></h4>
      </div>

      <div class="br-pagebody">
        <div class="br-section-wrapper">
          <div class = "row" style="margin:0px;">
            <div class="col-2" style="padding:0px;">
              <a href="order_new.php" type="button" id="btnCreate" class="btn btn-primary mg-b-10">Create</a>
            </div>
          </div>
          <div class="bd bd-gray-300 rounded table-responsive">
          <?php 
            $totalOrders = run_query("select * from ec_orders where status = 0");
            //Get number of pages (10 items per page)
            if(!$totalOrders){
              $totalOrders=[];
            }
            $pageAmount = floor(sizeof($totalOrders)/10);

            //if there are remainder, print an extra page
            if (sizeof($totalOrders)%10 !== 0){
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
                  <th>#</th>
                  <th>CUSTOMER</th>
                  <!-- <th>ITEMS</th> -->
                  <th>Contact</th>
                  <th>DELIVERY METHOD</th>
                  <th>DELIVERY DATE</th>
                  <th>TOTAL</th>
                  <th>REMARKS</th>
                  <th style="text-align:right">Last Updated</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    if ($totalOrders){
                      $count = 0;
                      $order = [];

                      $orders = run_query("select * from ec_orders where status=0 ORDER BY last_updated desc limit ".(($pageNo-1)*10).", 10");
                      if($orders){
                        foreach ($orders as $order_k=>$order_v){
                            $customer = run_query("select * from customers where id =".$order_v['customer_id'])[0];
                            $items = run_query("select * from ec_order_items where order_id=".$order_v['id']);
                            $customItems = run_query("select * from ec_custom_items where order_id=".$order_v['id']);
                            $itemQty=0;
                            if($items){
                              $itemQty = sizeof($items);
                            }
                            if($customItems){
                              $itemQty+=sizeof($customItems);
                            }
                            array_push($order,$order_v);
                            $count++;
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $count+(($pageNo-1)*10); ?></th>
                                    <td><?php echo $customer['name']; ?></td>
                                    <!-- <td><?php echo $itemQty; ?></td> -->
                                    <td><?php echo $customer['phone']; ?></td>
                                    <td>
                                      <?php 
                                        if($order_v['delivery_method'] != null){
                                          echo $order_v['delivery_method'];
                                        }
                                        else{
                                      ?>
                                        <span class="tx-danger">Not Decided</span>
                                      <?php
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
                                          $deliveryDate = "To Be Decided";
                                          echo $deliveryDate;
                                        }
                                      ?>
                                    </td>
                                    <td><?php echo nf_view_currency($order_v['total']); ?></td>
                                    <td><?php echo $order_v['remarks']; ?></td>
                                    <td style="text-align:right"><?php echo $order_v['last_updated']; ?>
                                      <br><br>
                                      <button class="btn btn-outline-info" onclick="edit('<?php echo $order[$count-1]['id']; ?>')" style="cursor:pointer;">
                                        <i class="fa fa-pencil" style="font-size:15px"></i>
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
              <a href="order.php?page=<?php echo $pageNo-1; ?>">&laquo;</a>
              <?php
              }
            ?>
            <?php
              for ($page = 1; $page <= $pageAmount; $page++){
            ?>
            <?php
              if($page == $pageNo){ ?>
                <a href='order.php?page=<?php echo $page ?>' class="active"><?php echo $page; ?></a>
            <?php
              }
              else{ ?>
              <a href='order.php?page=<?php echo $page ?>'><?php echo $page;?></a>
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
              <a href="order.php?page=<?php echo $pageNo+1; ?>">&raquo;</a>
              <?php
              }
            ?>
          </div><!-- pagination -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->
      <script>
        function edit(orderId){
          window.location.href = "order_edit.php?id=" + orderId;
        }
      </script>
<?php require_once 'php/req/footer.php'; ?>