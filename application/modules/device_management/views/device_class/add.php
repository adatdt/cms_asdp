<style type="text/css"> 
    .wajib{color: red}
    .switch {
        position: relative;
        display: block;
        width: 90px;
        height: 34px;
        }

        .switch input {display:none;}

        .slidertes {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc; /*#ca2222;*/
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px !important;
        }

        .slidertes:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50% !important;
        }

        input:checked + .slidertes {
        background-color: #3598dc;
        }

        input:focus + .slidertes {
        box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slidertes:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(55px);
        }

        /*------ ADDED CSS ---------*/
        .slidertes:after
        {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 12px;
        font-family: "Open Sans", sans-serif;
        }

        input:checked + .slidertes:after
        {  
        content:'ON';
        }    
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
                            <select id="device_type_terminal" class="form-control  select22 " dir="" required name="device_type_terminal">
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
                            <select id="port" class="form-control  select22 " dir="" name="port" required>
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
                            <select id="ship_class" class="form-control select22 " dir="" name="ship_class" required style="width: auto">
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
                            <select id="dock" class="form-control   select22 " name="dock" required style="width: auto">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_posPairing" style="display: none">
                            <label>Pos Pairing <span class="wajib">*</span></label>
                            <select id="posPairing" class="form-control   select22 " name="posPairing" required style="width: auto">
                                <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group" id="div_crossClass" style="display: none">
                            <label>Lintas Kelas<span class="wajib">*</span></label>
                            <select id="crossClass" class="form-control   select22 " name="crossClass" required style="width: auto">
                                <option value="">Pilih</option>
                                <option value="true">Iya</option>
                                <option value="false">Tidak</option>
                            </select>
                        </div>

                        <div class="col-sm-12"></div>
                        <div class="col-sm-4 form-group" id="div_user_phone"  style="display: none">
                            <label>Username phone  <span class="wajib">*</span></label>
                            <?= form_dropdown("userPhone",array(""=>"Pilih"),"",' class="form-control select22" id="userPhone" required '); ?>                            
                        </div>

                        <div class="col-sm-4 form-group" id="div_ext_phone"  style="display: none">
                            <label>Extension Phone <span class="wajib">*</span></label>
                            <input type="text" name="extensionPhone" class="form-control" placeholder="Extension Phone" disabled required id="extensionPhone" >
                        </div>

                        <div class="col-sm-4 form-group" id="div_password_phone"  style="display: none">
                            <label>Password Phone <span class="wajib">*</span></label>
                            <input type="text" name="passwordPhone" class="form-control" placeholder="Password Phone" disabled required id="passwordPhone" >
                        </div>

                        <div class="col-sm-12"></div>

                        <div class="col-sm-4 form-group" id="div_vehicle_class" style="display:none;" >
                            
                            <label>Golongan KND <span class="wajib">*</span></label>
                            <div class="pull-right">Spesifik <input  type="checkbox" class="allow3" id="isSpesific"  data-checkbox="icheckbox_flat-grey" value="t"  name="isSpesific" checked></div>

                            <span id="non_multiple_vehicle_span">
                                <select id="vehicle_class" class="form-control select22 " dir="" name="vehicle_class"  >
                                    <option value="">Pilih</option>
                                    <?php foreach($vehicle_class as $keyVehicleClass=>$valueVehicleClass ) {?>
                                    <option value="<?php echo $this->enc->encode($valueVehicleClass->id); ?>"><?php echo strtoupper($valueVehicleClass->name) ?></option>
                                    <?php } ?>
                                </select>
                            </span>
                            <span id="multiple_vehicle_span" style="display:none;">
                                <select id="vehicle_class_multiple" class="form-control  multiple-select" dir="" name="vehicle_class_multiple"    multiple='multiple'>
                                    <?php foreach($vehicle_class as $keyVehicleClass2=>$valueVehicleClass2 ) {?>
                                    <option value="<?php echo $this->enc->encode($valueVehicleClass2->id); ?>"><?php echo strtoupper($valueVehicleClass2->name) ?></option>
                                    <?php } ?>
                                </select>

                                <input type="hidden" id="vehicle_class_multiple_value" name="vehicle_class_multiple_value">
                            </span>                            

                        </div>
                        <div class="col-sm-4 form-group" id="div_overpaid" style="display:none;">
                             <label>Lebih Bayar/ Kurang Bayar </label>
                            <input type="hidden" name="value_param" class="form-control" placeholder="Value Param" required="" value="false" aria-required="true">
                             <label class="switch" style="">
                                <input type="hidden" name="overpaid" value="0">
                                <input type="checkbox" name="overpaid"  value="1" checked><div class="slidertes round"></div>
                            </label>
                         </div>     
                         <div class="col-sm-4 form-group" id="div_sensor" style="display:none;">
                             <label>Sensor </label>
                            <input type="hidden" name="value_param" class="form-control" placeholder="Value Param" required="" value="false" aria-required="true">
                             <label class="switch" style="">
                                <input type="hidden" name="sensor" value="0">
                                <input type="checkbox" name="sensor" value="1"><div class="slidertes round"></div>
                            </label>
                         </div>                                   

                        <div class="col-sm-12"></div>
                        <div class="col-sm-4 form-group" id="div_ip"  style="display: none">
                            <label>IP <span class="wajib">*</span></label>
                            <input type="text" name="ip" class="form-control" placeholder="IP" required id="ip" >
                        </div>                        
                        <div class="col-sm-8 form-group " id="div_cctv_path"  style="display:none;">
                            <label>CCTV PATH <span class="wajib">*</span></label> 
                            <div class="form-inline" >
                                <a class="btn btn-md btn-warning pull-right" title="tambah" id="addCctv" ><i class="fa fa-plus"></i> </a>
                                <p id="content_0" class="deleteCctv">
                                    <input type="text" name="cctvpath[]" class="form-control cctvpath" placeholder="CCTV PATH"  id="cctvpath_0" style="width:85% !important"> 
                                    <a class="btn btn-md btn-danger " title="Hapus" onclick=deleteCctv(0) ><i class="fa fa-trash-o"></i> </a>
                                </p>

                                <span id="downCctv" ></span>
                            </div>
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

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 
        && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }    
   
    function getPort() {
        // console.log(csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`]);
        $.ajax({
            type :"post",
            url :"<?php echo site_url()?>device_management/device_class/get_dock",
            data :{
                port:$("#port").val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
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
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
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

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required'); 
                
                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }

            // vending dan pos
            else if(device_type == 1 || device_type == 2 || device_type == 3 || device_type == 12  ) {
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").show();
                $("#ship_class").attr('required','required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").show();
                $("#crossClass").attr('required','required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");           
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
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

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');                
                 // getPort();

                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }

            // hand held/validator
            else if(device_type == 6) {
                $("#div_dock").show();
                $("#div_ship_class").hide();
                $("#div_imei").show();
                $("#imei").attr('required','required');
                $("#ship_class").removeAttr('required');
                $("#dock").attr('required','required');

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');  
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');        
                
                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }

            // ktp reader
            else if(device_type == 11 ||device_type == 20 ) {
                $("#div_dock").hide();
                $("#div_ship_class").hide();
                $("#div_imei").show();                
                $("#imei").attr('required','required');
                $("#ship_class").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');    
                
                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }

            // Mobile POS
            else if(device_type == 16 || device_type == 17 || device_type == 15) {
                $("#div_dock").hide();
                $("#dock").removeAttr('required');

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

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

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })
                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
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

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                // $("#div_ship_class").hide();
                // $("#ship_class").removeAttr('required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');
                $("#pass").removeAttr('required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })

                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }
            else if(device_type == 19){
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").show();
                $("#ship_class").attr('required','required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');

                $("#div_cctv_path").hide();
                $(".cctvpath").removeAttr('required');

                $("#div_posPairing").hide();
                $("#posPairing").removeAttr('required');     
                
                $("#divPass").hide();
                $("#pass").removeAttr('required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })

                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');

            }   
            //selft service
            else if(device_type == 21  ) {
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

                $("#div_ext_phone").show();
                $("#extensionPhone").attr('required','required');                
                $("#div_user_phone").show();
                $("#userPhone").attr('required','required');
                $("#div_password_phone").show();
                $("#passwordPhone").attr('required','required');
                $("#div_cctv_path").show();
                $(".cctvpath").attr('required','required');

                $(`#div_vehicle_class`).show();
                $("#vehicle_class").attr('required','required');

                $(`#div_overpaid`).show();
                $(`#div_sensor`).show();

                $(`#div_ip`).show();
                $("#ip").attr('required','required');
                
            }                              

            else {
                $("#div_dock").hide();
                $("#div_imei").hide();
                $("#div_ship_class").hide();
                $("#div_posPairing").hide();

                $("#div_ext_phone").hide();
                $("#div_user_phone").hide();
                $("#div_password_phone").hide();
                $("#div_cctv_path").hide();                

                $("#extensionPhone").removeAttr('required');                
                $("#userPhone").removeAttr('required');
                $("#passwordPhone").removeAttr('required');
                $(".cctvpath").removeAttr('required');

                $("#posPairing").removeAttr('required');
                $("#ship_class").removeAttr('required');
                $("#imei").removeAttr('required');
                $("#dock").removeAttr('required');

                $("#divPass").show();
                $("#pass").attr('required','required');

                $("#div_crossClass").hide();
                $("#crossClass").removeAttr('required');

                $(`.deleteCctv`).each(function(i,obj){                    
                    let getId = $(obj).attr("id")   
                    $(`#${getId} input`).val("");  
                    if(i != 0)
                    {                        
                        $(`#${getId}`).remove()
                    }               
                })

                $(`#div_vehicle_class`).hide();
                $("#vehicle_class").removeAttr('required');
                $("#vehicle_class_multiple").removeAttr('required');

                $(`#div_overpaid`).hide();
                $(`#div_sensor`).hide();

                $(`#div_ip`).hide();
                $("#ip").removeAttr('required');
            }
    }

    $(document).ready(function(){

        validateForm2('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow3').iCheck({
            checkboxClass: 'icheckbox_square-blue service-icheck',
            radioClass: 'icheckbox_square-blue',
        });   
        
        $(`#isSpesific`).on('ifChecked ifUnchecked', function(event){
            if (event.type == `ifChecked`) {
                
                $('#vehicle_class').val(null).trigger('change');
                $(`#multiple_vehicle_span`).hide();                
                $(`#non_multiple_vehicle_span`).show();
                $("#vehicle_class").attr('required','required');
                $("#vehicle_class_multiple").removeAttr('required');
            }
            else
            {
                $('#vehicle_class_multiple').val(null).trigger('change');
                $(`#multiple_vehicle_span`).show();
                $(`#non_multiple_vehicle_span`).hide();
    
                $("#vehicle_class_multiple").attr('required','required');
                $("#vehicle_class").removeAttr('required');
                                                
            }   
        })        

        $("#vehicle_class_multiple").on("select2:select select2:unselect", function (e) {
            var data = e.params.data;
            let merchantData = $(this).val();

            $(`#vehicle_class_multiple_value`).val("")
            if(merchantData != null)
            {
                $(`#vehicle_class_multiple_value`).val(merchantData.toString())
            }
        })        

        $('.select22:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width: '100%', 
            });
        });

        $('#vehicle_class_multiple').select2({
            placeholder: "Pilih",
            width: '100%',
            formatSelectionCssClass: function (data, container) { return "label label-primary"; },
        });                

        $("#port").on("change",function(){

            $getPerangkat = $(`#device_type_terminal`).val();
            /** 
                - 7 boarding gate passanger
                - 8 boarding gate vehicle
                - 6 hand held
                - 18 pids Vehicle
                - 21 self service
            */

            if($getPerangkat == 7 || $getPerangkat == 8 || $getPerangkat == 6)
            {
                getPort();
            }

            if($getPerangkat == 18)
            {
                getPosVehicle();
            }
            
            if($getPerangkat == 21) 
            {
                getExt()
            }
        });

        $("#device_type_terminal").on("change",function(){
            // unBlockUiId('box');
            getData();
            // $('#box').unblock();
            $getPerangkat = $(this).val();
            if($getPerangkat == 21) 
            {
                getExt()
            }            
        });

        let idx =1;
        $(`#addCctv`).on("click", function(){
            let html =`
            <p id="content_${idx}" class="deleteCctv" style="display:none;">
                <input required type="text" name="cctvpath[${idx}]" class="form-control cctvpath" placeholder="CCTV PATH"  id="cctvpath_${idx}" style="width:85% !important"> 
                <a class="btn btn-md btn-danger " title="Hapus"  onclick=deleteCctv(${idx}) " ><i class="fa fa-trash-o"></i> </a>
            </p>
            `
            $(html).insertBefore(`#downCctv`);
            $(`#content_${idx}`).slideDown();
            idx++;

        });

    })

    function deleteCctv(id)
    {
        $(document).ready(function(){

            let coutClassCctv = $(`.deleteCctv`).length

            if(coutClassCctv>1)
            {
                $(`#content_${id}`).slideUp('normal',function(){
                    $(this).remove()
                })
            }
            else
            {
                toastr.error("Minimal harus ada satu inputan path CCTV", 'Gagal');   
            }
            
        })
    }

    function validateForm2(id, callback) {
	    $(id).validate({
            ignore: 'input[type=hidden], .select2-search__field',
            errorClass: 'validation-error-label',
            successClass: 'validation-valid-label',
            rules: rules,
            messages: messages,

            highlight: function (element, errorClass) {
                $(element).addClass('val-error');
            },

            unhighlight: function (element, errorClass) {
                $(element).removeClass('val-error');
            },

            errorPlacement: function (error, element) {
                // console.log(element.hasClass('cctvpath'))
                if (element.parents('div').hasClass('has-feedback')) {
                    error.appendTo(element.parent());
                }
                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo(element.parent());
                }
                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('cctvpath')) {
                    error.appendTo(element.parent());
                }           

                else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function (form) {
                if (typeof callback != 'undefined' && typeof callback == 'function') {
                    callback(form.action, getFormData($(form)));
                }
            }
        });
    }

    function getExt()
    {
        $.ajax({
                url : `<?= site_url() ?>device_management/device_class/getextension`,
                data : `portId=${$(`#port`).val()}&<?php echo $this->security->get_csrf_token_name(); ?> = ${$("input[name=" + csfrData.csrfName + "]").val()}`,
                type        : 'POST',
                dataType    : 'json',
                beforeSend: function(){
                    unBlockUiId('box')
                },
                success : function(data)
                {
                    $("input[name=" + data.csrfName + "]").val(data.tokenHash);
                    csfrData[data['csrfName']] = data['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });

                    const x = data.data;
                    let userPhoneHtml = `<option value="" datal-pass="" data-ext="" >Pilih</option>`;

                    for (const key in x) {
                        let selectdata ="";
                        if(x[key].id == data.selectData)
                        {
                            selectdata =" selected";
                        }
                        userPhoneHtml +=`<option value="${x[key].id}" data-pass="${x[key].password_phone}" data-ext="${x[key].extension_phone}" ${selectdata}>${x[key].username_phone} (${x[key].port_name})</option>`;
                    }
                    $(`#userPhone`).html(userPhoneHtml);
                    $(`#extensionPhone`).val("");
                    $(`#passwordPhone`).val("");
                },
                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },
                complete: function(){
                    $('#box').unblock(); 
                }                
            })       
            
        $(`#userPhone`).on("change", function()
        {
            const pass = $(this).find(':selected').data('pass');
            const ext = $(this).find(':selected').data('ext');

            $(`#extensionPhone`).val(ext);
            $(`#passwordPhone`).val(pass);

        })            
    }

    
</script>
