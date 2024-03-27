<style type="text/css"> 
    .wajib{color: red}
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/device_class/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Tipe Perangkat <span class="wajib">*</span></label>
                            <select id="device_type_terminal" class="form-control  select2 " dir="" required name="device_type_terminal">
                                <option value="">Pilih</option>
                                <?php foreach($terminal_type as $key=>$value ) {?>
                                <option value="<?php echo $value->terminal_type_id; ?>"><?php echo strtoupper($value->terminal_type_name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Perangkat <span class="wajib">*</span></label>
                            <input type="text" name="device_name" class="form-control" placeholder="Nama Perangkat" required>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select id="port" class="form-control  select2 " dir="" name="port" required>
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) {?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-4 form-group" id="divPass"  >
                            <label>Password <span class="wajib">*</span></label>
                            <input type="password" name="password" id="pass" class="form-control" placeholder="Password" required>
                        </div>

                        <div class="col-sm-4 form-group" id="div_ship_class"  style="display: none">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select id="ship_class" class="form-control select2 " dir="" name="ship_class" required style="width: auto">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value ) {?>
                                <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_imei"  style="display: none">
                            <label>Serial Number <span class="wajib">*</span></label>
                            <input type="text" name="imei" class="form-control" placeholder="Serial Number" required id="imei">
                        </div>

                        <div class="col-sm-4 form-group" id="div_dock" style="display: none">
                            <label>Dermaga <span class="wajib">*</span></label>
                            <select id="dock" class="form-control   select2 " name="dock" required style="width: auto">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_posPairing" style="display: none">
                            <label>Pos Pairing <span class="wajib">*</span></label>
                            <select id="posPairing" class="form-control   select2 " name="posPairing" required style="width: auto">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_crossClass" style="display: none">
                            <label>Antar Pelabuhan<span class="wajib">*</span></label>
                            <select id="crossClass" class="form-control   select2 " name="crossClass" required style="width: auto">
                                <option value="">Pilih</option>
                                <option value="true">Iya</option>
                                <option value="false">Tidak</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<script type="text/javascript">
    
    
    $.ajaxSetup({
    data: csfrData
    });
    function getPort() {
        $.ajax({
            type :"post",
            url :"<?php echo site_url()?>device_management/device_class/get_dock",
            data :{
                port:$("#port").val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`],
            },
            dataType:"json",
            beforeSend:function() {
                unBlockUiId('box')
            },
            success:function(x) {
                console.log(x);
                $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                csfrData[x['csrfName']] = x['tokenHash'];
                $.ajaxSetup({
                    data: csfrData
                });
                var html = "<option value=''>Pilih</option>";
                for(var i=0; i<x.data.length; i++) {
                    html += "<option value='"+x.data[i].id+"'>"+x.data[i].name+"</option>";
                }
                $("#dock").html(html);
            },
            complete: function(){
                $('#box').unblock(); 
            }
        });
    }
    function getPosVehicle() {
        $.ajax({
            type :"post",
            url :"<?php echo site_url()?>device_management/device_class/getPosVehicle",
            data :{
                port:$("#port").val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`],
            },
            dataType:"json",
            beforeSend:function() {
                unBlockUiId('box')
            },
            success:function(x) {
                $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                csfrData[x['csrfName']] = x['tokenHash'];
                $.ajaxSetup({
                    data: csfrData
                });
                // console.log(x);
                var html = "<option value=''>Pilih</option>";
                for(var i=0; i<x.data.length; i++) {
                    html += "<option value='"+x.data[i].terminal_code+"'>"+x.data[i].terminal_name+"</option>";
                }
                $("#posPairing").html(html);
            },
            complete: function(){
                $('#box').unblock(); 
            }
        });
    }    

    function getData() {
            var device_type = $("#device_type_terminal").val();

            // gate in 
            if(device_type == 4 || device_type == 5 ) {
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").show();
                $("#ship_class").attr('required','required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');
            }

            // vending dan pos
            else if(device_type == 1 || device_type == 2 || device_type == 3 || device_type == 12  ) {
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").show();
                $("#ship_class").attr('required','required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").show();
                $("#crossClass").attr('required','required');
            }            

            // vending
            // else if(device_type == 3) {
            //     $("#div_dock").hide();
            //     $("#div_ship_class").show();
            //     $("#div_imei").hide();
            //     $("#ship_class").removeAttr('required');
            //     $("#dock").removeAttr('required');
            //     $("#imei").removeAttr('required');
            // }

            // boarding
            else if(device_type == 7 || device_type == 8) {
                $("#div_dock").show();
                $("#div_ship_class").show();
                $("#div_imei").hide();
                $("#ship_class").attr('required','required');
                $("#dock").attr('required','required');
                $("#imei").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');                
                 // getPort();

                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');
            }

            // hand held/validator
            else if(device_type == 6) {
                $("#div_dock").show();
                $("#div_ship_class").hide();
                $("#div_imei").show();
                $("#imei").attr('required','required');
                $("#ship_class").removeAttr('required');
                $("#dock").attr('required','required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');                
            }

            // ktp reader
            else if(device_type == 11 ||device_type == 20 ) {
                $("#div_dock").hide();
                $("#div_ship_class").hide();
                $("#div_imei").show();                
                $("#imei").attr('required','required');
                $("#ship_class").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');    
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');
            }

            // Mobile POS
            else if(device_type == 16 || device_type == 17 || device_type == 15) {
                $("#div_dock").hide();
                $("#dock").removeAttr('required');

                $("#div_ship_class").show();
                $("#ship_class").attr('required');

                $("#div_imei").show();                
                $("#imei").attr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');

                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").show();
                $("#crossClass").attr('required','required');    
            }
            else if (device_type == 18)
            {

                $("#div_posPairing").show();
                $("#posPairing").attr('required','required');

                $("#div_ship_class").show();
                $("#posPairing").attr('required','required');

                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#divPass").hide();
                // $("#div_ship_class").hide();
                // $("#ship_class").removeAttr('required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');
                $("#pass").removeAttr('required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');
            }
            else if(device_type == 19){
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").show();
                $("#ship_class").attr('required','required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');     
                
                $("#divPass").hide();
                $("#pass").removeAttr('required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

            }

            else {
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").hide();
                $("#div_posPairing").hide();

                $("#posPairing").removeAttr('required');
                $("#ship_class").removeAttr('required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');
            }
    }

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
            });
        });

        $("#port").on("change",function(){
            getPort();
            getPosVehicle();
        });

        $("#device_type_terminal").on("change",function(){
            unBlockUiId('box');
            getData();
            $('#box').unblock();
        });

        // $("#device_type_terminal").on("change",function(){


        //     var device_type= $("#device_type_terminal").val();

        //     //pos dan gate in dan vending

        //     if(device_type==1 || device_type==2 || device_type==4 || device_type==5 || device_type==3 || device_type==12)
        //     {
        //         $("#div_dock").hide();
        //         $("#div_imei").hide();
        //         $("#div_ship_class").show();

        //         $("#ship_class").attr('required','required');

        //         $("#imei").removeAttr('required');
        //         $("#dock").removeAttr('required');
        //     }

        //     //vending
        //     // else if(device_type==3 )
        //     // {
        //     //     $("#div_dock").hide();
        //     //     $("#div_ship_class").show();
        //     //     $("#div_imei").hide();

        //     //     $("#ship_class").removeAttr('required');
        //     //     $("#dock").removeAttr('required');
        //     //     $("#imei").removeAttr('required');
        //     // }

        //     // boarding
        //     else if(device_type==7 || device_type==8)
        //     {
        //         $("#div_dock").show();
        //         $("#div_ship_class").show();
        //         $("#div_imei").hide();

        //         $("#ship_class").attr('required','required');
        //         $("#dock").attr('required','required');

        //         $("#imei").removeAttr('required');
        //          // getPort();
        //     }

        //     // hand held / validator
        //     else if(device_type==6 )
        //     {
        //         $("#div_dock").show();
        //         $("#div_ship_class").hide();
        //         $("#div_imei").show();

        //         $("#imei").attr('required','required');
        //         $("#ship_class").removeAttr('required');
        //         $("#imei").attr('required','required');
        //     }
        //     else
        //     {
        //         $("#div_dock").hide();
        //         $("#div_imei").hide();
        //         $("#div_ship_class").hide();

        //         $("#ship_class").removeAttr('required');
        //         $("#imei").removeAttr('required');
        //         $("#dock").removeAttr('required');   
        //     }
        //     // console.log(device_type);

        // });

    })
</script>
