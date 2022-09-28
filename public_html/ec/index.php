<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?>
<?php
  $currentDate = new DateTime($current_datetime);
  $currentWeek = $currentDate->format("W");
  $currentMonth = $currentDate->format("M");
  $currentMonthNo = $currentDate->format("m");
  $currentYear = $currentDate->format("Y");
  $grandTotalSales = 0;
  $orders = run_query("select * from ec_orders where status = 1");
  $currentWeekOrders = [];
  $currentMonthOrders = [];
  $lastWeekOrders = [];
  $products = [];
  $pastMonths = [0,0,0,0,0,0];
  if($orders){
    foreach($orders as $order){
      if($order['paid_date']){
        $grandTotalSales = $grandTotalSales + nf_view_currency($order['total'] - $order['delivery_fee']);
        $date = new DateTime($order['paid_date']);
        $week = $date->format("W");
        $month = $date->format("M");
        $year = $date->format("Y");
        // debug_to_console($year);
        $orderMonthNo = $date->format("m");
        $orderYearNo = $date->format("y");
      }
      else{
        $date = null;
      }
      if($week == $currentWeek && $year == $currentYear){
        array_push($currentWeekOrders,$order);
      }
      if($month == $currentMonth && $year == $currentYear){
        array_push($currentMonthOrders,$order);
      }
      for($i = 1;$i<=6;$i++){
        $pastMonth = date('m', strtotime(date('Y-m')." -".$i." month"));
        $pastYear = date('y', strtotime(date('Y-m')." -".$i." month"));
        if(($pastMonth) == ($orderMonthNo) && ($orderYearNo) == ($pastYear)){
          $revenue = nf_view_currency($order['total'] - $order['delivery_fee']);
          $pastMonths[$i-1] += $revenue;
        }
      }
    }
  }

  $currentWeekSales = 0.00;
  $currentMonthSales = 0.00;
  $topSales = 0.00;
  $topSalesId = null;
  $customers = [];
  foreach($currentWeekOrders as $i){
    $revenue = nf_view_currency($i['total']) - nf_view_currency($i['delivery_fee']);
    $currentWeekSales += $revenue;
  }
  foreach($currentMonthOrders as $i){
    $revenue = nf_view_currency($i['total']) - nf_view_currency($i['delivery_fee']);
    $currentMonthSales += $revenue;
    array_push($customers,$i['customer_id']);
    if($topSales < $revenue){
      $topSales = $revenue;
      $topSalesId = $i['id'];
    }
  }
  $popular="";
  if(sizeof($customers)>0){
    $values = array_count_values($customers);
    arsort($values);
    $popular = array_slice(array_keys($values), 0, 1, true);
    $popular = run_query("select * from customers where id = ".$popular[0]."")[0]['name'];
  }

?>
<?php require_once 'php/req/header.php'; ?>
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
.btn:hover{
  cursor:pointer;
}

.bg-red {
  background-color: rgb(215 2 6);
}

div#salesPerformanceChart .ct-label{
  color:white;
}
.sales-performance .ct-grid{
  stroke: rgb(253 251 251);
}

.sales-performance .ct-series-a .ct-line, .sales-performance .ct-series-a .ct-point{
  stroke: #7ab4fb;
}

.sales-performance .ct-series-b .ct-line, .sales-performance .ct-series-b .ct-point{
  stroke: #d88df7;
}
</style>
    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
      <div class="pd-30">
        <!-- <h4 class="tx-gray-800 mg-b-5 ">Dashboard</h4> -->
        <h1 class="mg-b-5" style="text-align:center;color:black"><?php echo (new DateTime($current_datetime))->format("d")." ".$currentDate->format("F")." ".$currentDate->format("Y");?></h1>
        <!-- <p class="mg-b-0">Do big things with Bracket, the responsive bootstrap 4 admin template.</p> -->
      </div><!-- d-flex -->

      <div class="br-pagebody mg-t-5 pd-x-30">
        <div class="row row-sm">
          <div class="col-sm-6 col-xl-3 mg-t-20 mg-sm-t-0">
            <div class="bg-danger rounded overflow-hidden">
              <div class="pd-25 d-flex align-items-center">
                <i class="ion ion-ios-paper tx-60 lh-0 tx-white op-7"></i>
                <div class="mg-l-20">
                  <p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">This Week's Orders</p>
                  <p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?php echo sizeof($currentWeekOrders);?></p>
                  <?php
                    $msg = "Jia You!!";
                    if(sizeof($currentWeekOrders) > 0){
                      if(sizeof($currentMonthOrders) > 0){
                        $percentage = sizeof($currentWeekOrders)/sizeof($currentMonthOrders) * 100;
                        $msg = number_format($percentage,2)."% of this month's(".$currentMonth.") orders";
                      }
                    }
                  ?>
                  <span class="tx-11 tx-roboto tx-white-6"><?php echo $msg;?></span>
                </div>
              </div>
            </div>
          </div><!-- col-3 -->
          <div class="col-sm-6 col-xl-3">
            <div class="bg-teal rounded overflow-hidden">
              <div class="pd-25 d-flex align-items-center">
                <i class="ion ion-cash tx-60 lh-0 tx-white op-7"></i>
                <div class="mg-l-20">
                  <p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">This Week's Sales</p>
                  <p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1">RM<?php echo $currentWeekSales;?></p>
                  <?php
                    $msg = "";
                    $trueSales = 0.00;
                    if($currentMonthSales == 0){
                      $currentMonthSales = 1.00;
                    }
                    else{
                      $trueSales = $currentMonthSales;
                    }
                    $percentage = $currentWeekSales/$currentMonthSales * 100;
                    $msg = number_format($percentage,2)."% of this month's sales";
                  ?>
                  <span class="tx-11 tx-roboto tx-white-6"><?php echo $msg;?></span>
                </div>
              </div>
            </div>
          </div><!-- col-3 -->
          <div class="col-sm-6 col-xl-3 mg-t-20 mg-xl-t-0">
            <div class="bg-primary rounded overflow-hidden">
              <div class="pd-25 d-flex align-items-center">
                <i class="ion ion-ios-paper tx-60 lh-0 tx-white op-7"></i>
                <div class="mg-l-20">
                  <p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">THIS MONTH'S ORDERS</p>
                  <p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1"><?php echo sizeof($currentMonthOrders);?></p>
                  <span class="tx-11 tx-roboto tx-white-6">Top Customer : <?php echo $popular;?></span>
                </div>
              </div>
            </div>
          </div><!-- col-3 -->
          <div class="col-sm-6 col-xl-3 mg-t-20 mg-xl-t-0">
            <div class="bg-br-primary rounded overflow-hidden">
              <div class="pd-25 d-flex align-items-center">
                <i class="ion ion-cash tx-60 lh-0 tx-white op-7"></i>
                <div class="mg-l-20">
                  <p class="tx-10 tx-spacing-1 tx-mont tx-medium tx-uppercase tx-white-8 mg-b-10">This month's sales</p>
                  <p class="tx-24 tx-white tx-lato tx-bold mg-b-2 lh-1">RM<?php echo $trueSales;?></p>
                  <span class="tx-11 tx-roboto tx-white-6">Top Grossed Order : <a href="archive_view.php?id=<?php echo $topSalesId;?>" onclick="javascript:setCookie('currentMenuItem', 'archive', 30);" style="color:#bfdbf8">RM<?php echo $topSales;?></a></span>
                </div>
              </div>
            </div>
          </div><!-- col-3 -->
        </div><!-- row -->

        <div class="row row-sm mg-t-20">
        
          <div class="col-8" style="margin-bottom:2%;">
            <div class="card bd-0 shadow-base pd-30" style="background-color:#445bff;">
              <h6 class="tx-40 tx-uppercase tx-inverse tx-semibold tx-spacing-1 tx-gray-100">What's New</h6>
              <p class="mg-b-25 tx-gray-400">Summary of the new updates (version 1.6) 26/5/2021</p>

              <label class="tx-18 tx-gray-200 mg-b-10">- Updated customer details to have <b>notes.</b></label>
              <label class="tx-18 tx-gray-200 mg-b-10">- Updated product details to show <b>Total Weight</b> for kg/pkt items.</label>
            </div><!-- card -->
          </div>

          <div class="col-4" style="margin-bottom:2%;">
            <div class="card bd-0 shadow-base pd-30" style="background-color:#1D2939;">
              <h6 class="tx-20 tx-uppercase tx-inverse tx-semibold tx-spacing-1" style="margin-bottom:5%;color:white;">Previous Month Sales</h6>

              <?php
                for($i=1;$i<=6;$i++){
                  $salesMonth = date('M', strtotime(date('Y-m')." -".$i." month"));
                  // debug_to_console($salesMonth);
              ?>
                  <span class="tx-15 tx-gray-600 mg-b-10" style="color:white;"><?php echo $salesMonth;?>.
                    <label class="tx-18 tx-gray-800 mg-b-10" style="color:White;">&nbsp<b><?php echo "RM".number_format($pastMonths[$i-1],2); ?></b></label>
                  </span>
              <?php
                }
              ?>
              <div class="row">
                <div class="col-5">
                  <select name="reportMonth" class="form-control select2" data-placeholder="Choose one" required id="reportMonth" style="padding-right:0%">
                  <option value="" selected disabled hidden>Month</option>
                      <?php
                        for($i=1;$i <= 12;$i++){
                      ?>
                        <option value="<?php echo $i; ?>">
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
                <div class="col-5">
                  <input type="number" class="form-control select2 reportYear" name="reportYear" id="reportYear" placeholder="Year(e.g. <?php echo $currentYear;?>)" style="color:black;">
                </div>
                <div class="col-2">
                  <button type="button" class="btn btn-success" id="btnReport" name ="btnReport" onclick="generateReport()"><i class="fa fa-circle-o" style="font-size:0.875rem;"></i></button>
                </div>
              </div>
            </div><!-- card -->
          </div>

          <div class="col-8">
            <div class="card pd-0 bd-0 shadow-base" style="background-color:#1D2939;color:white;">
              <div class="pd-x-30 pd-t-30 pd-b-15">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h6 class="tx-20 tx-uppercase tx-inverse tx-semibold tx-spacing-1" style="color:white;">SALES PERFORMANCE</h6>
                    <p class="mg-b-0"></p>
                  </div>
                  <div class="tx-13">
                    <p class="mg-b-0"><span class="square-8 rounded-circle mg-r-10" style="background-color:#d88df7"></span>Orders</p>
                    <p class="mg-b-0"><span class="square-8 rounded-circle mg-r-10" style="background-color:#7ab4fb"></span>Sales (thousand)</p>
                  </div>
                </div><!-- d-flex -->
              </div>
              <!-- <div class="pd-x-15 pd-b-15">
                <div id="ch1" class="br-chartist br-chartist-2 ht-200 ht-sm-300"></div>
              </div> -->
              <div class="pd-x-30 pd-t-30 pd-b-15" id="salesPerformanceChart">
                <div class="ct-chart sales-performance ht-200 ht-sm-300"></div>
              </div>
            </div><!-- card -->

            <div class="card bd-0 shadow-base pd-30 mg-t-20">
              <div class="d-flex align-items-center justify-content-between mg-b-30">
                <div>
                  <h6 class="tx-20 tx-uppercase tx-inverse tx-semibold tx-spacing-1">ALL-TIME TOP CUSTOMERS</h6>
                  <!-- <p class="mg-b-0"><i class="icon ion-calendar mg-r-5"></i> From October 2017 - December 2017</p> -->
                </div>
                <!-- <a href="" class="btn btn-outline-info btn-oblong tx-11 tx-uppercase tx-mont tx-medium tx-spacing-1 pd-x-30 bd-2">See more</a> -->
              </div><!-- d-flex -->

              <table class="table table-valign-middle mg-b-0">
                <tbody>
                  <?php
                    $customers = run_query("select * from customers order by id desc");
                    $customerRank = [];
                    foreach($customers as $customer){
                      // debug_to_console($customer['name']);
                      $orders = run_query("select * from ec_orders where customer_id = ".$customer['id']." and status = 1");
                      $totalSpending = 0;
                      if(is_array($orders)){ //prevent warning
                        foreach($orders as $order){
                          $totalSpending += nf_view_currency($order['total']) - nf_view_currency($order['delivery_fee']);
                        }
                        $customerRank[$customer['name']] = $totalSpending;
                      }
                    }
                    
                    for($i = 0; $i < 5; $i++){
                      $maxs = array_search(max($customerRank), $customerRank);
                      // debug_to_console($maxs);
                      // debug_to_console($customerRank[$maxs]);
                    ?>
                    <tr>
                      <td class="pd-l-0-force" style="color:white;font-weight:bold;text-align:center;background-color:#1d2939;border-radius:50%;border:5px solid white;font-size:17px;">
                        <!-- <img src="img/img10.jpg" class="wd-40 rounded-circle" alt=""> -->
                        <?php echo $i+1;?>
                      </td>
                      <td>
                        <h6 class="tx-inverse tx-14 mg-b-0"><?php echo $maxs;?></h6>
                        <!-- <span class="tx-12">@deborah.miner</span> -->
                      </td>
                      <td>RM<?php echo number_format($customerRank[$maxs],2);?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <!-- <td><span id="sparkline1">1,4,4,7,5,9,4,7,5,9,1</span></td> -->
                      <!-- <td class="pd-r-0-force tx-center"><a href="" class="tx-gray-600"><i class="icon ion-more tx-18 lh-0"></i></a></td> -->
                    </tr>
                    
                    <?php
                      unset($customerRank[$maxs]);
                    }
                  ?>

                  <!-- <tr>
                    <td class="pd-l-0-force">
                      <img src="img/img9.jpg" class="wd-40 rounded-circle" alt="">
                    </td>
                    <td>
                      <h6 class="tx-inverse tx-14 mg-b-0">Belinda Connor</h6>
                      <span class="tx-12">@belinda.connor</span>
                    </td>
                    <td>Oct 28, 2017</td>
                    <td><span id="sparkline2">1,3,6,4,5,8,4,2,4,5,0</span></td>
                    <td class="pd-r-0-force tx-center"><a href="" class="tx-gray-600"><i class="icon ion-more tx-18 lh-0"></i></a></td>
                  </tr>
                  <tr>
                    <td class="pd-l-0-force">
                      <img src="img/img6.jpg" class="wd-40 rounded-circle" alt="">
                    </td>
                    <td>
                      <h6 class="tx-inverse tx-14 mg-b-0">Andrew Wiggins</h6>
                      <span class="tx-12">@andrew.wiggins</span>
                    </td>
                    <td>Oct 27, 2017</td>
                    <td><span id="sparkline3">1,2,4,2,3,6,4,2,4,3,0</span></td>
                    <td class="pd-r-0-force tx-center"><a href="" class="tx-gray-600"><i class="icon ion-more tx-18 lh-0"></i></a></td>
                  </tr>
                  <tr>
                    <td class="pd-l-0-force">
                      <img src="img/img5.jpg" class="wd-40 rounded-circle" alt="">
                    </td>
                    <td>
                      <h6 class="tx-inverse tx-14 mg-b-0">Brandon Lawrence</h6>
                      <span class="tx-12">@brandon.lawrence</span>
                    </td>
                    <td>Oct 27, 2017</td>
                    <td><span id="sparkline4">1,4,4,7,5,9,4,7,5,9,1</span></td>
                    <td class="pd-r-0-force tx-center"><a href="" class="tx-gray-600"><i class="icon ion-more tx-18 lh-0"></i></a></td>
                  </tr>
                  <tr>
                    <td class="pd-l-0-force">
                      <img src="img/img4.jpg" class="wd-40 rounded-circle" alt="">
                    </td>
                    <td>
                      <h6 class="tx-inverse tx-14 mg-b-0">Marilyn Tarter</h6>
                      <span class="tx-12">@marilyn.tarter</span>
                    </td>
                    <td>Oct 27, 2017</td>
                    <td><span id="sparkline5">1,3,6,4,5,8,4,2,4,5,0</span></td>
                    <td class="pd-r-0-force tx-center"><a href="" class="tx-gray-600"><i class="icon ion-more tx-18 lh-0"></i></a></td>
                  </tr> -->
                </tbody>
              </table>
            </div><!-- card -->

            <div class="card shadow-base card-body pd-25 bd-0 mg-t-20">
              <div class="row">
                <div class="col-sm-6">
                  <h6 class="card-title tx-uppercase tx-12">Statistics Summary</h6>
                  <p class="display-4 tx-medium tx-inverse mg-b-5 tx-lato">25%</p>
                  <div class="progress mg-b-10">
                    <div class="progress-bar bg-primary progress-bar-xs wd-30p" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                  </div><!-- progress -->
                  <p class="tx-12">Nulla consequat massa quis enim. Donec pede justo, fringilla vel...</p>
                  <p class="tx-11 lh-3 mg-b-0">You can also use other progress variant found in <a href="progress.html" target="blank">progress section</a>.</p>
                </div><!-- col-6 -->
                <div class="col-sm-6 mg-t-20 mg-sm-t-0 d-flex align-items-center justify-content-center">
                  <span class="peity-donut" data-peity='{ "fill": ["#0866C6", "#E9ECEF"],  "innerRadius": 60, "radius": 90 }'>30/100</span>
                </div><!-- col-6 -->
              </div><!-- row -->
            </div><!-- card -->


          </div><!-- col-9 -->
          <div class="col-4">


            <!-- <div class="card bd-0 shadow-base pd-30">
              <h6 class="tx-13 tx-uppercase tx-inverse tx-semibold tx-spacing-1">Server Status</h6>
              <p class="mg-b-25">Summary of the status of your server.</p>

              <label class="tx-12 tx-gray-600 mg-b-10">CPU Usage (40.05 - 32 cpus)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar wd-25p" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <label class="tx-12 tx-gray-600 mg-b-10">Memory Usage (32.2%)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar bg-teal wd-60p" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <label class="tx-12 tx-gray-600 mg-b-10">Disk Usage (82.2%)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar bg-danger wd-70p" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <label class="tx-12 tx-gray-600 mg-b-10">Databases (63/100)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar bg-warning wd-50p" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <label class="tx-12 tx-gray-600 mg-b-10">Domains (30/50)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar bg-info wd-45p" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <label class="tx-12 tx-gray-600 mg-b-10">Email Account (13/50)</label>
              <div class="progress ht-5 mg-b-10">
                <div class="progress-bar bg-purple wd-65p" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <div class="mg-t-20 tx-13">
                <a href="" class="tx-gray-600 hover-info">Generate Report</a>
                <a href="" class="tx-gray-600 hover-info bd-l mg-l-10 pd-l-10">Print Report</a>
              </div>
            </div> -->
            <!-- card -->

            <?php
              $d1 = new DateTime("2020-12-01 00:00:00");
              $d2 = new DateTime($current_datetime);
              $interval = $d2->diff($d1);
              $interval = $interval->m + $interval->y * 12;
              // debug_to_console($interval->m + $interval->y * 12);
            ?>
            <!-- <div class="card bg-transparent shadow-base bd-0 mg-t-20"> -->
            <div class="card bg-transparent shadow-base bd-0">
              <div class="bg-primary rounded-top">
                <div class="pd-x-30 pd-t-30">
                  <h6 class="tx-30 tx-uppercase tx-white tx-semibold tx-spacing-1">Sale Status</h6>
                  <p class="mg-b-20 tx-white-6">As of <?php echo (new DateTime($current_datetime))->format("d")." ".$currentDate->format("F")." ".$currentDate->format("Y");?></p>
                  <!-- <h3 class="tx-lato tx-white mg-b-0">$12, 201 <i class="icon ion-android-arrow-up tx-white-5"></i></h3> -->
                </div>
                <!-- <div id="chartLine1" class="wd-100p ht-150"></div> -->
              </div>
              <div class="bg-white pd-20 rounded-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-start">
                  <div><span id="sparkline6">5,4,7,5,9,7,4</span></div>
                  <div class="mg-l-15">
                    <label class="tx-uppercase tx-10 tx-medium tx-spacing-1 mg-b-0">Average Sales (<?php echo $interval?> months)</label>
                    <h6 class="tx-inverse mg-b-0 tx-lato tx-bold"><?php echo "RM".number_format($grandTotalSales/$interval,2); ?></h6>
                  </div>
                </div><!-- d-flex -->
                <div class="d-flex align-items-center">
                  <div><span id="sparkline7">4,7,5,9,4,7,5</span></div>
                  <div class="mg-l-15">
                    <label class="tx-uppercase tx-10 tx-medium tx-spacing-1 mg-b-0">Total Sales</label>
                    <h6 class="tx-inverse mg-b-0 tx-lato tx-bold"><?php echo "RM".number_format($grandTotalSales,2); ?></h6>
                  </div>
                </div><!-- d-flex -->
              </div><!-- d-flex -->
            </div><!-- card -->

            <div class="card bd-0 mg-t-20">
              <div id="carousel2" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carousel2" data-slide-to="0" class="active"></li>
                  <li data-target="#carousel2" data-slide-to="1"></li>
                  <li data-target="#carousel2" data-slide-to="2"></li>
                  <!-- <li data-target="#carousel2" data-slide-to="3"></li> -->
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="carousel-item active">
                    <div class="bg-br-primary pd-30 ht-300 pos-relative d-flex align-items-center rounded">
                      <div class="pos-absolute t-15 r-25">
                        <!-- <a href="" class="tx-white-5 hover-info"><i class="icon ion-edit tx-16"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-stats-bars tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-gear-a tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-more tx-20"></i></a> -->
                      </div>
                      <div class="tx-white">
                        <p class="tx-uppercase tx-11 tx-medium tx-mont tx-spacing-2 tx-white-5">Recent Article</p>
                        <h5 class="lh-5 mg-b-20">20 Best Travel Tips After 5 Years Of Traveling The World</h5>
                        <nav class="nav flex-row tx-13">
                          <a href="" class="tx-white-8 hover-white pd-l-0 pd-r-5">12K+ Views</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">234 Shares</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">43 Comments</a>
                        </nav>
                      </div>
                    </div><!-- d-flex -->
                  </div>
                  <div class="carousel-item">
                    <div class="bg-info pd-30 ht-300 pos-relative d-flex align-items-center rounded">
                      <div class="pos-absolute t-15 r-25">
                        <!-- <a href="" class="tx-white-5 hover-info"><i class="icon ion-edit tx-16"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-stats-bars tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-gear-a tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-more tx-20"></i></a> -->
                      </div>
                      <div class="tx-white">
                        <p class="tx-uppercase tx-11 tx-medium tx-mont tx-spacing-2 tx-white-5">Recent Article</p>
                        <h5 class="lh-5 mg-b-20">How I Flew Around the World in Business Class for $1,340</h5>
                        <nav class="nav flex-row tx-13">
                          <a href="" class="tx-white-8 hover-white pd-l-0 pd-r-5">Edit</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">Unpublish</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">Delete</a>
                        </nav>
                      </div>
                    </div><!-- d-flex -->
                  </div>
                  <div class="carousel-item">
                    <div class="bg-purple pd-30 ht-300 d-flex pos-relative align-items-center rounded">
                      <div class="pos-absolute t-15 r-25">
                        <!-- <a href="" class="tx-white-5 hover-info"><i class="icon ion-edit tx-16"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-stats-bars tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-gear-a tx-20"></i></a>
                        <a href="" class="tx-white-5 hover-info mg-l-7"><i class="icon ion-more tx-20"></i></a> -->
                      </div>
                      <div class="tx-white">
                        <p class="tx-uppercase tx-11 tx-medium tx-mont tx-spacing-2 tx-white-5">Recent Article</p>
                        <h5 class="lh-5 mg-b-20">10 Reasons Why Travel Makes You a Happier Person</h5>
                        <nav class="nav flex-row tx-13">
                          <a href="" class="tx-white-8 hover-white pd-l-0 pd-r-5">Edit</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">Unpublish</a>
                          <a href="" class="tx-white-8 hover-white pd-x-5">Delete</a>
                        </nav>
                      </div>
                    </div><!-- d-flex -->
                  </div>
                </div><!-- carousel-inner -->
              </div><!-- carousel -->
            </div><!-- card -->

          </div><!-- col-3 -->
        </div><!-- row -->

      </div><!-- br-pagebody -->


<script>
  function generateReport(){
    var reportMonth = document.getElementById("reportMonth").value;
    var reportYear = document.getElementById("reportYear").value;
    console.log(reportMonth);
    console.log(reportYear);
    if(!reportMonth || !reportYear){
      var d = new Date();
      var n = d.getFullYear();
      alert("Enter Month (Jan-Dec) and Year(e.g. "+n+").");
    }
    else{
      $.ajax({
        type:"POST",
        url:"ajax.php",
        data:{
          "generateReport":1,
          "reportMonth":reportMonth,
          "reportYear":reportYear
        },
        success:function(result){
          // window.location.href="order_edit.php?id="+orderId;
          var data = new Blob([result], {type: 'text/plain'});
          textFile = window.URL.createObjectURL(data);
          var a = document.createElement("a");
          a.href = textFile;
          a.download = "Month-Report.txt";
          document.body.appendChild(a);
          a.click();
          setTimeout(function() {
              document.body.removeChild(a);
              window.URL.revokeObjectURL(textFile);  
          }, 0); 
          // console.log(textFile);
          
        }
      });
    }
  }


  //Charts

  //Sales performance graph
  $.ajax({
    type:"POST",
    url:"ajax.php",
    data:{
      "pastMonthsData":1,
    },
    success:function(result){
      // console.log(JSON.parse(result)['rev']);
      // console.log(JSON.parse(result)['numOrders']);

      var data = {
        // A labels array that can contain any sort of values
        labels: JSON.parse(result)['pastMonths'],
        // Our series array that contains series objects or in this case series data arrays
        series: [
          JSON.parse(result)['rev'],
          JSON.parse(result)['numOrders']
        ]
      };

      // Create a new line chart object where as first parameter we pass in a selector
      // that is resolving to our chart container element. The Second parameter
      // is the actual data object.
      new Chartist.Line('.ct-chart.sales-performance', data);
        }
      });

</script>
      
<?php require_once 'php/req/footer.php'; ?>