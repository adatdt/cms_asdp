<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent1 . '">' . $parent1; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent2 . '">' . $parent2; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>
        <br />
        <div class="portlet box blue-madison">
            <div class="portlet-title">
                <div class="caption">
                    <h4><?php echo $title; ?></h4>
                </div>
                <div class="tools">
                    <div class="pull-right">
                        <a href="<?php echo site_url('home') ?>"
                           class="btn btn-warning">Home</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form action="<?php echo site_url('configuration/users/update_profile'); ?>" method="post"
                      id="form-input" class="form-horizontal">
                    <input type="hidden" name="id" value="<?php echo $users->id; ?>">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Grup<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">

                                <select class="form-control" name="group" id="group">
                                    <option disabled>Pilih</option>
                                    <?php
                                    $id = $users->group_id;
                                    foreach ($groups as $group) {
                                        $selected = $id == $group->id ? 'selected' : '';
                                        echo '<option ' . $selected . ' value="' . $group->id . '">' . $group->group_name . '</option>';
                                    }
                                    ?>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group <?php echo ($users->group_id == '4') ? '' : 'hidden'; ?>" id="po_code_wrapper">
                            <label class="control-label col-md-3">Kode PO<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control" name="po_code" id="po_code" <?php echo ($users->group_id == '4') ? '' : 'disabled'; ?>> 
                                <option disabled selected="selected">Pilih</option> 
                                <?php 
                                    $code = $users->po_code;                         
                                    foreach ($po as $row) {
                                      echo "<option  value='$row->po_code'";
                                      if($code==$row->po_code){
                                        echo " selected";
                                      }
                                      echo ">".ucwords($row->po_code)." - ".ucwords($row->po_name)."</option>"; 
                                    }
                                ?>                               
                                </select> 
                            </div>
                        </div>
                        <div class="form-group <?php echo ($users->group_id == '9') ? '' : 'hidden'; ?>" id="terminal_code_wrapper">
                            <label class="control-label col-md-3">Kode Terminal<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control" name="terminal_code" id="terminal_code" <?php echo ($users->group_id == '9') ? '' : 'disabled'; ?>> 
                                <option disabled selected="selected">Pilih</option> 
                                <?php 
                                    $code = $users->terminal_code;                         
                                    foreach ($terminal as $row) {
                                      echo "<option  value='$row->terminal_code'";
                                      if($code==$row->terminal_code){
                                        echo " selected";
                                      }
                                      echo ">".ucwords($row->terminal_code)." - ".ucwords($row->terminal_name)."</option>"; 
                                    }
                                ?>                               
                                </select> 
                            </div>
                        </div>
                        <div class="form-group <?php echo ($users->group_id == '6') ? '' : 'hidden'; ?>" id="mid_wrapper">
                            <label class="control-label col-md-3">Mid<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control" name="mid" id="mid" <?php echo ($users->group_id == '6') ? '' : 'disabled'; ?> >
                                <option disabled  selected="selected">Pilih</option> 
                                <?php 
                                    $code = $users->mid;                         
                                    foreach ($mid as $row) {
                                      echo "<option  value='$row->mid'";
                                      if($code==$row->mid){
                                        echo " selected";
                                      }
                                      echo ">".ucwords($row->mid)." - ".ucwords($row->name)."</option>"; 
                                    }
                                ?>                               
                                </select> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Depan<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" value="<?php cetak($users->firstname); ?>" name="firstname" data-required="1" class="form-control"/> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Belakang<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="lastname"  value="<?php cetak($users->lastname); ?>" data-required="1" class="form-control"/> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Username<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="username"  value="<?php cetak($users->username); ?>" data-required="1" class="form-control" readonly="readonly"/> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Password<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="password" name="password" id="password" class="form-control"  data-required="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Ulangi Password<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="password" name="repeat_password" id="repeat_password" class="form-control"  data-required="1">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-warning">Update</button>
                                <button type="reset" class="btn default">Batal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var FormValidation = function () {

        var handleValidation = function () {

            var form = $('#form-input');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    group: {
                        required: true
                    },
                    mid: {
                        required: true
                    },
                    po_code: {
                        required: true
                    },
                    terminal_code: {
                        required: true
                    },
                    firstname: {
                        required: true
                    },
                    lastname: {
                        required: true
                    },
                    username: {
                        required: true,
                        minlength: 3
                    },
                    password: {
                        minlength: 5
                    },
                    repeat_password: {
                        minlength: 5,
                        equalTo: "#password"
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.parent(".input-group").size() > 0) {
                        error.insertAfter(element.parent(".input-group"));
                    } else if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else if (element.parents('.radio-list').size() > 0) {
                        error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                    } else if (element.parents('.radio-inline').size() > 0) {
                        error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                    } else if (element.parents('.checkbox-list').size() > 0) {
                        error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                    } else if (element.parents('.checkbox-inline').size() > 0) {
                        error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                            .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                

            });
        }

        return {
            //main function to initiate the module
            init: function () {

                handleValidation();

            }

        };

    }();

    jQuery(document).ready(function () {
        FormValidation.init();

        // Jika dipilih
        $('#reset').on('click', function () {
            $('.form-group').removeClass('has-error');
             $('.help-block').text("");
        });
        $('#group').change(function (){
            // Reset
            $("#po_code_wrapper").addClass("hidden"); 
            $("#po_code").attr( 'disabled', 'disabled' );
            $("#po_code").html('');           
            $("#terminal_code_wrapper").addClass("hidden");            
            $("#terminal_code").attr( 'disabled', 'disabled' ); 
            $("#terminal_code").html('');
            $("#mid_wrapper").addClass("hidden"); 
            $("#mid").attr( 'disabled', 'disabled' );           
            $("#mid").html('');             
            
            var group = $(this).val();
            switch(group){ 
                 case "9" :
                    $("#terminal_code_wrapper").removeClass("hidden");
                    $("#terminal_code").removeAttr( 'disabled');
                    $.get("<?php echo site_url('configuration/users/getTerminal'); ?>", function(data){
                        $("#terminal_code").html(data);
                    });
                break;               
                case "6":
                    $("#mid_wrapper").removeClass("hidden");
                    $("#mid").removeAttr( 'disabled');                    
                    $.get("<?php echo site_url('configuration/users/getMerchant'); ?>", function(data){
                        $("#mid").html(data);
                    });
                break;                
                case "4":
                    $("#po_code_wrapper").removeClass("hidden");
                    $("#po_code").removeAttr( 'disabled');
                    $.get("<?php echo site_url('configuration/users/getPo'); ?>", function(data){
                        $("#po_code").html(data);
                    });
                break;
                
                 }
        });
    });
</script>
