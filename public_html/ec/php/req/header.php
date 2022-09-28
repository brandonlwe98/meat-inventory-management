<?php require_once 'php/func/debug.php'; ?>
<?php
$currentDate = new DateTime($current_datetime);
$startingDay = $currentDate->format("Y-m-d\TH:i:s");
$upcomingDays = $currentDate->modify("+3 day");
$upcomingDays = $upcomingDays->format("Y-m-d H:i:s");
$upcomingDeliveries = run_query("select * from ec_orders where delivery_status=0 and delivery_date <= '".$upcomingDays."' order by delivery_date desc");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Twitter -->
    <meta name="twitter:site" content="@themepixels">
    <meta name="twitter:creator" content="@themepixels">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bracket">
    <meta name="twitter:description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="twitter:image" content="http://themepixels.me/bracket/img/bracket-social.png">

    <!-- Facebook -->
    <meta property="og:url" content="http://themepixels.me/bracket">
    <meta property="og:title" content="Bracket">
    <meta property="og:description" content="Premium Quality and Responsive UI for Dashboard.">

    <meta property="og:image" content="http://themepixels.me/bracket/img/bracket-social.png">
    <meta property="og:image:secure_url" content="http://themepixels.me/bracket/img/bracket-social.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="author" content="ThemePixels">

    <title>Meat and Seafood Inventory System</title>

    <!-- vendor css -->
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="lib/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="lib/jquery-switchbutton/jquery.switchButton.css" rel="stylesheet">
    <link href="lib/rickshaw/rickshaw.min.css" rel="stylesheet">
    <!-- <link href="lib/chartist/chartist.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

    <!-- Bracket CSS -->
    <link rel="stylesheet" href="lib/css/bracket.css">

    <!-- Pagination CSS -->
    <link rel="stylesheet" href="lib/css/pagination.css">

    <script>
    // Picture element HTML5 shiv
      document.createElement( "picture" );
    </script>
    <script src="scripts/picturefill.js" async></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

  </head>
<style>
.icon-area .notify{
  font-size: .6rem;
  position: absolute;
  top: 2px;
  right: 5px;
  width: 15px;
  height: 15px;
  color: #fff;
  background-color: #fd556d;
  border-radius: 50%;
}

li.lateDelivery i{
  color:#f65050 !important;
}
li.closeDelivery i{
  color:yellow !important;
}
</style>
  <body>

    <!-- ########## START: LEFT PANEL ########## -->
    <div class="br-logo"><a href=""><span>[</span>Inventory<span>]</span></a></div>
    <div class="br-sideleft overflow-y-auto">
      <label class="sidebar-label pd-x-15 mg-t-20">Navigation</label>
      <div class="br-sideleft-menu" id="sideMenu">
        <a href="index.php" class="br-menu-link" id="dashboard">
          <div class="br-menu-item">
            <i class="menu-item-icon icon ion-ios-home-outline tx-22"></i>
            <span class="menu-item-label">Dashboard</span>
          </div><!-- menu-item -->
        </a><!-- br-menu-link -->
        <a href="product.php" class="br-menu-link" id="product">
          <div class="br-menu-item">
            <i class="menu-item-icon icon ion-ios-gear-outline tx-24"></i>
            <span class="menu-item-label">Product</span>
          </div>
        </a>
        <a href="customer.php" class="br-menu-link" id="customer">
          <div class="br-menu-item">
            <i class="menu-item-icon icon ion-ios-person-outline tx-24"></i>
            <span class="menu-item-label">Customer</span>
          </div>
        </a>
        <a href="order.php" class="br-menu-link" id="order">
          <div class="br-menu-item">
            <i class="menu-item-icon icon ion-ios-paper-outline tx-24"></i>
            <span class="menu-item-label">Order</span>
          </div>
        </a>
        <a href="archive.php?month=<?php echo $_COOKIE['archiveMonth'];?>&year=<?php echo $_COOKIE['archiveYear'];?>" class="br-menu-link" id="archive">
          <div class="br-menu-item">
            <i class="menu-item-icon icon ion-ios-albums-outline tx-24"></i>
            <span class="menu-item-label">Archive</span>
          </div>
        </a>
      </div><!-- br-sideleft-menu -->
      <br>
    </div><!-- br-sideleft -->
    <!-- ########## END: LEFT PANEL ########## -->

    <!-- ########## START: HEAD PANEL ########## -->
    <div class="br-header">
      <div class="br-header-left">
        <div class="navicon-left hidden-md-down"><a id="btnLeftMenu" href=""><i class="icon ion-navicon-round"></i></a></div>
        <div class="navicon-left hidden-lg-up"><a id="btnLeftMenuMobile" href=""><i class="icon ion-navicon-round"></i></a></div>
      </div><!-- br-header-left -->
      <div class="br-header-right">
        <nav class="nav">
          
          <div class="dropdown">
            <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
              <span class="logged-name hidden-md-down"><?php echo $user_data['username']; ?></span><i class="icon ion-navicon-round"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-header wd-200">
              <ul class="list-unstyled user-profile-nav">
                <li><a href="logout.php"><i class="icon ion-power"></i> Log Out</a></li>
              </ul>
            </div><!-- dropdown-menu -->
          </div><!-- dropdown -->
          <div class="dropdown">
            <a href="" class="nav-link nav-link-profile icon-area" data-toggle="dropdown">
              <i class="fa fa-truck" style="font-size:20px;"></i>
              <span class="notify d-flex align-items-center justify-content-center" <?php if(!$upcomingDeliveries){echo "style='visibility:hidden'";}?>><?php echo sizeof($upcomingDeliveries);?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-header wd-200">
              <ul class="list-unstyled user-profile-nav">
              <li style="text-align:center;"><b>Upcoming Deliveries</b></li>
                <?php
                  if($upcomingDeliveries){
                    foreach($upcomingDeliveries as $order){
                      $date = new DateTime($order['delivery_date']);
                      $page="archive_view";
                      $deliveryStatus = false;
                      $nextDay = (new DateTime($startingDay))->modify("+1 day");
                      $nextDay = $nextDay->format("Y-m-d\TH:i:s");
                      if($order['status'] == 0){
                        $page = "order_edit";
                      }
                      if($order['delivery_date']<$startingDay){
                        $deliveryStatus = "lateDelivery";
                      }
                      else if($order['delivery_date'] < $nextDay){
                        $deliveryStatus = "closeDelivery";
                      }
                ?>
                <li style="text-align:right;" <?php if($deliveryStatus){echo "class=".$deliveryStatus;}?>><a href="<?php echo $page;?>.php?id=<?php echo $order['id'];?>"><i class="icon ion-ios-time-outline" style="color:green;"></i><?php echo $date->format("d-m-Y h:i A");?>
                <?php 
                  $customer=run_query("select * from customers where id = ".$order['customer_id'])[0]; 
                  echo $customer['name'];
                ?>
                </a>
                </li>
                <?php
                    }
                  }
                  if(!$upcomingDeliveries){
                ?>
                    <li style="text-align:center;">No Upcoming Deliveries...</li>
                <?php
                  }
                ?>
              </ul>
            </div><!-- dropdown-menu -->
              <!-- <i class="fa fa-truck" style="font-size:15px"></i> -->
          </div><!-- dropdown -->
        </nav>
      </div><!-- br-header-right -->
    </div><!-- br-header -->
    <!-- ########## END: HEAD PANEL ########## -->

    <script>
    // Add active class to the current button (highlight it)
    var sideMenu = document.getElementById("sideMenu");
    var menuItem = sideMenu.getElementsByClassName("br-menu-link");
    for (var i = 0; i < menuItem.length; i++) {
      if (getCookie("currentMenuItem") == menuItem[i].id){
        menuItem[i].className += " active";
        var icon = menuItem[i].querySelector('div').querySelector('i');
        var iconName = icon.className.split(" ");
        var str = iconName[2].replace("-outline","");
        iconName[2]=str;
        icon.className="";
        for(var j = 0; j < iconName.length; j++){
          icon.className += (iconName[j] + " ");
        }
      }
      menuItem[i].addEventListener("click", function() {
        //update cookie to display the active menu item
        setCookie("currentMenuItem", this.id, 30);
      });
    }

    //function to retrieve cookie
    function getCookie(cname) {
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(';');
      for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }
    // function to set cookie
    function setCookie(cname,cvalue,exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays*24*60*60*1000));
      var expires = "expires=" + d.toGMTString();
      document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    </script>