<script type="text/javascript">

function getSchema()
{
    $.ajax({
        dataType:'json',
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_schema',
        data:'schema='+$("#discount_schema").val(),
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x){

            // console.log(x);
            var html ="";
            $('#schema_code').val(x.schema_code);

            if(x.init_code=='a')
            {

                html += "<div  id='field'>"
                        +"<div class='col-sm-12 form-group'><label>Berlaku<span class='wajib'>*</span></label></div>"
                        +"<div class='col-sm-2 form-group'><input type='checkbox' value='true' class='allow' name='pos_passanger' id='pos_passanger'>POS Penumpang"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                        +"<input type='checkbox'  value='true' class='allow' name='pos_vehicle' id='pos_vehicle'>POS Kendaraan"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='vm' id='vm'>VM"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='mobile' id='mobile'>Mobile"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='web' id='web'>Web"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='b2b' id='b2b'>B2B"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='ifcs' id='ifcs'>IFCS"
                        +"</div>"

                        +"<div class='col-sm-12 '><hr></div>"

                        +"<div class='col-sm-12 form-group'></div>"

                            +"<div class='col-sm-3 form-group'>"
                                +"<label>Rute<span class='wajib'>*</span></label>"
                               + "<select class='form-control select2' name='route' id='route' required >"
                                    +"<option value=''>Pilih</option>"
                                    +"<?php foreach($route as $key=>$value ) { ?>"
                                        +"<option value='<?php echo $this->enc->encode($value->id) ?>' ><?php echo strtoupper($value->route_name) ?></option>"
                                    +"<?php } ?>"
                               + "</select>"
                           + "</div>"

                            +"<div class='col-sm-3 form-group'>"                                    
                                +"<label>Pelabuhan<span class='wajib'>*</span></label>"
                                +"<input type='text' class='form-control ' name='port' id='port' required readonly>"
                            +"</div>"

                            +"<div class='col-sm-12 form-group' ><hr></div>"

                            +"<div class='col-sm-12 form-group'><label>Tipe Pembayaran <span class='wajib'>*</span></label></div>"

                            +"<?php foreach($payment_type as $key=>$value) { ?>"
                            +"<div class='col-sm-2 form-group'>"
                                +"<input type='checkbox' value='<?php echo $value->payment_type ?>' class='allow' name='payment_type[<?php echo $key ?>]' id='<?php echo $value->payment_type?>'><?php echo $value->payment_type ?>"
                            +"</div>"
                            +"<?php } ?>"

                            +"<div class='col-sm-12 form-group' id='fareInput2'></div>"                            

                            +"<div class='col-sm-12 form-group' style='display:none' id='fareInput'>"
                                +"<div class='kt-portlet'>"
                                    +"<div class='kt-portlet__head'>"
                                        +"<div class='kt-portlet__head-label'>"
                                            +"<h3 class='kt-portlet__head-title'></h3>"
                                        +"</div>"
                                    +"</div>"
                                    +"<div class='kt-portlet__body'>"
                                        +"<ul class='nav nav-tabs ' role='tablist'>"
                                            +"<li class='nav-item active'>"
                                                    +"<a class='label label-primary ' data-toggle='tab' href='#fare_passanger'>Tarif  Penumpang Reguler</a>"
                                            +"</li>"
                                            +"<li class='nav-item'>"
                                                    +"<a class='label label-primary ' data-toggle='tab' href='#fare_passanger_eks'>Tarif Penumpang Eksekutif</a>"
                                            +"</li>"

                                            +"<li class='nav-item'>"
                                                    +"<a class='label label-primary ' data-toggle='tab' href='#fare_vehicle'>Tarif Kendaraan Reguler</a>"
                                            +"</li>"

                                            +"<li class='nav-item'>"
                                                    +"<a class='label label-primary ' data-toggle='tab' href='#fare_vehicle_eks'>Tarif Kendaraan Eksekutif</a>"
                                            +"</li>"                                
                                        +"</ul>"
                      
                                        +"<div class='tab-content' >"
                                            +"<!-- Fare Penumpang-->"
                                            +"<div class='tab-pane active' id='fare_passanger' role='tabpanel' >"
                                                +"<div class='col-sm-12 form-group' id='fareData1'></div>"
                                            +"</div>"

                                            +"<div class='tab-pane' id='fare_passanger_eks' role='tabpanel'>"
                                                +"<div class='col-sm-12 form-group' id='fareData2'></div>"
                                            +"</div>"

                                            +"<div class='tab-pane' id='fare_vehicle' role='tabpanel'>"
                                                +"<div class='col-sm-12 form-group' id='fareData3'></div>"                                        
                                            +"</div>"

                                            +"<div class='tab-pane' id='fare_vehicle_eks' role='tabpanel'>"
                                                +"<div class='col-sm-12 form-group' id='fareData4'></div>"   
                                            +"</div>"                                

                                        +"</div>"      
                                    +"</div>"
                                +"</div>"

                            +"</div>"
                        +"</div>"
                $("#field").remove();
                $(html).insertAfter( "#get_form" );
            }
            else if (x.init_code=='b')
            {
                html += "<div  id='field'>"
                        +"<div class='col-sm-12 form-group'><label>Berlaku<span class='wajib'>*</span></label></div>"
                        +"<div class='col-sm-2 form-group'><input type='checkbox' value='true' class='allow' name='pos_passanger' id='pos_passanger'>POS Penumpang"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                        +"<input type='checkbox'  value='true' class='allow' name='pos_vehicle' id='pos_vehicle'>POS Kendaraan"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='vm' id='vm'>VM"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='mobile' id='mobile'>Mobile"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='web' id='web'>Web"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='b2b' id='b2b'>B2B"
                        +"</div>"

                        +"<div class='col-sm-2 form-group'>"
                            +"<input type='checkbox' value='true' class='allow' name='ifcs' id='ifcs'>IFCS"
                        +"</div>"
                        

                        +"<div class='col-sm-12 '><hr></div>"

                        +"<div class='col-sm-12 form-group'></div>"

                            +"<div class='col-sm-3 form-group'>"
                                +"<label>Rute<span class='wajib'>*</span></label>"
                               + "<select class='form-control select2' name='route' id='route2' required >"
                                    +"<option value=''>Pilih</option>"
                                    +"<?php foreach($route as $key=>$value ) { ?>"
                                        +"<option value='<?php echo $this->enc->encode($value->id) ?>' ><?php echo strtoupper($value->route_name) ?></option>"
                                    +"<?php } ?>"
                               + "</select>"
                           + "</div>"

                            +"<div class='col-sm-3 form-group'>"                                    
                                +"<label>Pelabuhan<span class='wajib'>*</span></label>"
                                +"<input type='text' class='form-control ' name='port' id='port' required readonly placeholder='Pelabuhan'>"
                            +"</div>"


                            +"<div class='col-sm-3 form-group'>"
                                +"<label>Tipe Kapal<span class='wajib'>*</span></label>"
                               + "<select class='form-control select2' name='ship_class' id='ship_class' required >"
                                    +"<option value=''>Pilih</option>"
                                    +"<option value='<?php echo $this->enc->encode('all') ?>' >SEMUA TIPE</option>"
                                    +"<?php foreach($ship_class as $key=>$value ) { ?>"
                                        +"<option value='<?php echo $this->enc->encode($value->id) ?>' ><?php echo strtoupper($value->name) ?></option>"
                                    +"<?php } ?>"
                               + "</select>"
                           + "</div>"  

                            +"<div class='col-sm-3 form-group'>"                                    
                                +"<label>Potongan Harga<span class='wajib'>*</span></label>"
                                +"<input type='text' class='form-control ' name='value' id='value' required placeholder='Potongan Harga' onkeypress='return isNumberKey(event)'>"
                            +"</div>"                          

                            +"<div class='col-sm-3 form-group'>"
                                +"<label>Tipe Potongan<span class='wajib'>*</span></label>"
                               + "<select class='form-control select2' name='value_type' id='value_type' required >"
                                    +"<option value='' >Pilih</option>"
                                    +"<?php foreach($value_type as $key=>$value ) { ?>"
                                        +"<option value='<?php echo $this->enc->encode($value->id) ?>' ><?php echo strtoupper($value->name) ?></option>"
                                    +"<?php } ?>"
                               + "</select>"
                           + "</div>"  


                            +"<div class='col-sm-12 form-group' ><hr></div>"

                            +"<div class='col-sm-12 form-group'><label>Tipe Pembayaran <span class='wajib'>*</span></label></div>"

                            +"<?php foreach($payment_type as $key=>$value) { ?>"
                            +"<div class='col-sm-2 form-group'>"
                                +"<input type='checkbox' value='<?php echo $value->payment_type ?>' class='allow' name='payment_type[<?php echo $key ?>]' id='<?php echo $value->payment_type?>'><?php echo $value->payment_type ?>"
                            +"</div>"
                            +"<?php } ?>"   

                        +"</div>"

                $("#field").remove();
                $(html).insertAfter( "#get_form" );

            }
            else
            {
                $("#field").remove();
            }

            
            $('.allow').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'icheckbox_square-blue',
            });

            $('.select2:not(.normal)').each(function () {
                $(this).select2({
                    dropdownParent: $(this).parent()
                });
            });

            // mendapatkan harga
           $("#route").on("change",function(){
                getFare();
            });

         $("#route2").on("change",function(){
            getPortName();
        });


            // console.log(html);
        },
        complete: function(){
            $('#box').unblock(); 
        }
    });
}

function getData(param)
{
    if( $("#entry_fee"+param).val()=='')
    {
        var entry_fee=0;

    }
    else
    {
        var entry_fee=parseInt($("#entry_fee"+param).val());            
    }

    if($("#dock_fee"+param).val()=='')
    {
        var dock_fee=0;
    }
    else
    {
        var dock_fee=parseInt($("#dock_fee"+param).val());      
    }

    if($("#ifpro_fee"+param).val()=='')
    {
        var ifpro =0;   
    }
    else
    {
        var ifpro = parseInt($("#ifpro_fee"+param).val());     
    }

    if($("#trip_fee"+param).val()=='')
    {
        var trip_fee=0;
    }
    else
    {
        var trip_fee = parseInt($("#trip_fee"+param).val());    
    }

    if($("#responsibility_fee"+param).val()=='')
    {
        var responsibility_fee=0;
    }
    else
    {
        var responsibility_fee = parseInt($("#responsibility_fee"+param).val());    
    }


    if($("#insurance_fee"+param).val()=='')
    {
        var insurance_fee=0;
    }
    else
    {
        var insurance_fee=parseInt($("#insurance_fee"+param).val()); 
    }

    harga=entry_fee+dock_fee+ifpro+responsibility_fee+insurance_fee+trip_fee;

    $("#fare"+param).val(harga);

    // console.log(harga);

}

function getDataVehicle(param)
{
    if( $("#vehicle_entry_fee"+param).val()=='')
    {
        var entry_fee=0;

    }
    else
    {
        var entry_fee=parseInt($("#vehicle_entry_fee"+param).val());            
    }

    if($("#vehicle_dock_fee"+param).val()=='')
    {
        var dock_fee=0;
    }
    else
    {
        var dock_fee=parseInt($("#vehicle_dock_fee"+param).val());      
    }

    if($("#vehicle_ifpro_fee"+param).val()=='')
    {
        var ifpro =0;   
    }
    else
    {
        var ifpro = parseInt($("#vehicle_ifpro_fee"+param).val());     
    }

    if($("#vehicle_trip_fee"+param).val()=='')
    {
        var trip_fee=0;
    }
    else
    {
        var trip_fee = parseInt($("#vehicle_trip_fee"+param).val());    
    }

    if($("#vehicle_responsibility_fee"+param).val()=='')
    {
        var responsibility_fee=0;
    }
    else
    {
        var responsibility_fee = parseInt($("#vehicle_responsibility_fee"+param).val());    
    }


    if($("#vehicle_insurance_fee"+param).val()=='')
    {
        var insurance_fee=0;
    }
    else
    {
        var insurance_fee=parseInt($("#vehicle_insurance_fee"+param).val()); 
    }

    harga=entry_fee+dock_fee+ifpro+responsibility_fee+insurance_fee+trip_fee;

    $("#vehicle_fare"+param).val(harga);

    // console.log(harga);

}

function getPort()
{
    $.ajax({
        dataType:'json',
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_port',
        // beforeSend:function(){
        //     unBlockUiId('box')
        // },
        success:function(x){

            // $('#schema_code').val(x.schema_code);
            // console.log(x);
        }
        // ,
        // complete: function(){
        //     $('#box').unblock(); 
        // }
    });
}

function getRoute()
{
    $.ajax({
        dataType:'json',
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_route',
        data:'port='+$("#port").val(),
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x){

            // console.log(x);
            // var html="<option value=''>Pilih</option>";

            var html="<option value=''>Pilih</option>";

            for(var i=0; i<x.length; i++)
            {
                html +="<option value='"+x[i].id+"'>"+x[i].route_name+"</option>";
            }

            $("#route").html(html);
        },
        complete: function(){
            $('#box').unblock(); 
        }
    });
}

function getPortName()
{
    var rt = document.getElementById('route2');
    var opt= rt.options[rt.selectedIndex].text;
    var splitPort=opt.split("-")

    $("#port").val(splitPort[0]);

}


function getFare()
{
    $.ajax({
        dataType:'json',
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_fare',
        data:'route='+$("#route").val(),
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x){

            // $('#schema_code').val(x.schema_code);

            // Mendapatkan nama pelabuhan dari option yang dipilih
            var rt = document.getElementById('route');
            var opt= rt.options[rt.selectedIndex].text;
            var splitPort=opt.split("-")

            $("#port").val(splitPort[0]);

            
            var err="<div style=' background-color: #ecf4fa; padding:10px; margin:10px 10px; text-align: center; '>Tidak ada data</div>";
            var html="";
            if (x.passanger_reg.length>0)
            {

                html="<div class='portlet light bordered'>"
                    +"<div class='portlet-title'>"
                        +"<div class='caption'>"
                            +"<i class='fa fa-money font-blue-sharp'></i>"
                            +"<span class='caption-subject font-blue-sharp bold uppercase'>Penumpang "+x.passanger_reg[0].ship_class_name+"</span>"
                        +"</div>"
                    +"</div>"
                    +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"

                for(var i=0; i<x.passanger_reg.length; i++)
                {

                    html +="<tr><td><div class='row'>"
                        +"<div class='col-sm-2 form-group'><label>Tipe Penumpang<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='passanger_type_name["+i+"]' id='passanger_type_name"+i+"' required value='"+x.passanger_reg[i].passanger_type_name+"' readonly>"
                            +"<input type='hidden' value='"+x.passanger_reg[i].passanger_type+"' name='passanger_type["+i+"]' id='passanger_type"+i+"' required' >"
                            +"<input type='hidden' value='"+x.passanger_reg[i].ship_class+"' name='ship_class["+i+"]' id='ship_class"+i+"' required' >"
                        +"</div>"

                    html +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tipe<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='ship_class_name["+i+"]' id='ship_class_name"+i+"' required value='"+x.passanger_reg[i].ship_class_name+"' readonly></div>"


                    html +="<div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='entry_fee["+i+"]' id='entry_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")' required value='"+x.passanger_reg[i].entry_fee+"'></div>"

                    html +="<div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='dock_fee["+i+"]' id='dock_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")' required value='"+x.passanger_reg[i].dock_fee+"'></div>"

                    html +="<div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='ifpro_fee["+i+"]' id='ifpro_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")'  required value='"+x.passanger_reg[i].ifpro_fee+"'></div>"

                    html +="<div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='trip_fee["+i+"]' id='trip_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")' required value='"+x.passanger_reg[i].trip_fee+"'></div>"

                    html +="<div class='col-sm-12 form-group'></div>"    

                    html +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='responsibility_fee["+i+"]' id='responsibility_fee"+i+"'  onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")' required value='"+x.passanger_reg[i].responsibility_fee+"'></div>"

                    html +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='insurance_fee["+i+"]' id='insurance_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+")' required value='"+x.passanger_reg[i].insurance_fee+"'></div>"

                    html +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Harga<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='fare["+i+"]' id='fare"+i+"' onkeypress='return isNumberKey(event)' required readonly value='"+x.passanger_reg[i].fare+"'></div>"

                    html +="</div></td></tr>"

                }

                html +="</div></div>"

                $("#fareData1").html(html);

            }
            else
            {
                $("#fareData1").html(err);               
            }

            var html2="";
            if (x.passanger_eks.length>0)
            {

                html2="<div class='portlet light bordered'>"
                    +"<div class='portlet-title'>"
                        +"<div class='caption'>"
                            +"<i class='fa fa-money font-blue-sharp'></i>"
                            +"<span class='caption-subject font-blue-sharp bold uppercase'>Penumpang "+x.passanger_eks[0].ship_class_name+"</span>"
                        +"</div>"
                    +"</div>"
                    +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"


                for(var i=0; i<x.passanger_eks.length; i++)
                {

                    html2 +="<tr><td><div class='row'>"
                    +"<div class='col-sm-2 form-group'><label>Tipe Penumpang<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='passanger_type_name_eks["+i+"]' id='passanger_type_name_eks"+i+"' required value='"+x.passanger_eks[i].passanger_type_name+"' readonly>"
                            +"<input type='hidden' value='"+x.passanger_eks[i].passanger_type+"' name='passanger_type_eks["+i+"]' id='passanger_type_eks"+i+"' required' >"
                            +"<input type='hidden' value='"+x.passanger_eks[i].ship_class+"' name='ship_class_eks["+i+"]' id='ship_class_eks"+i+"' required' >"
                        +"</div>"

                    html2 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tipe<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='ship_class_name_eks["+i+"]' id='ship_class_name_eks"+i+"' required value='"+x.passanger_eks[i].ship_class_name+"' readonly></div>"


                    html2 +="<div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='entry_fee_eks["+i+"]' id='entry_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+") required value='"+x.passanger_eks[i].entry_fee+"'></div>"

                    html2 +="<div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='dock_fee_eks["+i+"]' id='dock_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+") required value='"+x.passanger_eks[i].dock_fee+"'></div>"

                    html2 +="<div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='ifpro_fee_eks["+i+"]' id='ifpro_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+")  required value='"+x.passanger_eks[i].ifpro_fee+"'></div>"

                    html2 +="<div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='trip_fee_eks["+i+"]' id='trip_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+") required value='"+x.passanger_eks[i].trip_fee+"'></div>"

                    html2 +="<div class='col-sm-12 form-group'></div>"    

                    html2 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='responsibility_fee_eks["+i+"]' id='responsibility_fee_eks"+i+"'  onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+") required value='"+x.passanger_eks[i].responsibility_fee+"'></div>"

                    html2 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='insurance_fee_eks["+i+"]' id='insurance_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getData("+"'_eks"+i+"'"+") required value='"+x.passanger_eks[i].insurance_fee+"'></div>"

                    html2 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Harga<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='fare_eks["+i+"]' id='fare_eks"+i+"' onkeypress='return isNumberKey(event)' required readonly value='"+x.passanger_eks[i].fare+"'></div>"

                    html2 +="</div></td></tr>"

                }

                    html2 +="</div></div>"

                $("#fareData2").html(html2);

            }
            else
            {
                $("#fareData2").html(err);
            }

            var html3=""
            if (x.vehicle_reg.length>0)
            {

                html3 +="<div class='portlet light bordered'>"
                    +"<div class='portlet-title'>"
                        +"<div class='caption'>"
                            +"<i class='fa fa-money font-blue-sharp'></i>"
                            +"<span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan "+x.vehicle_reg[0].ship_class_name+"</span>"
                        +"</div>"
                    +"</div>"
                    +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"

                for(var i=0; i<x.vehicle_reg.length; i++)
                {

                    html3 +="<tr><td><div class='row'>"
                        +"<div class='col-sm-2 form-group'>"                                    
                       +"<label>Golongan<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_class_name' id='vehicle_class_name"+i+"' required value='"+x.vehicle_reg[i].vehicle_class_name+"' readonly>"
                            +"<input type='hidden' value='"+x.vehicle_reg[i].vehicle_class_id+"' name='vehicle_class["+i+"]' id='vehicle_class"+i+"' required' >"
                            +"<input type='hidden' value='"+x.vehicle_reg[i].ship_class+"' name='vehicle_ship_class["+i+"]' id='vehicle_ship_class"+i+"' required' >"
                        +"</div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tipe<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_ship_class_name' required value='"+x.vehicle_reg[i].ship_class_name+"' readonly></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tarif Masuk<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_entry_fee["+i+"]' id='vehicle_entry_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+")' required value='"+x.vehicle_reg[i].entry_fee+"'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Jasa Dermaga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_dock_fee["+i+"]' id='vehicle_dock_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+")' required value='"+x.vehicle_reg[i].dock_fee+"'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Ifpro<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_ifpro_fee["+i+"]' id='vehicle_ifpro_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+")' required value='"+x.vehicle_reg[i].ifpro_fee+"'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tarif Jasa<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_trip_fee["+i+"]' id='vehicle_trip_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+")' required value='"+x.vehicle_reg[i].trip_fee+"'></div>"

                    html3 +="<div class='col-sm-12 form-group'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_responsibility_fee["+i+"]' id='vehicle_responsibility_fee"+i+"'  onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle("+i+")' required value='"+x.vehicle_reg[i].responsibility_fee+"'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_insurance_fee["+i+"]' id='vehicle_insurance_fee"+i+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+")'  required value='"+x.vehicle_reg[i].insurance_fee+"'></div>"

                    html3 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Harga<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_fare["+i+"]' id='vehicle_fare"+i+"' onkeypress='return isNumberKey(event)' readonly required value='"+x.vehicle_reg[i].fare+"'></div>"

                    html3 +="</div></td></tr>"

                }

                html3 +="</div></div>"

                $("#fareData3").html(html3);
                
            }
            else
            {
                $("#fareData3").html(err);
            }

            var html4="";
            if (x.vehicle_eks.length>0)
            {

                html4+="<div class='portlet light bordered'>"
                    +"<div class='portlet-title'>"
                        +"<div class='caption'>"
                            +"<i class='fa fa-money font-blue-sharp'></i>"
                            +"<span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan "+x.vehicle_eks[0].ship_class_name+"</span>"
                        +"</div>"
                    +"</div>"
                    +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"


                for(var i=0; i<x.vehicle_eks.length; i++)
                {

                    html4 +="<tr><td><div class='row'>"
                    +"<div class='col-sm-2 form-group'>"                                    
                       +"<label>Golongan<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_class_eks' id='vehicle_class_eks"+i+"' required value='"+x.vehicle_eks[i].vehicle_class_name+"' readonly>"
                            +"<input type='hidden' value='"+x.vehicle_eks[i].vehicle_class_id+"' name='vehicle_class_eks["+i+"]' id='vehicle_class_eks"+i+"' required' >"
                            +"<input type='hidden' value='"+x.vehicle_eks[i].ship_class+"' name='vehicle_ship_class_eks["+i+"]' id='vehicle_ship_class_eks"+i+"' required' >"
                        +"</div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tipe Penumpang<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_ship_class_name_eks' id='vehicle_ship_class_name_eks"+i+"'required value='"+x.vehicle_eks[i].ship_class_name+"' readonly></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tarif Masuk<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_entry_fee_eks["+i+"]' id='vehicle_entry_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].entry_fee+"'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Jasa Dermaga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_dock_fee_eks["+i+"]' id='vehicle_dock_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].dock_fee+"'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Ifpro<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_ifpro_fee_eks["+i+"]' id='vehicle_ifpro_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].ifpro_fee+"'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Tarif Jasa<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_trip_fee_eks["+i+"]' id='vehicle_trip_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].trip_fee+"'></div>"

                    html4 +="<div class='col-sm-12 form-group'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_responsibility_fee_eks["+i+"]'  id='vehicle_responsibility_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].responsibility_fee+"'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_insurance_fee_eks["+i+"]' id='vehicle_insurance_fee_eks"+i+"' onkeypress='return isNumberKey(event)' onKeyup=getDataVehicle("+"'_eks"+i+"'"+") required value='"+x.vehicle_eks[i].insurance_fee+"'></div>"

                    html4 +="<div class='col-sm-2 form-group'>"                                    
                       +"<label>Harga<span class='wajib'>*</span></label>"
                        +"<input type='text' class='form-control' name='vehicle_fare_eks["+i+"]' id='vehicle_fare_eks"+i+"' onkeypress='return isNumberKey(event)' readonly required value='"+x.vehicle_eks[i].fare+"'></div>"

                    html4 +="</div></td></tr>"

                }

                html4 +="</div></div>"

                $("#fareData4").html(html4);
                
            }
            else
            {
                $("#fareData4").html(err);
            }

            if (x === undefined || (x.passanger_reg.length == 0 && x.passanger_eks.length == 0 && x.vehicle_reg.length == 0 && x.passanger_eks.length == 0)) 
            {
                var clearHtml="";
                clearHtml ="<div class='col-sm-12 form-group'><hr></div>"

                clearHtml +="<div class='col-sm-12 form-group' ><div style=' background-color: #ecf4fa; padding:10px; margin:10px 10px; text-align: center; '>Tidak ada tarif dalam rute "+opt+" </div></div>"



                $("#fareInput2").html(clearHtml);
                $("#fareInput").hide();
                $("#fareData1").html("");
                $("#fareData2").html("");
                $("#fareData3").html("");
                $("#fareData4").html("");
            }
            else
            {
                $("#fareInput").show();
                $("#fareInput2").html("");
            }

            
            // console.log(x);


        },
        complete: function(){
            $('#box').unblock(); 
        }
    });
}


$(document).ready(function(){
    $("#discount_schema").on("change",function(){
        getSchema();
    });


});

$('.waktu').datetimepicker({
    // format: 'yyyy-mm-dd hh:ii:ss',
    format: 'hh:ii:ss',
    changeMonth: true,
    changeYear: true,
    autoclose: true,
    // endDate: new Date(),
});
    
</script>