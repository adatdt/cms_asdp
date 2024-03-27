 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/shift_vm/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port" id="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kode Perangkat</label>
                            <select class="form-control select2" required name="device_code" id="device_code">
                                <option value="">Pilih</option>
                                <?php foreach($device_code as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->terminal_code); ?>" <?php echo $value->terminal_code==$detail->terminal_code?"selected":""; ?> ><?php echo strtoupper($value->terminal_code); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift </label>
                            <select class="form-control select2" required name="shift" id="shift">
                                <option value="">Pilih</option>
                                <?php foreach($shift as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->shift_id?"selected":""; ?>><?php echo strtoupper($value->shift_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-4 form-group">
                            <label>Shift Login</label>

                            <input type="time"  class="form-control" name="shift_login" id="shift_login" placeholder="Shift" required value="<?php echo $detail->shift_login ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift Logout</label>

                            <input type="time" class="form-control" name="shift_logout" id="shift_logout" placeholder="Shift" required value="<?php echo $detail->shift_logout?>" >

                            <input type="hidden" value="<?php echo $this->enc->encode($detail->id) ?>" name="shift_vending" required >
                        </div>


                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

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

        $("#port").on("change",function(){
            getDeviceCode();
        });
    })
</script>