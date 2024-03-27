 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style>
    .wajib{color: red}
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #eef1f5 !important;
    cursor: no-drop !important;
/*    opacity: 1;*/
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
                            <input type="text" class="form-control" name="schema_code" id="schema_code"  value="<?php echo $discount->schema_code ?>" required readonly>
                        </div>


                        <div class="col-sm-3 form-group">
                            <label>Kode Diskon <span class="wajib">*</span></label>
                            <input type="text" class="form-control " name="discount_code" id="discount_code" value="<?php echo $discount->discount_code ?>" required readonly>

                        </div>


                        <div class="col-sm-3 form-group">
                            <label>Tanggal Awal Berlaku <span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="start_date" id="start_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->start_date ?>">

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir Berlaku<span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="end_date" id="end_date" required placeholder="YYYY-MM-DD HH:II" value="<?php echo $discount->end_date ?>">

                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Nama Promo <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="description" id="description" required placeholder="Nama Promo" value="<?php echo $discount->description ?>">

                        </div>


                        <div class='col-sm-3 form-group'>                                    
                            <label>Jam Awal Berlaku<span class='wajib'>*</span></label>
                            <input type='time' class='form-control ' name='start_time' id='start_time' required value="<?php echo $discount->start_time ?>" step="2">
                        </div>

                       <div class='col-sm-3 form-group'>                                    
                            <label>Jam Akhir Berlaku<span class='wajib'>*</span></label>
                            <input type='time' class='form-control' name='end_time' id='end_time' required value="<?php echo $discount->end_time ?>" step="2" >
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
                                <input type="text" class="form-control " name="port" id="port"  value="<?php echo $value->port_name ?>" required readonly>
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
                                            <li class='nav-item active'>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_passanger'>Tarif  Penumpang Reguler</a>
                                            </li>
                                            <li class='nav-item'>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_passanger_eks'>Tarif Penumpang Eksekutif</a>
                                            </li>

                                            <li class='nav-item'>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_vehicle'>Tarif Kendaraan Reguler</a>
                                            </li>

                                            <li class='nav-item'>
                                                    <a class='label label-primary ' data-toggle='tab' href='#fare_vehicle_eks'>Tarif Kendaraan Eksekutif</a>
                                            </li>                                
                                        </ul>
                      
                                        <div class='tab-content' >
                                            <!-- Fare Penumpang-->
                                            <div class='tab-pane active' id='fare_passanger' role='tabpanel' >
                                                <div class='col-sm-12 form-group' id='fareData1'>
                                                    <?php if(count($pr)>0) { ?>
                                                    <div class='portlet light bordered'>
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Penumpang Reguler</span>
                                                            </div>
                                                        </div>
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                <?php foreach($pr as $key=>$value) { ?>
                                                                <tr><td><div class='row'>
                                                                    <div class='col-sm-2 form-group'><label>Tipe Penumpang<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='passanger_type_name[<?php echo $key ?>]' id='passanger_type_name<?php echo $key?>' required value='<?php echo $value->passanger_type_name ?>' readonly>
                                                                        
                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->passanger_type) ?>' name='passanger_type[<?php echo $key ?>]' id='passanger_type<?php echo $key ?>' required >

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->ship_class) ?>' name='ship_class[<?php echo $key ?>]' id='ship_class<?php echo $key ?>' required >

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->id) ?>' name='id_dis_fare_pass[<?php echo $key ?>]' id='id_dis_fare_pass<?php echo $key ?>' required >


                                                                    </div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Tipe<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='ship_class_name[<?php echo $key ?>]' id='ship_class_name<?php echo $key ?>' required value='<?php echo $value->ship_class_name ?>' readonly></div>


                                                                    <div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='entry_fee[<?php echo $key ?>]' id='entry_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)' required value='<?php echo $value->entry_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='dock_fee[<?php echo $key ?>]' id='dock_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)' required value='<?php echo $value->dock_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='ifpro_fee[<?php echo $key ?>]' id='ifpro_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)'  required value='<?php echo $value->ifpro_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='trip_fee[<?php echo $key ?>]' id='trip_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)' required value='<?php echo $value->trip_fee ?>'></div>

                                                                    <div class='col-sm-12 form-group'></div>    

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='responsibility_fee[<?php echo $key ?>]' id='responsibility_fee<?php echo $key ?>'  onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)' required value='<?php echo $value->responsibility_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='insurance_fee[<?php echo $key ?>]' id='insurance_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData(<?php echo $key ?>)' required value='<?php echo $value->insurance_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Harga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='fare[<?php echo $key ?>]' id='fare<?php echo $key ?>' onkeypress='return isNumberKey(event)' required readonly value='<?php echo $value->fare ?>'></div>

                                                                </div></td></tr>
                                                                <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <?php }  else { echo $err;} ?>
                                                </div>
                                            </div>

                                            <div class='tab-pane' id='fare_passanger_eks' role='tabpanel'>
                                                <div class='col-sm-12 form-group' id='fareData2'>
                                                    <?php if(count($pe)>0) { ?>
                                                    <div class='portlet light bordered'>
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Penumpang Eksekutif</span>
                                                            </div>
                                                        </div>
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                <?php foreach($pe as $key=>$value) { ?>
                                                                <tr><td><div class='row'>
                                                                    <div class='col-sm-2 form-group'><label>Tipe Penumpang<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='passanger_type_name_eks_eks[<?php echo $key ?>]' id='passanger_type_name_eks_eks<?php echo $key?>' required value='<?php echo $value->passanger_type_name ?>' readonly>
                                                                        
                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->passanger_type) ?>' name='passanger_type_eks[<?php echo $key ?>]' id='passanger_type_eks<?php echo $key ?>' required >
                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->ship_class) ?>' name='ship_class_eks[<?php echo $key ?>]' id='ship_class_eks<?php echo $key ?>' required >

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->id) ?>' name='id_dis_fare_pass_eks[<?php echo $key ?>]' id='id_dis_fare_pass_eks<?php echo $key ?>' required >

                                                                    </div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Tipe<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='ship_class_name_eks[<?php echo $key ?>]' id='ship_class_name_eks<?php echo $key ?>' required value='<?php echo $value->ship_class_name ?>' readonly></div>


                                                                    <div class='col-sm-2 form-group'><label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='entry_fee_eks[<?php echo $key ?>]' id='entry_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")' required value='<?php echo $value->entry_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='dock_fee_eks[<?php echo $key ?>]' id='dock_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")' required value='<?php echo $value->dock_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Ifpro<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='ifpro_fee_eks[<?php echo $key ?>]' id='ifpro_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")'  required value='<?php echo $value->ifpro_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'><label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='trip_fee_eks[<?php echo $key ?>]' id='trip_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")' required value='<?php echo $value->trip_fee ?>'></div>

                                                                    <div class='col-sm-12 form-group'></div>    

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='responsibility_fee_eks[<?php echo $key ?>]' id='responsibility_fee_eks<?php echo $key ?>'  onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")' required value='<?php echo $value->responsibility_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='insurance_fee_eks[<?php echo $key ?>]' id='insurance_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getData("_eks<?php echo $key ?>")' required value='<?php echo $value->insurance_fee ?>'></div>

                                                                    <div class='col-sm-2 form-group'>                                    
                                                                        <label>Harga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='fare_eks[<?php echo $key ?>]' id='fare_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' required readonly value='<?php echo $value->fare ?>'></div>

                                                                </div></td></tr>
                                                                <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <?php } else { echo $err;} ?>
                                                </div>

                                            </div>

                                            <div class='tab-pane' id='fare_vehicle' role='tabpanel'>
                                                <div class='col-sm-12 form-group' id='fareData3'>

                                                    <?php if(count($vr)>0) { ?>
                                                    <div class='portlet light bordered'>
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan Reguler</span>
                                                            </div>
                                                        </div>
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                <?php foreach($vr as $key=>$value) { ?>
                                                                <tr><td><div class='row'>
                                                                    <div class='col-sm-2 form-group'>                                    
                                                                   <label>Golongan<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_class_name[<?php echo $key?>]' id='vehicle_class_name<?php echo $key ?>' required value='<?php echo $value->vehicle_class_name ?>' readonly>

                                                                       <input type='hidden' value='<?php echo $this->enc->encode($value->vehicle_class_id) ?>' name='vehicle_class[<?php echo $key ?>]' id='vehicle_class<?php echo $key ?>' required >

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->ship_class) ?>' name='vehicle_ship_class[<?php echo $key ?>]' id='vehicle_ship_class<?php echo $key ?>' required > 

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->id) ?>' name='id_dis_fare_veh[<?php echo $key ?>]' id='id_dis_fare_veh<?php echo $key ?>' required >

                                                                    </div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tipe<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_ship_class_name[<?php echo $key?>]' required value='<?php echo $value->ship_class_name ?>' readonly></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_entry_fee[<?php echo $key ?>]' id='vehicle_entry_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $key ?>)' required value='<?php echo $value->entry_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                       <label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='vehicle_dock_fee[<?php echo $key ?>]' id='vehicle_dock_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $key ?>)' required value='<?php echo $value->dock_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Ifpro<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_ifpro_fee[<?php echo $key ?>]' id='vehicle_ifpro_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $key ?>)' required value='<?php echo $value->ifpro_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_trip_fee[<?php echo $key ?>]' id='vehicle_trip_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $key ?>)' required value='<?php echo $value->trip_fee ?>'></div>

                                                                <div class='col-sm-12 form-group'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_responsibility_fee[<?php echo $key ?>]' id='vehicle_responsibility_fee<?php echo $key ?>'  onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle(<?php echo $key ?>)' required value='<?php echo $value->responsibility_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_insurance_fee[<?php echo $key ?>]' id='vehicle_insurance_fee<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle(<?php echo $key ?>)'  required value='<?php echo $value->insurance_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Harga<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_fare[<?php echo $key ?>]' id='vehicle_fare<?php echo $key ?>' onkeypress='return isNumberKey(event)' readonly required value='<?php echo $value->fare ?>'></div>

                                                                </div></td></tr>
                                                                <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <?php } else { echo $err;} ?>
                                                </div>
                                        
                                            </div>

                                            <div class='tab-pane' id='fare_vehicle_eks' role='tabpanel'>
                                                <div class='col-sm-12 form-group' id='fareData4'>

                                                    <?php if(count($ve)>0) { ?>
                                                    <div class='portlet light bordered'>
                                                        <div class='portlet-title'>
                                                            <div class='caption'>
                                                                <i class='fa fa-money font-blue-sharp'></i>
                                                                <span class='caption-subject font-blue-sharp bold uppercase'>Kendaraan Eksekutif</span>
                                                            </div>
                                                        </div>
                                                        <div class='portlet-body'>
                                                            <table class='table table-hover table-striped table-bordered'>
                                                                <tbody>

                                                                <?php foreach($ve as $key=>$value) { ?>
                                                                <tr><td><div class='row'>
                                                                    <div class='col-sm-2 form-group'>                                    
                                                                   <label>Golongan<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_class_name_eks[<?php echo $key?>]' id='vehicle_class_name<?php echo $key ?>' required value='<?php echo $value->vehicle_class_name ?>' readonly>

                                                                       <input type='hidden' value='<?php echo $this->enc->encode($value->vehicle_class_id) ?>' name='vehicle_class_eks[<?php echo $key ?>]' id='vehicle_class_eks<?php echo $key ?>' required >

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->ship_class) ?>' name='vehicle_ship_class_eks[<?php echo $key ?>]' id='vehicle_ship_class_eks<?php echo $key ?>' required > 

                                                                        <input type='hidden' value='<?php echo $this->enc->encode($value->id) ?>' name='id_dis_fare_veh_eks[<?php echo $key ?>]' id='id_dis_fare_veh_eks<?php echo $key ?>' required >

                                                                    </div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tipe<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_ship_class_name_eks[<?php echo $key ?>]' required value='<?php echo $value->ship_class_name ?>' readonly></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tarif Masuk<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_entry_fee_eks[<?php echo $key ?>]' id='vehicle_entry_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("_eks<?php echo $key ?>")' required value='<?php echo $value->entry_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                       <label>Jasa Dermaga<span class='wajib'>*</span></label>
                                                                        <input type='text' class='form-control' name='vehicle_dock_fee_eks[<?php echo $key ?>]' id='vehicle_dock_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("_eks<?php echo $key ?>")' required value='<?php echo $value->dock_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Ifpro<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_ifpro_fee_eks[<?php echo $key ?>]' id='vehicle_ifpro_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("_eks<?php echo $key ?>")' required value='<?php echo $value->ifpro_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Tarif Jasa<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_trip_fee_eks[<?php echo $key ?>]' id='vehicle_trip_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("_eks<?php echo $key ?>")' required value='<?php echo $value->trip_fee ?>'></div>

                                                                <div class='col-sm-12 form-group'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Biaya Bertanggung Jawab<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_responsibility_fee_eks[<?php echo $key ?>]' id='vehicle_responsibility_fee_eks<?php echo $key ?>'  onkeypress='return isNumberKey(event)'  onKeyup='getDataVehicle("_eks<?php echo $key ?>")' required value='<?php echo $value->responsibility_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Asuransi Jasa Raharja<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_insurance_fee_eks[<?php echo $key ?>]' id='vehicle_insurance_fee_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' onKeyup='getDataVehicle("_eks<?php echo $key ?>")'  required value='<?php echo $value->insurance_fee ?>'></div>

                                                                <div class='col-sm-2 form-group'>                                    
                                                                   <label>Harga<span class='wajib'>*</span></label>
                                                                    <input type='text' class='form-control' name='vehicle_fare_eks[<?php echo $key ?>]' id='vehicle_fare_eks<?php echo $key ?>' onkeypress='return isNumberKey(event)' readonly required value='<?php echo $value->fare ?>'></div>

                                                                </div></td></tr>
                                                                <?php } ?>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <?php } else { echo $err;} ?>
                                                </div>
 
                                            </div>                                

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

        $('.date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

    })
</script>