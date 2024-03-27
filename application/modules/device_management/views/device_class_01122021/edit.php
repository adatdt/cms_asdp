<style type="text/css"> 
    .wajib{color: red} 
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/device_class/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Kode Perangkat <span class="wajib">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Kode Perangkat" required value="<?php echo $detail->terminal_code ?>" disabled>
                        </div>

                        <div class="col-sm-4 form-group" id="div_terminal_type">
                            <label>Tipe Perangkat <span class="wajib">*</span></label>
                            <select id="detail" class="form-control  select2 " dir="" required name="detail" disabled>
                                <option value="">Pilih</option>
                                <?php foreach($terminal_type as $key=>$value ) {?>
                                <option value="<?php echo $value->terminal_type_id; ?>" <?php echo $value->terminal_type_id==$detail->terminal_type?"selected":""; ?> ><?php echo strtoupper($value->terminal_type_name) ?></option>
                                <?php } ?>
                            </select>
                            <!-- <input type="hidden" name="detail" id="detail" value="<?php // echo $detail->terminal_type ?>"> -->
                            <input type="hidden" name="device_type_terminal" id="device_type_terminal" value="<?php echo $detail->terminal_type ?>">
                            <input type="hidden" name="device_terminal_id" id="device_terminal_id" value="<?php echo $this->enc->encode($detail->device_terminal_id) ?>">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Perangkat <span class="wajib">*</span></label>
                            <input type="text" name="device_name" class="form-control" placeholder="Nama Perangkat" required value="<?php echo $detail->terminal_name ?>">
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select id="port" class="form-control  select2 " dir="" name="port" required>
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) {?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>"  <?php echo $value->id==$detail->port_id?"selected":""; ?>><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_ship_class"  >
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select id="ship_class" class="form-control select2 " dir="" name="ship_class" required >
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value ) {?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>"  <?php echo $value->id==$detail->ship_class?"selected":""; ?>><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_imei" >
                            <label>Serial Number <span class="wajib">*</span></label>
                            <input type="text" name="imei" value="<?php echo $detail->imei; ?>" class="form-control" placeholder="Serial Number" required id="imei">
                        </div>

                        <div class="col-sm-4 form-group" id="div_dock" >
                            <label>Dermaga <span class="wajib">*</span></label>
                            <select id="dock" class="form-control   select2 " name="dock" required >
                                <?php foreach($dock as $key=>$value) {?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $value->id==$detail->dock_id?"selected":""; ?> ><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getPort() {
        $.ajax({
            type :"post",
            url :"<?php echo site_url()?>device_management/device_class/get_dock",
            data :"port="+$("#port").val(),
            dataType:"json",
            beforeSend:function() {
                unBlockUiId('box')
            },
            success:function(x) {
                var html = "<option value=''>Pilih</option>";
                for(var i=0; i<x.length; i++) {
                    html += "<option value='"+x[i].id+"'>"+x[i].name+"</option>";
                }
                $("#dock").html(html);
            },
            complete: function() {
                $('#box').unblock(); 
            }            
        });
    }

    var device_type= $("#detail").val();

    if(device_type == 1 || device_type == 2 || device_type == 4 || device_type == 5 || device_type== 3 || device_type == 12) {
        $("#div_dock").hide();
        $("#div_imei").hide();
        $("#div_ship_class").show();
        $("#ship_class").attr('required','required');
        $("#imei").removeAttr('required');
        $("#dock").removeAttr('required');
    }

    // boarding
    else if(device_type==7 || device_type==8) {
        $("#div_dock").show();
        $("#div_ship_class").show();
        $("#div_imei").hide();
        $("#ship_class").attr('required','required');
        $("#dock").attr('required','required');
        $("#imei").removeAttr('required');
    }

    // hand held / validator
    else if(device_type==6) {
        $("#div_dock").show();
        $("#div_ship_class").hide();
        $("#div_imei").show();
        $("#imei").attr('required','required');
        $("#ship_class").removeAttr('required');
        $("#dock").attr('required','required');
    }

    // ktp reader
    else if(device_type==11) {
        $("#div_dock").hide();
        $("#div_ship_class").hide();
        $("#div_imei").show();
        $("#imei").attr('required','required');
        $("#ship_class").removeAttr('required');
        $("#dock").removeAttr('required');
    }
        
    // Mobile POS
    else if(device_type == 16 || device_type == 17 || device_type == 18) {
        $("#div_dock").hide();
        $("#dock").removeAttr('required');

        $("#div_ship_class").show();
        $("#ship_class").attr('required');

        $("#div_imei").show();
        $("#imei").removeAttr('required');
        $("#imei").attr("disabled", true);

        $("#detail").removeAttr('disabled');
    }

    else {
        $("#div_dock").hide();
        $("#div_imei").hide();
        $("#div_ship_class").hide();
        $("#ship_class").removeAttr('required');
        $("#imei").removeAttr('required');
        $("#dock").removeAttr('required');
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
            getPort();
        });
    })
</script>
