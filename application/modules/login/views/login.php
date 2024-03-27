
<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
    <meta charset="utf-8">
    <title>Login (<?php echo idtyApp() ?>) - Port Ticketing System</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ASDP Ticketing System">
    <meta name="author" content="Nutech Integrasi">
    <!-- <link rel="icon" href="../../favicon.ico"> -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/img/favicon.png'); ?>">
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" /> -->

        <link href="<?php base_url()?>assets/pages/css/googletapis.css" rel="stylesheet" type="text/css" />
        <link href="<?php base_url()?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php base_url()?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php base_url()?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php base_url()?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?php base_url()?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php base_url()?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?php base_url()?>assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php base_url()?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?php base_url()?>assets/pages/css/login-4.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
    <!-- END HEAD -->
    <style>
      @media (max-width: 480px) {
        .login .content {
            width: 298px !important;
        }

      }
    </style>

    <body class=" login layer" style=" background-image: url('assets/img/logo_4.jpg'); background-size:cover; background-attachment: fixed;">
        <!-- BEGIN LOGO -->
        <p></p>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="container" style="background-color:white;  box-shadow: 3px 5px 20px 2px;  padding:100px 0px; background-color: blue;  background: rgba(255, 255,255, 0.3);" >

          <div class="content" style="background-color: white; box-shadow: 3px 5px 20px 2px; "> 
            <!-- BEGIN LOGIN FORM -->
            <form class="login-form box" id="form-login" action="<?php echo site_url('login/do_login'); ?>" method="post">
              <!-- <img src="<?php base_url()?>assets/img/asdp-logo2.jpg" width="100%"  /> -->
              <!-- CR logo -->
                <center>
                <img src="<?php base_url()?>assets/img/asdp-logo2.jpg" width="75%" height="auto" />
              </center>
                <p></p>
              <div class="alert alert-danger display-hide" id="alert-message">
                <button class="close" data-close="alert"></button>
                <span></span>
              </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Username</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/> </div>

                        <input type="hidden" id='tokenHash' class="tokenHash"  value="<?php echo $this->security->get_csrf_hash(); ?>" name="<?php echo $this->security->get_csrf_token_name(); ?>" >
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" /> </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                    <label class="control-label visible-ie8 visible-ie9">Captcha</label>
                    <div class="input-icon ">
                        
                        <i class="fa fa-lock"></i>
                        <input class=" form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Captcha" name="ratcha" />    
                    </div>
                    </div>
                    <div class="col-md-6">
                      <img src="<?php echo base_url(); ?>login/capcay" id="ratcha_image" alt="CAPTCHA" width='100%' height='35px'>
                    </div>
                  </div>
                </div>

                <div class="form-actions">
<!--                     <label class="rememberme mt-checkbox mt-checkbox-outline">
                        <input type="checkbox" name="remember" value="1" /> Remember me
                        <span></span>
                    </label> -->
                    <button class="btn btn-primary pull-right" type="submit" id="btn-login" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Checking">Sign In</button>
                </div>
                <p>
            </form>

          </div>
            <!-- END LOGIN FORM -->
            <!-- BEGIN FORGOT PASSWORD FORM -->
        </div>
        <!-- END LOGIN -->
        <!-- BEGIN COPYRIGHT -->
        <!-- <div class="copyright"> 2014 &copy; Metronic - Admin Dashboard Template. </div> -->
        <!-- END COPYRIGHT -->
        <!-- [if lt IE 9]> -->
<script src="<?php base_url()?>assets/global/plugins/respond.min.js"></script>
<script src="<?php base_url()?>assets/global/plugins/excanvas.min.js"></script> 
<![endif]
        <!-- BEGIN CORE PLUGINS -->
        <script src="<?php base_url()?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?php base_url()?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="<?php base_url()?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?php base_url()?>assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="<?php base_url()?>assets/pages/scripts/login-4.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
            <script type="text/javascript">
      var Login = function() {

        var handleLogin = function() {

          $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
              username: {
                required: true
              },
              password: {
                required: true
              },
              ratcha: {
                required: true
              }
            },
            messages: {
              username: {
                required: "Username is required."
              },
              password: {
                required: "Password is required."
              },
              ratcha: {
                required: "Captcha is required."
              }
            },
            invalidHandler: function(event, validator) { //display error alert on form submit
              $("#alert-message span").text("Masukan username, password dan captcha.");
              $('.alert-danger', $('.login-form')).show();
            },
            highlight: function(element) { // hightlight error inputs
              $(element)
                      .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function(label) {
              label.closest('.form-group').removeClass('has-error');
              label.remove();
            },
            errorPlacement: function(error, element) {
              error.insertAfter(element.closest('.input-icon'));
            },
            submitHandler: function(form) {
              form.preventDefault;

              var btn = $("#btn-login");
              var form = $("#form-login").attr('action');

              $.ajax({
                url: form,
                type: 'POST',
                data: $("#form-login").serialize(),
                cache: false,
                beforeSend: function() {
                  btn.button('loading');
                },
                success: function(data) {
                  var obj = jQuery.parseJSON(data);

                  if (obj.success)
                  {
                    // window.location.replace("<?php echo site_url('home'); ?>");
                    window.location="<?php echo site_url('home'); ?>";
                  } else {
                    $('.alert-danger', $('.login-form')).show();
                    $('#alert-message span').text(obj.error);
                  }
                },
                complete: function() {
                  btn.button('reset');
                }
              })
              return false;
            }
          });

          $('.login-form input').keypress(function(e) {
            e.stopImmediatePropagation(); // agar tidak hit 2 x ketiika masuk dengan enter
            if (e.which == 13) {
              if ($('.login-form').validate().form()) {
                $('.login-form').submit(); //form validation success, call ajax form submit
              }
              return false;
            }
          });

          $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
              if ($('.forget-form').validate().form()) {
                $('.forget-form').submit();
              }
              return false;
            }
          });

          $('#forget-password').click(function() {
            $('.login-form').hide();
            $('.forget-form').show();
          });

          $('#back-btn').click(function() {
            $('.login-form').show();
            $('.forget-form').hide();
          });
        }


        return {
          //main function to initiate the module
          init: function() {

            handleLogin();

            var siteUrl = "<?php echo base_url(); ?>";

            console.log(siteUrl);

            // init background slide images
            $('.login-bg').backstretch([
              //siteUrl + "assets/img/asdp-logo.jpg",
              siteUrl + "assets/img/organization.jpg",
              siteUrl + "assets/img/organization2.jpg",
              //siteUrl + "assets/img/organization3.jpg",
              //siteUrl + "assets/img/seat.jpg",
            ], {
              fade: 1000,
              duration: 5000
            }
            );

            $('.forget-form').hide();

          }

        };

      }();

      jQuery(document).ready(function() {
        Login.init();
      });
    </script>


    </body>

</html>
