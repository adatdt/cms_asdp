 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo base_url(); ?>assets/js/TimePicki-master/css/timepicki.css" rel="stylesheet" type="text/css" />

<style>
    .wajib{color: red}
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #eef1f5 !important;
    cursor: no-drop !important;
    }
</style>
<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/discount/action_edit2', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row" id="form">

                        <div class="col-sm-3 form-group">
                            <label>Schema Discount<span class="wajib">*</span></label>
                            <select class="form-control select2" name="discount_schema" id="discount_schema" required disabled>
                                <?php foreach($discount_schema as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode("$value->schema_code")?>" ><?php echo strtoupper($value->description) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Kode Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="schema_code" id="schema_code" required placeholder="Kode Schema" readonly value="<?php echo $discount->schema_code ?>" disabled>
                        </div>


                        <div class="col-sm-3 form-group">
                            <label>Kode Diskon <span class="wajib">*</span></label>
                            <input type="text" class="form-control " name="discount_code" id="discount_code" required placeholder="Kode Diskon" value="<?php echo $discount->discount_code ?>" readonly>

                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Mulai Berlakunya Konfigurasi <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="reservation_date" id="reservation_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->active_reservation_date ?>">

                        </div>


                        <div class="col-sm-3 form-group">
                            <label>Tanggal Awal Berlaku <span class="wajib">*</span></label>
                            <input type="text" class="form-control start_date" name="start_date" id="start_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->start_date ?>">

                        </div>    

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir Berlaku<span class="wajib">*</span></label>
                            <input type="text" class="form-control end_date" name="end_date" id="end_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->end_date ?>">

                        </div>

                        <div class='col-sm-3 form-group'>                                    
                            <label>Jam Awal Berlaku<span class='wajib'>*</span></label>
                            <input type="time" class='form-control start_time' name='start_time' id='start_time' required value="<?php echo $discount->start_time ?>" step="2">
                        </div>

                       <div class='col-sm-3 form-group'>                                    
                            <label>Jam Akhir Berlaku<span class='wajib'>*</span></label>
                            <input type="time" class='form-control end_time' name='end_time' id='end_time' required value="<?php echo $discount->end_time ?>" step="2" >
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-3 form-group">
                            <label>Nama Promo <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="description" id="description" required placeholder="Nama Promo" value="<?php echo $discount->description ?>">

                        </div>

                        <div class="col-sm-12 " id="get_form"><hr></div>

                        <div class="col-sm-12 form-group"><label>Berlaku<span class="wajib">*</span></label></div>

                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="pos_passanger" id="pos_passanger" <?php echo $discount->pos_passanger=='t'?'checked':''; ?> >
                            POS Penumpang
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="pos_vehicle" id="pos_vehicle" <?php echo $discount->pos_vehicle=='t'?'checked':''; ?>>
                            POS Kendaraan
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="vm" id="vm" <?php echo $discount->vm=='t'?'checked':''; ?>>
                            VM
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="mobile" id="mobile" <?php echo $discount->mobile=='t'?'checked':''; ?>>
                            Mobile
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="web" id="web" <?php echo $discount->web=='t'?'checked':''; ?>>
                            Web
                        </div>
                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="b2b" id="b2b" <?php echo $discount->b2b=='t'?'checked':''; ?>>
                            B2B
                        </div>

                        <div class="col-sm-2 form-group">
                            <input type="checkbox"  class="allow" name="ifcs" id="ifcs" <?php echo $discount->ifcs=='t'?'checked':''; ?>>
                            IFCS
                        </div>                        


                        <div class="col-sm-12 "><hr></div>

                        <div class="col-sm-12 form-group">

                            <div class="col-sm-3 form-group">
                                <label>Rute<span class="wajib">*</span></label>
                                <select class="form-control select2" name="route" id="route" required disabled>
                                        <option value="<?php echo $this->enc->encode($detail_port->id) ?>" ><?php echo strtoupper($detail_port->route_name) ?></option>
                                </select>
                            </div>

                            <div class="col-sm-3 form-group">                                    
                                <label>Pelabuhan<span class="wajib">*</span></label>
                                <input type="text" class="form-control " name="port" id="port" required readonly value="<?php echo $detail_port->port_name ?>">
                            </div>

                            <div class="col-sm-3 form-group">
                                <label>Tipe Kapal<span class="wajib">*</span></label>
                                <select class="form-control select2" name="route" id="route" required disabled>
                                        <option value="<?php echo $this->enc->encode($ship_class_name) ?>" ><?php echo strtoupper($ship_class_name) ?></option>
                                </select>
                            </div>

                            <div class='col-sm-3 form-group'>                                    
                                <label>Potongan Harga<span class='wajib'>*</span></label>
                                <input type='text' class='form-control ' name='value' id='value' required placeholder='Potongan Harga' onkeypress='return isNumberKey(event)' value="<?php echo $detail_value->value ?>">
                            </div>                          

                            <div class='col-sm-12 form-group'> </div>
                            <div class='col-sm-3 form-group'>
                                <label>Tipe Potongan<span class='wajib'>*</span></label>
                                <select class='form-control select2' name='value_type' id='value_type' required >
                                    <option value=''>Pilih</option>
                                    <?php foreach($value_type as $key=>$value ) { ?>
                                        <option value='<?php echo $this->enc->encode($value->id) ?>' <?php echo $detail_value->value_type==$value->id?'selected':''; ?>><?php echo strtoupper($value->name) ?></option>
                                    <?php } ?>
                               </select>
                           </div>  


                            <div class='col-sm-12 form-group' ><hr>
                            </div>

                            <div class='col-sm-12 form-group'><label>Tipe Pembayaran <span class='wajib'>*</span></label></div>

                            <?php foreach($payment_type as $key=>$value) {

                             $array_checked=array();
                            foreach ($detail_discount as $key2 => $value2) {
                                
                                if(trim(strtoupper($value->payment_type))==trim(strtoupper($value2->payment_type)))
                                {
                                    $array_checked[]=1;
                                }
                                else
                                {
                                    $array_checked[]=0;
                                }

                            } 

                            array_sum($array_checked)>0?$checked='checked':$checked='';

                             ?>

                            <div class='col-sm-2 form-group'>
                                <input type='checkbox' value='<?php echo $value->payment_type ?>' class='allow' name='payment_type[<?php echo $key ?>]' id='<?php echo $value->payment_type ?>'  <?php echo $checked ?> ><?php echo $value->payment_type ?> 
                            </div>
                            <?php } ?>


                            <div class="col-sm-12 form-group"></div>  

                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/TimePicki-master/js/timepicki.js" type="text/javascript"></script>
<script type="text/javascript">

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

    $(document).ready(function(){
        var rules = {start_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'},end_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'} }
        
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        // let time_start=$(".start_time").val();
        // let time_end=$(".end_time").val();

        // time_start=time_start.split(":");
        // time_start.push("AM");

        // $('.start_time').timepicki({
        //     start_time: time_start,
        //     show_meridian:false,
        //     min_hour_value:0,
        //     max_hour_value:23,
        //     step_size_minutes:1,
        //     overflow_minutes:true,
        //     increase_direction:'up',
        //     // disable_keyboard_mobile: true
        // });

        // time_end=time_end.split(":");
        // time_end.push("AM");

        // $('.end_time').timepicki({
        //     start_time: time_end,
        //     show_meridian:false,
        //     min_hour_value:0,
        //     max_hour_value:23,
        //     step_size_minutes:1,
        //     overflow_minutes:true,
        //     increase_direction:'up',
        //     // disable_keyboard_mobile: true
        // });

        // $('.date').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     // endDate: new Date(),
        // });

        function getDataNow()
        {
            var d = new Date();
            var month = d.getMonth() +1;
            var day = d.getDate();
            var year = d.getFullYear();

            let getDay="";
            let getMonth="";

            if(day.length>1)
            {
                getDay=day;
            }
            else
            {
                getDay=`0${day}`
            }

            if(month.length>1)
            {
                getMonth=month;
            }
            else
            {
                getMonth=`0${month}`
            }            

            const returnData = `${year}-${getMonth}-${getDay}`;

            return returnData
        }

         $('#reservation_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            secondStep: true,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

       
        $('#start_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            minuteStep:60,
            startDate: getDataNow()
        });

        $('#end_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            minuteStep:60,
            // endDate: "+1m",
           startDate: getDataNow()
        });


        $("#start_date").change(function() {

            var startDate = $(this).val();

            // destroy ini firts setting
            $('#end_date').datetimepicker('remove');

            // Re-int with new options
            $('#end_date').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                 minuteStep:60,
                // endDate: endDate,
                startDate: startDate
            });

            $('#end_date').val(startDate).datetimepicker("update")
        });
    })
</script>