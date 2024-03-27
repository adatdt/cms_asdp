<script type="text/javascript">

function getSchema()
{
    $.ajax({
     
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_schema',
        // data:'schema='+$("#discount_schema").val(),
        data :{schema:$("#discount_schema").val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
            },
        dataType:'json',
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x){
            $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                csfrData[x['csrfName']] = x['tokenHash'];
                $.ajaxSetup({
                    data: csfrData
                });

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
                                            +"<?php 
                                             // $i=1;
                                            foreach($ship_class as $key=>$value) { ?>"
                                            
                                                <?php if($value->id ==1) { ?>
                                                    +"<li class='nav-item active'>"
                                                <?php }else{ ?>
                                                    +"<li class='nav-item '>"
                                                <?php } ?>
                                                        +"<a class='label label-primary' data-toggle='tab' href='#fare_passanger<?php echo $value->id ?>'>Tarif Penumpang <?php echo $value->name ?></a>"
                                                    +"</li>"

                                            +"<?php } ?>"

                                            +"<?php foreach($ship_class as $key=>$value) { ?>"
                                                +"<li class='nav-item ' >"
                                                    +"<a class='label label-primary ' data-toggle='tab' href='#fare_vehicle<?php echo $value->id ?>'>Tarif Kendaraan <?php echo $value->name ?></a>"
                                                +"</li>"
                                            +"<?php } ?>"

                                        +"</ul>"
                      
                                        +"<div class='tab-content' >"
                                           
                                            +"<!-- Fare Penumpang-->"
                                            +"<?php 
                                              // $i=1;
                                              foreach($ship_class as $key=>$value) { ?>" 

                                                    <?php if($value->id ==1) { ?>
                                                        +"<div class='tab-pane active' id='fare_passanger<?php echo $value->id ?>' role='tabpanel' >"
                                                    <?php }else{ ?>
                                                        +"<div class='tab-pane ' id='fare_passanger<?php echo $value->id ?>' role='tabpanel' >"
                                                    <?php } ?>
                                                           
                                                            +"<div class='col-sm-12 form-group' id='fareDataPassenger<?php echo $value->id ?>'></div>"
                                                        
                                                        +"</div>"

                                            +"<?php } ?>" 
                                            
                                            +"<!-- Fare Kendaraan-->"
                                            +"<?php foreach($ship_class as $key=>$value) { ?>"                  
                                                +"<div class='tab-pane ' id='fare_vehicle<?php echo $value->id ?>' role='tabpanel' >"
                                                    +"<div class='col-sm-12 form-group' id='fareDataVehicle<?php echo $value->id ?>'></div>"
                                                +"</div>"
                                            +"<?php } ?>"                            

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
        },
        "fnDrawCallback": function(allRow) 
        {
            let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
            let getToken = allRow.json[getTokenName];
            csfrData[getTokenName] = getToken;
            $.ajaxSetup({
                data: csfrData
            });
        }
    });
}

function getData(param,ship_class)
{

    if( $("#entry_fee"+param+ship_class).val()=='')
    {
        var entry_fee=0;

    }
    else
    {
        var entry_fee=parseInt($("#entry_fee"+param+ship_class).val());            
    }

    if($("#dock_fee"+param+ship_class).val()=='')
    {
        var dock_fee=0;
    }
    else
    {
        var dock_fee=parseInt($("#dock_fee"+param+ship_class).val());      
    }

    if($("#ifpro_fee"+param+ship_class).val()=='')
    {
        var ifpro =0;   
    }
    else
    {
        var ifpro = parseInt($("#ifpro_fee"+param+ship_class).val());     
    }

    if($("#trip_fee"+param+ship_class).val()=='')
    {
        var trip_fee=0;
    }
    else
    {
        var trip_fee = parseInt($("#trip_fee"+param+ship_class).val());    
    }

    if($("#insurance_fee"+param+ship_class).val()==null )
    {
        var insurance_fee=0;
    }
    else
    {
        var insurance_fee = parseInt($("#insurance_fee"+param+ship_class).val());    
    }

    if($("#responsibility_fee"+param+ship_class).val()=='')
    {
        var responsibility_fee=0;
    }
    else
    {
        var responsibility_fee = parseInt($("#responsibility_fee"+param+ship_class).val());    
    }


    harga=entry_fee+dock_fee+ifpro+responsibility_fee+insurance_fee+trip_fee;

    $("#fare"+param+ship_class).val(harga);

    // console.log(harga);

    

}

function getDataVehicle(param,ship_class)
{
    if( $("#vehicle_entry_fee"+param+ship_class).val()=='')
    {
        var entry_fee=0;

    }
    else
    {
        var entry_fee=parseInt($("#vehicle_entry_fee"+param+ship_class).val());            
    }

    if($("#vehicle_dock_fee"+param+ship_class).val()=='')
    {
        var dock_fee=0;
    }
    else
    {
        var dock_fee=parseInt($("#vehicle_dock_fee"+param+ship_class).val());      
    }

    if($("#vehicle_ifpro_fee"+param+ship_class).val()=='')
    {
        var ifpro =0;   
    }
    else
    {
        var ifpro = parseInt($("#vehicle_ifpro_fee"+param+ship_class).val());     
    }

    if($("#vehicle_trip_fee"+param+ship_class).val()=='')
    {
        var trip_fee=0;
    }
    else
    {
        var trip_fee = parseInt($("#vehicle_trip_fee"+param+ship_class).val());    
    }

    if($("#vehicle_responsibility_fee"+param+ship_class).val()=='')
    {
        var responsibility_fee=0;
    }
    else
    {
        var responsibility_fee = parseInt($("#vehicle_responsibility_fee"+param+ship_class).val());    
    }


    if($("#vehicle_insurance_fee"+param+ship_class).val()=='')
    {
        var insurance_fee=0;
    }
    else
    {
        var insurance_fee=parseInt($("#vehicle_insurance_fee"+param+ship_class).val()); 
    }

    harga=entry_fee+dock_fee+ifpro+responsibility_fee+insurance_fee+trip_fee;

    $("#vehicle_fare"+param+ship_class).val(harga);

    // console.log(harga);

}


function getPort()
{
    $.ajax({
        dataType:'json',
        type:'post',
        url:'<?php echo site_url()?>fare/discount/get_port',
        data :{<?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
            },
        // beforeSend:function(){
        //     unBlockUiId('box')
        // },
        success:function(x){
            $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                csfrData[x['csrfName']] = x['tokenHash'];
                $.ajaxSetup({
                    data: csfrData
                });
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
        data :{route:$("#route").val(),
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),
            },
        beforeSend:function(){
            unBlockUiId('box')
        },
        success:function(x){

            $("input[name=" + x.csrfName + "]").val(x.tokenHash);
                csfrData[x['csrfName']] = x['tokenHash'];
                $.ajaxSetup({
                    data: csfrData
                });

            // console.log(x)
            // let key = x.passanger.length;
            
            // Mendapatkan nama pelabuhan dari option yang dipilih
            var rt = document.getElementById('route');
            var opt= rt.options[rt.selectedIndex].text;
            var splitPort=opt.split("-")

            $("#port").val(splitPort[0]);

            var err="<div style=' background-color: #ecf4fa; padding:10px; margin:10px 10px; text-align: center; '>Tidak ada data</div>";
        
           let dataPassenger =0;
           let dataVehicle   =0;

            while(dataPassenger < x.passanger.length) {
                
                var html="";           
                if (x.passanger[dataPassenger].length >0)
                {
                      html ="<div class='portlet light bordered'>"
                        +"<div class='portlet-title'>"
                            +"<div class='caption'>"
                                +"<i class='fa fa-money font-blue-sharp'></i>"

                                +"<span class='caption-subject font-blue-sharp bold uppercase'>Penumpang "+x.passanger[dataPassenger][0].ship_class_name+"</span>"
                            +"</div>"
                        +"</div>"
                        +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"

                    for(var i=0; i<x.passanger[dataPassenger].length; i++)
                    {

                        html +="<tr><td><div class='row'>"
                            +"<div class='col-sm-2 form-group'><label>Tipe Penumpang<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='passanger_type_name["+i+"]' id='passanger_type_name"+i+"' required value='"+x.passanger[dataPassenger][i].passanger_type_name+"' readonly>"
                                +"<input type='hidden' value='"+x.passanger[dataPassenger][i].passanger_type+"' name='passanger_type_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='passanger_type"+i+"' required' >"
                                +"<input type='hidden' value='"+x.passanger[dataPassenger][i].ship_class+"' name='ship_class_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='ship_class"+i+"' required' >"
                            +"</div>"

                        html +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Tipe<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='ship_class_name["+i+"]' id='ship_class_name"+i+"' required value='"+x.passanger[dataPassenger][i].ship_class_name+"' readonly></div>"


                        html +="<div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='entry_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='entry_fee"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")' required value='"+x.passanger[dataPassenger][i].entry_fee+"'></div>"

                        html +="<div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>"
                                +"<input type='text' class='form-control' name='dock_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='dock_fee"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")' required value='"+x.passanger[dataPassenger][i].dock_fee+"'></div>"

                        html +="<div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='ifpro_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='ifpro_fee"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")'  required value='"+x.passanger[dataPassenger][i].ifpro_fee+"'></div>"

                        html +="<div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='trip_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='trip_fee"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")' required value='"+x.passanger[dataPassenger][i].trip_fee+"'></div>"

                        html +="<div class='col-sm-12 form-group'></div>"    

                        html +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='responsibility_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='responsibility_fee"+i+x.passanger[dataPassenger][i].ship_class+"'  onkeypress='return isNumberKey(event)' onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")'required value='"+x.passanger[dataPassenger][i].responsibility_fee+"'></div>"

                        html +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='insurance_fee_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='insurance_fee"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)'onKeyup='getData("+i+','+x.passanger[dataPassenger][i].ship_class+")' required value='"+x.passanger[dataPassenger][i].insurance_fee+"'></div>"

                        html +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Harga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='fare_"+x.passanger[dataPassenger][i].ship_class+"["+i+"]' id='fare"+i+x.passanger[dataPassenger][i].ship_class+"' onkeypress='return isNumberKey(event)' required readonly value='"+x.passanger[dataPassenger][i].fare+"'></div>"

                        html +="</div></td></tr>"

                    }

                    html  +="</div></div>"

                    let parentPassenger = dataPassenger + 1 ;

                    $(`#fareDataPassenger${parentPassenger}`).html(html);

                }
                else
                {
                    let parentPassenger = dataPassenger + 1 ;
                    $(`#fareDataPassenger${parentPassenger}`).html(err);               
                }

                // console.log(`#fareDataPassenger${parentPassenger}`)

            dataPassenger++;
            }


            while(dataVehicle < x.passanger.length ) {

                var html2="";
                if (x.vehicle[dataVehicle].length > 0)
                {

                    html2 +="<div class='portlet light bordered'>"
                        +"<div class='portlet-title'>"
                            +"<div class='caption'>"
                                +"<i class='fa fa-money font-blue-sharp'></i>"
                                +"<span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan "+x.vehicle[dataVehicle][0].ship_class_name+"</span>"
                            +"</div>"
                        +"</div>"
                        +"<div class='portlet-body'><table class='table table-hover table-striped table-bordered'><tbody>"

                    for(var i=0; i<x.vehicle[dataVehicle].length; i++)
                    {

                        html2 +="<tr><td><div class='row'>"
                            +"<div class='col-sm-2 form-group'>"                                    
                           +"<label>Golongan<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_class_name' id='vehicle_class_name"+i+"' required value='"+x.vehicle[dataVehicle][i].vehicle_class_name+"' readonly>"
                                +"<input type='hidden' value='"+x.vehicle[dataVehicle][i].vehicle_class_id+"' name='vehicle_class_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_class"+i+"' required' >"
                                +"<input type='hidden' value='"+x.vehicle[dataVehicle][i].ship_class+"' name='vehicle_ship_class_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_ship_class"+i+"' required' >"
                            +"</div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Tipe<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_ship_class_name' required value='"+x.vehicle[dataVehicle][i].ship_class_name+"' readonly></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Tarif Masuk<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_entry_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_entry_fee"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")' required value='"+x.vehicle[dataVehicle][i].entry_fee+"'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                               +"<label>Jasa Dermaga<span class='wajib'>*</span></label>"
                                +"<input type='text' class='form-control' name='vehicle_dock_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_dock_fee"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")' required value='"+x.vehicle[dataVehicle][i].dock_fee+"'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Ifpro<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_ifpro_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_ifpro_fee"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")' required value='"+x.vehicle[dataVehicle][i].ifpro_fee+"'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Tarif Jasa<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_trip_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_trip_fee"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")' required value='"+x.vehicle[dataVehicle][i].trip_fee+"'></div>"

                        html2 +="<div class='col-sm-12 form-group'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_responsibility_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_responsibility_fee"+i+x.vehicle[dataVehicle][i].ship_class+"'  onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")' required value='"+x.vehicle[dataVehicle][i].responsibility_fee+"'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_insurance_fee_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_insurance_fee"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("+i+','+x.vehicle[dataVehicle][i].ship_class+")'  required value='"+x.vehicle[dataVehicle][i].insurance_fee+"'></div>"

                        html2 +="<div class='col-sm-2 form-group'>"                                    
                           +"<label>Harga<span class='wajib'>*</span></label>"
                            +"<input type='text' class='form-control' name='vehicle_fare_"+x.vehicle[dataVehicle][i].ship_class+"["+i+"]' id='vehicle_fare"+i+x.vehicle[dataVehicle][i].ship_class+"' onkeypress='return isNumberKey(event)' readonly required value='"+x.vehicle[dataVehicle][i].fare+"'></div>"

                        html2 +="</div></td></tr>"

                    }

                    html2 +="</div></div>"

                    let parentVehicle = dataVehicle + 1 ;
                    $(`#fareDataVehicle${parentVehicle}`).html(html2);
                    
                }
                else
                {
                    let parentVehicle = dataVehicle + 1 ;
                    $(`#fareDataVehicle${parentVehicle}`).html(err);
                }

            dataVehicle++;
            }

            // if (x === undefined || (x.passanger[1].length == 0 && x.passanger_eks.length == 0 && x.vehicle_reg.length == 0 && x.passanger_eks.length == 0)) 

            if (x === undefined  ) 
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
        },
        "fnDrawCallback": function(allRow) 
        {
            let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
            let getToken = allRow.json[getTokenName];
            csfrData[getTokenName] = getToken;
            $.ajaxSetup({
                data: csfrData
            });
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