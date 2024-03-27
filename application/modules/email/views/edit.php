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
                        <?php echo generate_button('port', 'view', '<a href="'.site_url('port').'" class="btn btn-warning">Kembali</a>'); ?>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form action="<?php echo site_url('port/update'); ?>" method="post" id="form-input" class="form-horizontal">
                    <input type="hidden" name="id" value="<?php cetak($this->enc->encode($details->id)) ; ?>">
                    <div class="form-body">                       
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Port Name <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="hidden" name="port_code" data-required="1" class="form-control" value="<?php cetak($details->port_code) ; ?>" readonly/>
                                <input type="text" name="port_name" data-required="1" class="form-control" value="<?php cetak($details->port_name) ; ?>" maxlength="100"/>
                            </div>
                        </div>
						<!--
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Kota <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="port_city" data-required="1" class="form-control" value="<?ph cetak($details->port_city); ?>" maxlength="100"/>
                            </div>
                        </div> -->
						
						<div class="form-group">
                            <label class="control-label col-md-3">
                                Province <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" >
							<!--	<select name="prov" class="form-control" id="prov">
									<?ph 
										$data_province=$this->db->query("select * from t_mtr_province order by name asc")->result(); 
										foreach ($data_province as $data_province) {
									?>
									<option name="data1" <?ph echo $details->province_id == $data_province->id?'selected':''; ?> ><?ph cetak($data_province->name);?></option>
									
									<?ph } ?>
								</select> -->								
								<select name="prov" class="form-control" id="prov" required data-placeholder="Select Province">
									<option></option>
									<?php foreach ($data_province as $data_province) {?>
									<option value="<?php echo cetak($data_province->id);?>" <?php echo $details->province_id == $data_province->id?'selected':''; ?> >
										<?php cetak($data_province->name);?>
									</option>
									<?php } ?>
								</select>
								
                            </div>
                        </div>
						
						<div class="form-group">
                            <label class="control-label col-md-3">
                                City <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" >	
								<select name="city" class="form-control" id="city" data-placeholder="Select City" required>
									<?php foreach ($data_city as $data_city) {?>
									<option value="<?php echo cetak($data_city->id);?>" <?php echo $details->city_id == $data_city->id?'selected':''; ?> >
										<?php cetak($data_city->name);?>
									</option>
									<?php } ?>
								</select>
								
                            </div>
                        </div>
						
						<div class="form-group">
                            <label class="control-label col-md-3">
                                District <span class="required"> * </span>
                            </label>
                            <div class="col-md-6" >	
								<select name="district" class="form-control" id="district" data-placeholder="Select district" required>
									<?php foreach ($data_district as $data_district) {?>
									<option value="<?php echo cetak($data_district->id);?>" <?php echo $details->district_id == $data_district->id?'selected':''; ?> >
										<?php cetak($data_district->name);?>
									</option>
									<?php } ?>
								</select>
								
                            </div>
                        </div>
                       
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-warning">Simpan</button>
                                <button type="reset" id="reset" class="btn default">Batal</button>
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
$("#prov").select2();
$("#city").select2();
$("#district").select2();

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
                    
                    port_name: {
                        required: true
                    },
                    port_city: {
                        required: true
                    },
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
                    error.appendTo(element.parents('.form-group'));
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
        FormValidation.init();
        $('#reset').on('click', function () {
            $('.form-group').removeClass('has-error');
            $('.help-block').text("");
        });
    });
	
	function dataCity(x='')
	{

		var province_id=x;
		$.ajax({	
		type : "POST",
		data :"province_id="+province_id,
		url : "<?php echo site_url('port/get_area')?>",
		dataType : "json",
		success : function (data){
				//console.log(data);
				
			var baris="";
					
			for(i=0; i<data.length; i++)
			{
					baris+="<option></option>"+
					"<option value='"+data[i].id+"'>"+data[i].name+"</option>";
			}	
			$("#city").html(baris);	
			} 
		});
	}
	

	$("#prov").on("change", function (){
		var province_id=$("[name='prov']").val();
		//console.log(province_id);
		dataCity(province_id);
		$("#opcity").hide();
	});
	
function dataDistrict(x='')
{
	var city_id=x;
	$.ajax({
		type:"POST",
		data : "district_id="+city_id,
		url : "<?php echo site_url('port/get_district');?>",
		dataType:"json",
		success:function(data){
			//console.log(data);
			baris="";
			
			for(i=0;i<data.length;i++)
			{
				baris+="<option > </option>"+
				"<option value='"+data[i].id+"'>"+data[i].name+"</option>";
			}
			
			$("#district").html(baris);
		}
	});
}

$("#city").change(function(){
	var city_id=$("[name='city']").val();
	dataDistrict(city_id);
});

</script>
