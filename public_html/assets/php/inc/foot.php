<?php
if ($_POST){
    $error_name = 0;
    $error_email = 0;
    $error_phone = 0;
    $error_company = 0;
    $error_message = 0;
    if (isset($_POST['name']) && $_POST['name'] != ''){ } else { $error_name = 1; }
    if (isset($_POST['email']) && $_POST['email'] != ''){ } else { $error_email = 1; }
    if (isset($_POST['phone']) && $_POST['phone'] != ''){ } else { $error_phone = 1; }
    if (isset($_POST['message']) && $_POST['message'] != ''){ } else { $error_message = 1; }
    if (!$error_name && !$error_email && !$error_phone && !$error_company && !$error_message){
        run_query("alter table contact_form auto_increment = 1");
        $insert = run_query("insert into contact_form (name, email, phone, company, message, created_datetime) values ('".$_POST['name']."', '".$_POST['email']."', '".$_POST['phone']."', '".$_POST['company']."', '".$_POST['message']."', '".$current_datetime."')");
        if ($insert){
            ?>
                <script>alert('Thank you! Your message is successfully sent.');</script>
            <?php
        } else {
            ?>
                <script>alert('We\'re sorry, but something went wrong.');</script>
            <?php
        }
    } else {
        ?>
            <script>alert('Please fill in the required fields.');</script>
        <?php
    }
}
?>
                        <section class="contacts section" style="margin-top: 9em;">
                            <div class="container">
                                <header class="section-header">
                                    <h2 class="section-title">Get <span class="text-primary">in touch<br>联系我们</span></h2>
                                    <strong class="fade-title-right">Contacts</strong>
                                </header>
                                <div class="section-content">
                                    <div class="row-base row">
                                        <div class="col-address col-base col-md-4">
                                            +603-8082 3158<br>
                                            +603-8082 3159<br>
                                            +603-8080 3159<br><br>
                                            SS-02-13 & 13A, Sky-Pod Square<br>
                                            Puchong Jaya South, 47100 Puchong<br>
                                            Selangor, Malaysia</div>
                                        <div class="col-base  col-md-8">
                                            <form id="contact_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                                <div class="row-field row">
                                                    <div class="col-field col-sm-6 col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" id="contact_form_name" class="form-control" name="name" required placeholder="Name 姓名*">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" id="contact_form_email" class="form-control" name="email" required placeholder="Email address 电邮*">
                                                        </div>
                                                    </div>
                                                    <div class="col-field col-sm-6 col-md-4">
                                                        <div class="form-group">
                                                            <input type="tel" id="contact_form_phone" class="form-control" name="phone" required placeholder="Phone 联络号码*">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" id="contact_form_company" class="form-control" name="company" placeholder=" Company 公司名字">
                                                        </div>
                                                    </div>
                                                    <style>
                                                        textarea.error { border-color: #c5a47e; }
                                                    </style>
                                                    <div class="col-field col-sm-12 col-md-4">
                                                        <div class="form-group">
                                                            <textarea id="contact_form_message" name="message" required placeholder=" Message 留言信息*"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-message col-field col-sm-12" style="display: block;">
                                                        <div class="form-group">
                                                            <div id="contact_form_success_message" style="display: none;"><i class="fa fa-check text-primary"></i> Thank you! Your message is successfully sent.</div>
                                                            <div id="contact_form_error_message" style="display: none;">We&#039;re sorry, but something went wrong.</div>
                                                            <div id="contact_form_required_message" style="display: none;">Please fill in the required fields.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-submit text-right">
                                                    <button type="submit" id="contact_form_submit_button" class="btn btn-shadow-2 wow swing">Send 发送<i class="icon-next"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <footer id="footer" class="footer">
            <div class="container">
                <div class="row-base row">
                    <!--<div class="col-base text-left-md col-md-4">
                        <a href="http://paul-themes.com/wp/go-arch/go-arch-dark" class="brand">go<span class="text-primary">.</span>arch </a>
                    </div>
                    <div class="text-center-md col-base col-md-4">
                        <a class="author-link" href="http://themeforest.net/user/murren20" target="_blank">Murren20</a>
                    </div>
                    <div class="text-right-md col-base col-md-4">
                        © Richwood Ventures Berhad 2019. All Rights Reserved.
                    </div>-->
                    <div class="col-base text-center-md col-md-12">
                        <a href="/" class="brand">
                            <img src="assets/img/logo_full.png" alt="">
                        </a>
                        <div style="color: rgba(255,255,255,.4); margin-top: 10px;">© Richwood Ventures Berhad 2019. All Rights Reserved.</div>
                    </div>
                </div>
            </div>
        </footer>
        <div class="page-lines">
            <div class="container">
                <div class="col-line col-xs-4">
                    <div class="line"></div>
                </div>
                <div class="col-line col-xs-4">
                    <div class="line"></div>
                </div>
                <div class="col-line col-xs-4">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script data-cfasync="false" src="assets/js/email-decode.min.js?<?php echo time(); ?>"></script><script type="text/javascript">
    function revslider_showDoubleJqueryError(sliderID) {
        var errorMessage = "Revolution Slider Error: You have some jquery.js library include that comes after the revolution files js include.";
        errorMessage += "<br> This includes make eliminates the revolution slider libraries, and make it not work.";
        errorMessage += "<br><br> To fix it you can:<br>&nbsp;&nbsp;&nbsp; 1. In the Slider Settings -> Troubleshooting set option:  <strong><b>Put JS Includes To Body</b></strong> option to true.";
        errorMessage += "<br>&nbsp;&nbsp;&nbsp; 2. Find the double jquery.js include and remove it.";
        errorMessage = "<span style='font-size:16px;color:#BC0C06;'>" + errorMessage + "</span>";
        jQuery(sliderID).show().html(errorMessage);
    }
</script>
<script type='text/javascript' src='assets/js/bootstrap.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/jquery.validate.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/wow.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/jquery.stellar.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/jquery.magnific-popup.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/owl.carousel.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript'>
    /* <![CDATA[ */
    var goarch_obj = {"ajaxurl":"http:\/\/paul-themes.com\/wp\/go-arch\/go-arch-dark\/wp-admin\/admin-ajax.php","theme_url":"http:\/\/paul-themes.com\/wp\/go-arch\/go-arch-dark\/wp-content\/themes\/goarch"};
    /* ]]> */
</script>
<script type='text/javascript' src='assets/js/interface.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='https://maps.google.com/maps/api/js?key=AIzaSyCwVuYiM-83l2IdjpT9uC0lg4jBm8-w4j8&#038;ver=1'></script>
<script type='text/javascript' src='assets/js/gmap.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/comment-reply.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/wp-embed.min.js?<?php echo time(); ?>'></script>
<script type='text/javascript' src='assets/js/js_composer_front.min.js?<?php echo time(); ?>'></script>
<!--<script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.min.js'></script>-->
<script>
    /*function submit_contact_form(){
        var form_data = {
            name: $('#contact_form_name').val(),
            email: $('#contact_form_email').val(),
            phone: $('#contact_form_phone').val(),
            company: $('#contact_form_company').val(),
            message: $('#contact_form_message').val()
        };
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'assets/ajax/submit_contact_form.php',
            data: form_data,
            success: function(data){
                $('#contact_form_success_message').hide();
                $('#contact_form_error_message').hide();
                $('#contact_form_required_message').hide();
                $('#contact_form_'+data+'_message').show();
                if (data == 'success'){
                    $('#contact_form_name').attr('disabled', true).css('background-color', '#222');
                    $('#contact_form_email').attr('disabled', true).css('background-color', '#222');
                    $('#contact_form_phone').attr('disabled', true).css('background-color', '#222');
                    $('#contact_form_company').attr('disabled', true).css('background-color', '#222');
                    $('#contact_form_message').attr('disabled', true).css('background-color', '#222').css('cursor', 'not-allowed');
                    $('#contact_form_submit_button').attr('disabled', true);
                }
            }
        });
    }*/
</script>
</body>
</html>