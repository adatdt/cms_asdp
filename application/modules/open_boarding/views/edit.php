<style>
.has-error .select2-selection {
    border: 1px solid #a94442;
    border-radius: 4px;
}
</style>

<div class="page-content-wrapper">
    <div class="page-content"> 
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent1 . '">' . $parent1 . '</a>'; ?>
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
                    <h4><?php cetak($title); ?></h4>
                </div>
                <div class="tools">
                    <div class="pull-right">
                        <?php echo generate_button('port', 'view', '<a href="'.site_url('sandar').'" class="btn btn-warning">Kembali</a>'); ?>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form action="<?php echo site_url('open_boarding/save')?>" method="post"  id="form-input" class="form-horizontal">
                    <div class="form-body">
                     <!-- 
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Nama kapal <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="ship_name" data-required="1" maxlength="100" class="form-control"/>
                            </div>
                        </div>
                    -->
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Pelabuhan <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" data-required="1">
                                <input type="input" class="form-control" value='<?php echo $dock->port_name; ?>' disabled >
                                <input type="hidden" name="dock_id" class="form-control" value='<?php echo $dock->dock_id2; ?>'  >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Nama kapal <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" data-required="1">
                                <input type="input" class="form-control" value='<?php echo $dock->ship_name; ?>' disabled >                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Dermaga <span class="required"> * </span>
                            </label>

                            <div class="col-md-6" data-required="1">
                                <input type="input" class="form-control" value='<?php echo $dock->dock_name; ?>' disabled >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Tanggal Sandar <span class="required"> * </span>
                            </label>

                            <div class="col-md-6" data-required="1">
                                <input type="input" class="form-control" value='<?php echo format_datetime($dock->created_on); ?>' disabled >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Tanggal / Jam Open Boarding <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" data-required="1">
                                <div class="input-group">
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                                <input class="form-control input-small date" name="open_date"id="datefrom" placeholder="yyyy-mm-dd hh-ii">
                                </div>
                            </div>
                        </div>
                        <!--
						<div class="form-group">
                            <label class="control-label col-md-3">
                                District <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
								<select id="district" class="form-control " data-placeholder="Select district" name="district" required ></select>
                            </div>
                        </div>
                    -->
                        
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-warning">Simpan</button>
                                <button type="reset" class="btn default" id="reset">Batal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function (){


	$(".prov").select2({});
	$("#city").select2();
	$("#district").select2();

});


    var FormValidation = function () {

        var handleValidation = function () {

            var form = $('#form-input')
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: 'input[type=hidden]', // validate all fields including form hidden input
                rules: {
                    
                    port_name: {
                        required: true
                    },
                    city: {
                        required: true                        
                    },
                    dermaga:{
                        required:true
                    },
                    ship:{
                        required:true
                    },
                    port:{
                        required:true
                    },
                },

                messages: {// custom messages for radio buttons and checkboxes
                    
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    // if (element.parent(".input-group").size() > 0) {
                    //     error.insertAfter(element.parent(".input-group"));
                    // } else if (element.attr("data-error-container")) {
                    //     error.appendTo(element.attr("data-error-container"));
                    // } else if (element.parents('.radio-list').size() > 0) {
                    //     error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                    // } else if (element.parents('.radio-inline').size() > 0) {
                    //     error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                    // } else if (element.parents('.checkbox-list').size() > 0) {
                    //     error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                    // } else if (element.parents('.checkbox-inline').size() > 0) {
                    //     error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                    // } else {
                    //     error.insertAfter(element); // for other inputs, just perform default behavior
                    // }
                    //error.appendTo(element.parents('.form-group'));
					    	if (element.hasClass('select2')) {     
								error.insertAfter(element.next('span'));  // select2
							} else {                                      
								error.appendTo(element.parents('.form-group'));               // default
							}
							
							
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label.closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                // submitHandler: function (form) {
                //     success.show();
                //     error.hide();
                //     form[0].submit(); // submit the form
                // }

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
        $('.date').datetimepicker({
       // minDate:moment().add(52,'minutes'),
        minuteStep:1,
        format: 'yyyy-mm-dd hh:ii',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate',function(e) {
        //sandar.reload();
    });
});
</script>
