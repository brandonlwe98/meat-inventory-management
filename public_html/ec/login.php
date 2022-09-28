<?php require_once 'php/func/func.php'; ?>
<?php require_once 'php/func/debug.php'; ?> <!-- Console log function on Chrome with extension - Xdebug helper -->
<?php
$errorLogin = false;
if ($_POST){
    $user = run_query("select * from users where username = '".$_POST['username']."'");
    if ($user){
        $user = $user[0];
        debug_to_console("verifying pwd");
        if ($_POST['password'] == $user['password']){
            debug_to_console("password verified");
            $_SESSION['user_id'] = $user['id'];
            setcookie("currentMenuItem", "dashboard", time()+30*24*60*60, '/');
            setcookie("currentProductCat",0, time()+30*24*60*60, '/');

            $currentDate = new DateTime($current_datetime);
            $currentMonthNo = $currentDate->format("m");
            $currentYear = $currentDate->format("Y");
            setcookie("archiveMonth",$currentMonthNo, time()+30*24*60*60, '/');
            setcookie("archiveYear",$currentYear, time()+30*24*60*60, '/');
            header('Location: /meat_delivery/public_html/ec');
        }
        else{
            $errorLogin = true;
            debug_to_console("password failed to be verified");
        }
    }
    $errorLogin = true;
}
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

    <!-- Bracket CSS -->
    <link rel="stylesheet" href="lib/css/bracket.css">
  </head>

  <style>
  .input-group-addon{
    background-color: transparent;
    border: 1px solid rgb(0 0 0 / 0.15);
    border-radius: 0px 3px 3px 0px;
    border-left: none;
  }
  .input-group-addon:hover{
    cursor:pointer;
    background-color:#17a2b8;
    color:white;
  }
  </style>

  <body>

    <div class="d-flex align-items-center justify-content-center bg-br-primary ht-100v">

      <div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white rounded shadow-base">
        <div class="signin-logo tx-center tx-22 tx-bold tx-inverse"><span class="tx-normal">[</span> Meat & Seafood System <span class="tx-normal">]</span></div>
        <div class="tx-center mg-b-30">by Brandon the Great</div>
        <form action="login.php" method="post">
            <div class="form-group">
              <input type="text" name="username" class="form-control" placeholder="Username">
            </div><!-- form-group -->
            <div class="form-group">
              <small style="display:none;color:#17a2b8;" id="caps_warning">CAPS LOCK IS ACTIVATED</small>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" onfocus="highlightIcon()" onblur="removeHighlight()">
                <div class="input-group-addon" id="showPwd" style="border-right:1px solid rgb(0 0 0 /0.15);border-radius: 0px 3px 3px 0px;" onclick="showPassword()">
                  <i class="fa fa-eye"></i>
                </div>
                <div class="input-group-addon" id="hidePwd" style="display:none" onclick="hidePassword()">
                  <i class="fa fa-eye-slash"></i>
                </div>
              </div>
            </div><!-- form-group -->
            <?php
              if ($errorLogin){
            ?>
              <div class="form-group" style="margin-bottom:2%;">
                <span class="tx-danger" style="padding:1% 2%;"> Incorrect username or password.</span>
              </div>
            <?php
              }
            ?>
            <button type="submit" class="btn btn-info btn-block">Sign In</button>
        </form>
      </div><!-- login-wrapper -->
    </div><!-- d-flex -->

    <script src="lib/jquery/jquery.js"></script>
    <script src="lib/popper.js/popper.js"></script>
    <script src="lib/bootstrap/bootstrap.js"></script>
    <script>
      var password = document.getElementById('password');
      var capsWarning = document.getElementById('caps_warning');
      password.addEventListener("keyup",function(event){
        if (event.getModifierState("CapsLock")){
          capsWarning.style.display = "inline";
        }
        else{
          capsWarning.style.display = "none";
        }
      })
    </script>
    <script>
      var password = document.getElementById('password');
      var showPwd = document.getElementById('showPwd');
      var hidePwd = document.getElementById('hidePwd');
      function showPassword(){
        if (password.type="password"){
          password.type="text";
          showPwd.style.display="none";
          hidePwd.style.display="inline";
        }
      }
      function hidePassword(){
        if (password.type="text"){
          password.type="password";
          showPwd.style.display="inline";
          hidePwd.style.display="none";
        }
      }
      function highlightIcon(){
        showPwd.style.borderColor="#17a2b8";
        hidePwd.style.borderColor="#17a2b8";
      }

      function removeHighlight(){
        showPwd.style.borderColor="rgb(0 0 0 / 0.15)";
        hidePwd.style.borderColor="rgb(0 0 0 / 0.15)";
      }
    </script>
  </body>
</html>
