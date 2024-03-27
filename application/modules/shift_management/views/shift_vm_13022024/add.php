 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/shift_vm/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port" id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kode Perangkat</label>
                            <select class="form-control select2" required name="device_code" id="device_code">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift </label>
                            <select class="form-control select2" required name="shift" id="shift">
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift Login</label>

                            <input type="time"  class="form-control" name="shift_login" id="shift_login" placeholder="Shift" required >
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift Logout</label>

                            <input type="time" class="form-control" name="shift_logout" id="shift_logout" placeholder="Shift" required >
                        </div>


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    function getDeviceCode()
    {

        $.ajax({
            type:"post",
            data:{"port":$("#port").val(),
                    "<?php echo $this->security->get_csrf_token_name(); ?>" : $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()},
            url:"<?php echo site_url()?>shift_management/shift_vm/get_device_code",
            dataType:"json",
            success:function(x){

                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length;i++)
                {
                    html +="<option value='"+x[i].terminal_code+"'>"+x[i].full_code+"</option>";                    
                }

                $("#device_code").html(html);
                $('[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(x[0].tokenHash);
                // console.log(html);   
            }
        });
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $("#port").on("change",function(){
            getDeviceCode();
        });

    })
</script>