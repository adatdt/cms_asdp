 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicki@2.0.1/css/timepicki.min.css"> -->
 <link href="<?php echo base_url(); ?>assets/js/TimePicki-master/css/timepicki.css" rel="stylesheet" type="text/css" />

<style>
    .wajib{color: red}
    input.timepicki-input:focus{
    outline: 1px solid #CCCCCC;
    }
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #eef1f5 !important;
    cursor: no-drop !important;
    }
</style>
<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/discount/action_edit1', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row" id="form">
                        <?php  $err="<div style=' background-color: #ecf4fa; padding:10px; margin:10px 10px; text-align: center; '>Tidak ada data</div>"; ?>
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
                            <label>Tanggal Awal Berlaku <span class="wajib">*</span></label>
                            <input type="text" class="form-control start_date" name="start_date" id="start_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->start_date ?>">

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir Berlaku<span class="wajib">*</span></label>
                            <input type="text" class="form-control end_date" name="end_date" id="end_date" required placeholder="YYYY-MM-DD HH:II"  value="<?php echo $discount->end_date ?>">
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Nama Promo <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="description" id="description" required placeholder="Nama Promo" value="<?php echo $discount->description ?>">

                        </div>


                        <div class='col-sm-3 form-group'>                                    
                            <label>Jam Awal Berlaku<span class='wajib'>*</span></label>
                            <input type="text" class='form-control start_time' name='start_time' id='start_time' required value="<?php echo substr($discount->start_time,0,5) ?>" step="2">
                        </div>

                       <div class='col-sm-3 form-group'>                                    
                            <label>Jam Akhir Berlaku<span class='wajib'>*</span></label>
                            <input type="text" class='form-control end_time' name='end_time' id='end_time' required value="<?php echo substr($discount->end_time,0,5) ?>" step="2" >
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

                            <?php foreach ($detail_port as $key=>$value) { ?>

                            <div class="col-sm-3 form-group">
                                <label>Rute<span class="wajib">*</span></label>
                                <select class="form-control select2" name="route" id="route" required disabled>
                                        <option value="<?php echo $this->enc->encode($value->id) ?>" ><?php echo strtoupper($value->route_name) ?></option>
                                </select>
                            </div>

                            <div class="col-sm-3 form-group">                                    
                                <label>Pelabuhan<span class="wajib">*</span></label>
                                <input type="text" class="form-control " name="port" id="port" required readonly value="<?php echo $value->port_name ?>">
                            </div>

                            <?php } ?>

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

                            <div class='col-sm-12 form-group' id='fareInput'>
                                <div class='kt-portlet'>
                                    <div class='kt-portlet__head'>
                                        <div class='kt-portlet__head-label'>
                                            <h3 class='kt-portlet__head-title'></h3>
                                        </div>
                                    </div>
                                    <div class='kt-portlet__body'>

                                        <ul class='nav nav-tabs ' role='tablist'>
                                            <?php foreach($ship_class as $key=>$value) { ?>
                                                <?php if($value->id ==1) { ?>
                                                <li class='nav-item active'>
                                                <?php }else{ ?>
                                                <li class='nav-item '>
                                                <?php } ?>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_passanger<?php echo $value->id ?>'>Tarif  Penumpang <?php echo $value->name ?></a>
                                                </li>
                                            <?php } ?>

                                            <?php foreach($ship_class as $key=>$value) { ?>    
                                                <li class='nav-item '>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_vehicle<?php echo $value->id ?>'>Tarif Kendaraan <?php echo $value->name ?></a>
                                                </li>
                                            <?php } ?>                              
                                        </ul>
                      
                                        <div class='tab-content' >
                                            <!-- Fare Penumpang-->
                                        <?php foreach($ship_class as $key=>$value) { ?>
                                            
                                            <?php if($value->id ==1) { ?>
                                            <div class='tab-pane active' id='fare_passanger<?php echo $value->id ?>' role='tabpanel' >
                                            <?php }else{ ?>
                                            <div class='tab-pane' id='fare_passanger<?php echo $value->id ?>' role='tabpanel' >
                                            <?php } ?>
                                               
                                                <div class='col-sm-12 form-group' id='fareDataPassenger<?php echo $value->id ?>'>
                                                <?php if(count(array_filter($passanger[$key])) > 0 ) { ?>

                                                    <div class='portlet light bordered'>
                                                       
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Penumpang <?php echo $value->name ?></span>
                                                            </div>
                                                        </div>
                                                     
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                    <?php  for ($x = 0; $x < count($passanger[$key]); $x++) { ?>

                                                                        <tr><td><div class='row'>

                                                                            <div class='col-sm-2 form-group'><label>Tipe Penumpang <?php echo $passanger[$key][$x]->passanger_type_name ?><span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='passanger_type_name<?php echo $key?>' id='passanger_type_name<?php echo $key?>' required value='<?php echo $passanger[$key][$x]->passanger_type_name  ?>' readonly>
                                                                                
                                                                                <input type='hidden' value='<?php echo $this->enc->encode($passanger[$key][$x]->passanger_type) ?>' name='passanger_type_<?php echo $value->id.'['.$x.']'?>' id='passanger_type<?php echo $key ?>' required >

                                                                                <input type='hidden' value='<?php echo $this->enc->encode($passanger[$key][$x]->ship_class) ?>' name='ship_class_<?php echo $value->id.'['.$x.']'?>' id='ship_class<?php echo $key ?>' required >

                                                                                 <input type='hidden' value='<?php echo $this->enc->encode($passanger[$key][$x]->id) ?>' name='id_dis_fare_pass_<?php echo $value->id.'['.$x.']'?>' id='id_dis_fare_pass<?php echo $key ?>' required >
                                                            
                                                                            </div>

                                                                            <div class='col-sm-2 form-group'>  

                                                                            <label>Tipe<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='ship_class_name_<?php echo $value->id.'['.$x.']'?>' id='ship_class_name<?php echo $key ?>' required value='<?php echo $passanger[$key][$x]->ship_class_name ?>' readonly></div>


                                                                            <div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='entry_fee_<?php echo $value->id.'['.$x.']'?>' id='entry_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $passanger[$key][$x]->entry_fee ?>'></div>

                                                                            <div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='dock_fee_<?php echo $value->id.'['.$x.']'?>' id='dock_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $passanger[$key][$x]->dock_fee ?>'></div>

                                                                            <div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='ifpro_fee_<?php echo $value->id.'['.$x.']'?>' id='ifpro_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)'  required value='<?php echo $passanger[$key][$x]->ifpro_fee ?>'></div>

                                                                            <div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='trip_fee_<?php echo $value->id.'['.$x.']'?>' id='trip_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $passanger[$key][$x]->trip_fee ?>'></div>

                                                                            <div class='col-sm-12 form-group'></div>    

                                                                            <div class='col-sm-2 form-group'>                                    
                                                                                <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='responsibility_fee_<?php echo $value->id.'['.$x.']'?>' id='responsibility_fee<?php echo $x ?><?php echo $value->id ?>'  onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $passanger[$key][$x]->responsibility_fee ?>'></div>

                                                                            <div class='col-sm-2 form-group'>                                    
                                                                                <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='insurance_fee_<?php echo $value->id.'['.$x.']'?>' id='insurance_fee<?php echo $x?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $passanger[$key][$x]->insurance_fee ?>'></div>

                                                                            <div class='col-sm-2 form-group'>                                    
                                                                                <label>Harga<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='fare_<?php echo $value->id.'['.$x.']'?>' id='fare<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' required readonly value='<?php echo $passanger[$key][$x]->fare ?>'></div>


                                                                        </div></td></tr>

                                                                    <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>


                                                    </div>
                                                <?php }  else { echo $err;} ?>
                                                </div>
                                            </div>
                                        <?php } ?>


                                        <?php foreach($ship_class as $key=>$value) { ?>

                                            <div class='tab-pane' id='fare_vehicle<?php echo $value->id ?>' role='tabpanel'>
                                                        
                                                <div class='col-sm-12 form-group' id='fareData3'>

                                                    <?php if(count(array_filter($vehicle[$key])) > 0 ) { ?>
                                                    <div class='portlet light bordered'>
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan <?php echo $value->name ?></span>
                                                            </div>
                                                        </div>
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                <?php  for ($x = 0; $x < count($vehicle[$key]); $x++) { ?>

                                                                    <tr><td><div class='row'>
                                                                        <div class='col-sm-2 form-group'>                                    
                                                                        <label>Golongan<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='vehicle_class_name<?php echo $key?>' id='vehicle_class_name<?php echo $key ?>' required value='<?php echo $vehicle[$key][$x]->vehicle_class_name ?>' readonly>

                                                                           <input type='hidden' value='<?php echo $this->enc->encode($vehicle[$key][$x]->vehicle_class_id) ?>' name='vehicle_class_<?php echo $value->id.'['.$x.']'?>' id='vehicle_class<?php echo $key ?>' required >

                                                                            <input type='hidden' value='<?php echo $this->enc->encode($vehicle[$key][$x]->ship_class) ?>' name='vehicle_ship_class_<?php echo $value->id.'['.$x.']'?>' id='vehicle_ship_class<?php echo $key ?>' required > 

                                                                            <input type='hidden' value='<?php echo $this->enc->encode($vehicle[$key][$x]->id) ?>' name='id_dis_fare_veh_<?php echo $value->id.'['.$x.']'?>' id='id_dis_fare_veh<?php echo $key ?>' required >

                                                                        </div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Tipe<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_ship_class_name_<?php echo $value->id.'['.$x.']'?>' required value='<?php echo $vehicle[$key][$x]->ship_class_name ?>' readonly></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_entry_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_entry_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $vehicle[$key][$x]->entry_fee ?>'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                               <label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                                <input type='text' class='form-control' name='vehicle_dock_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_dock_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)'  required value='<?php echo $vehicle[$key][$x]->dock_fee ?>'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Ifpro<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_ifpro_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_ifpro_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)' required value='<?php echo $vehicle[$key][$x]->ifpro_fee ?>'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_trip_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_trip_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)'  required value='<?php echo $vehicle[$key][$x]->trip_fee ?>'></div>

                                                                        <div class='col-sm-12 form-group'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_responsibility_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_responsibility_fee<?php echo $x ?><?php echo $value->id ?>'  onkeypress='return isNumberKey(event)'   onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)'  required value='<?php echo $vehicle[$key][$x]->responsibility_fee ?>'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_insurance_fee_<?php echo $value->id.'['.$x.']'?>' id='vehicle_insurance_fee<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle(<?php echo $x ?>,<?php echo $value->id ?>)'  required value='<?php echo $vehicle[$key][$x]->insurance_fee ?>'></div>

                                                                        <div class='col-sm-2 form-group'>                                    
                                                                           <label>Harga<span class='wajib'>*</span></label>
                                                                            <input type='text' class='form-control' name='vehicle_fare_<?php echo $value->id.'['.$x.']'?>' id='vehicle_fare<?php echo $x ?><?php echo $value->id ?>' onkeypress='return isNumberKey(event)' readonly required value='<?php echo $vehicle[$key][$x]->fare ?>'></div>

                                                                    </div></td></tr>

                                                                <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <?php } else { echo $err;} ?>
                                                </div>                                 
                                            </div>
                                        <?php } ?>

                                </div>      
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo createBtnForm('Edit') ?>
        <?php echo form_close(); ?>
        </div>
    </div>
</div>

 <script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/TimePicki-master/js/timepicki.js" type="text/javascript"></script>
<script type="text/javascript">

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

    $(document).ready(function(){

        let time_start=$(".start_time").val();
        let time_end=$(".end_time").val();

        time_start=time_start.split(":");
        time_start.push("AM");

        $('.start_time').timepicki({
            start_time: time_start,
            show_meridian:false,
            min_hour_value:0,
            max_hour_value:23,
            step_size_minutes:1,
            overflow_minutes:true,
            increase_direction:'up',
            // disable_keyboard_mobile: true
        });

        time_end=time_end.split(":");
        time_end.push("AM");

        $('.end_time').timepicki({
            start_time: time_end,
            show_meridian:false,
            min_hour_value:0,
            max_hour_value:23,
            step_size_minutes:1,
            overflow_minutes:true,
            increase_direction:'up',
            // disable_keyboard_mobile: true
        });

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