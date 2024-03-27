<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Railink</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/pages/css/login-4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/pages/css/login-4-custom.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico" />
    <base href="<?php echo site_url(); ?>">
</head>

<body class="login">
        <div class="bg">
          <!-- BEGIN LOGO -->
          <div class="logo">
              <a href="index.html">
                  <img src="<?php echo base_url() ;?>assets/img/logo.png" alt="" /> </a>
          </div>
          <!-- END LOGO -->
          <!-- BEGIN LOGIN -->
          <div class="content">
              <!-- BEGIN LOGIN FORM -->
              <form class="login-form" id="form-login" action="<?php echo site_url('login/do_login'); ?>" method="post">
                  <h2 class="form-title text-center">AFC</h2>
                  <h4 class="text-center">Automatic Fare Collection</h4>
                  <div class="alert alert-danger display-hide" id="alert-message">
                      <button class="close" data-close="alert"></button>
                      <span> Enter any username and password. </span>
                  </div>
                  <div class="form-group">
                      <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                      <label class="control-label visible-ie8 visible-ie9">Username</label>
                      <div class="input-icon">
                          <i class="fa fa-user"></i>
                          <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" /> </div>
                  </div>
                  <div class="form-group">
                      <label class="control-label visible-ie8 visible-ie9">Password</label>
                      <div class="input-icon">
                          <i class="fa fa-lock"></i>
                          <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" /> </div>
                  </div>
                  <div class="form-actions">
                      <button style="box-shadow:0 3px 3px #000 !important;" type="submit" class="btn blue-madison col-md-offset-2 col-md-8" id="btn-login" data-loading-text="Loading <i class='fa fa-circle-o-notch fa-spin'></i>"> Login </button>
                  </div>
              </form>
              <!-- END LOGIN FORM -->
          </div>
          <!-- END LOGIN -->
        </div>



    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/pages/scripts/login-4.js" type="text/javascript"></script>
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
                        }
                    },

                    messages: {
                        username: {
                            required: "Username is required."
                        },
                        password: {
                            required: "Password is required."
                        }
                    },

                    invalidHandler: function(event, validator) { //display error alert on form submit
                        $("#alert-message span").text("Enter any username and password.");
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

                        var btn  = $("#btn-login");
                        var form = $("#form-login").attr('action');

                        $.ajax({
                            url     : form,
                            type    : 'POST',
                            data    : $("#form-login").serialize(),
                            cache   : false,
                            beforeSend : function () {
                                btn.button('loading');
                            },
                            success : function(data) {
                                setTimeout(function(){
                                    btn.button('reset');

                                    var obj = jQuery.parseJSON(data);

                                    if (obj.success) {
                                       window.location.replace("<?php echo site_url('dashboard'); ?>");
                                    } else {
                                        // alert(obj.error);
                                        $('.alert-danger', $('.login-form')).show();
                                        $('#alert-message span').text(obj.error);
                                    }
                                }, 500);
                            }
                        })
                        return false;
                    }
                });

                $('.login-form input').keypress(function(e) {
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

                $('#forget-password').click(function(){
                    $('.login-form').hide();
                    $('.forget-form').show();
                });

                $('#back-btn').click(function(){
                    $('.login-form').show();
                    $('.forget-form').hide();
                });
            }




            return {
                //main function to initiate the module
                init: function() {

                    handleLogin();

                    // var siteUrl = $("base").attr("href");

                    // init background slide images
                    // $('.login-bg').backstretch([
                    //
                    //     siteUrl + "assets/img/slide-login.jpg"
                    //     ], {
                    //       fade: 1000,
                    //       duration: 8000
                    //     }
                    // );
                    //
                    // $('.forget-form').hide();

                }

            };

        }();

        jQuery(document).ready(function() {
            Login.init();
        });
    </script>
</body>

</html>
