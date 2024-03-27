<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/vehicle_activated/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                     <div class="col-sm-4 form-group">
                        <label>Pelabuhan</label>
                        <select class="form-control select2" required name="port">
                            <option value="">Pilih</option>
                            <?php foreach($port as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->port_id == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                            <?php } ?>
                        </select>
                    </div>

                     <div class="col-sm-4 form-group">
                        <label>Golongan</label>
                        <select class="form-control select2" required name="vehicle_class">
                            <option value="">Pilih</option>
                            <?php foreach($class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->vehicle_class == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Kelas Layanan</label>
                        <select class="form-control select2" required name="ship_class">
                            <option value="">Pilih</option>
                            <?php foreach($ship_class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->ship_class == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web == "t" ?"checked":""; ?> > Web
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='pos_motor_bike' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->pos_motor_bike == "t" ?"checked":""; ?> > Pos Motor
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='mpos_motor_bike' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mpos_motor_bike == "t" ?"checked":""; ?> > MPOS Motor
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label><input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mobile == "t" ?"checked":""; ?> > Mobile</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='pos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->pos_vehicle == "t" ?"checked":""; ?> > Pos Kendaraan
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='mpos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mpos_vehicle == "t" ?"checked":""; ?> > MPOS Kendaraan
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->ifcs == "t" ?"checked":""; ?> > IFCS
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input id="b2b" type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->b2b == "t" ?"checked":""; ?> > B2B
                                </label>
                                <p></p>
                                <div class="divMerchant">
                                    <div class="pull-right"> Pilih Semua <input  type="checkbox" class="allow" id="selectAllMerchant"  data-checkbox="icheckbox_flat-grey" value="t" ></div>
                                        <select class='form-control ' id='getMerchant'  multiple='multiple' name="getMerchant" <?= $detail->b2b == "t"?"required":""; ?> >
                                            
                                            <?php 
                                            foreach ($merchant as $key => $value) {
                                                
                                                $selectedMerchant = empty($dataSelected[$value->merchant_id])?"":"selected";
                                                echo "<option value=".$this->enc->encode($value->merchant_id)." $selectedMerchant >".$value->merchant_name."</option>";
                                            }?>    
                                        </select>

                                    <input type="hidden" name="merchant" required="" id="merchant" aria-required="true" value="<?= implode(",",$selectMerchant); ?>" >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web_cs == "t" ?"checked":""; ?> > WEB CS
                                </label>
                            </div>
                        </div>
                    </div>                    

                    <input type="hidden" name="id" value="<?=$this->enc->encode($detail->id) ?>">
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $('#getMerchant').select2({
            placeholder: "Pilih",
            formatSelectionCssClass: function (data, container) { return "label label-primary"; },
        }); 

        // $('.divMerchant').hide();

        $(`#b2b`).on('ifChecked ifUnchecked', function(event){

                $("#getMerchant > option").prop("selected", false);
                $("#getMerchant").trigger("change");
                $(`#merchant`).val("")
                console.log(event.type)
                if (event.type == `ifChecked`) {
                    $(`.divMerchant`).show();
                    $(`#getMerchant`).prop('required',true);

                }
                else
                {
                    $(`.divMerchant`).hide();
                    $(`#getMerchant`).prop('required',false);
                }
            })  
        
        $("#getMerchant").on("select2:select select2:unselect", function (e) {
            var data = e.params.data;
            let merchantData = $(this).val();

            $(`#merchant`).val("")
            if(merchantData != null)
            {
                $(`#merchant`).val(merchantData.toString())
            }
        })
        
        $(`#selectAllMerchant`).on('ifChecked ifUnchecked', function(event){
            if (event.type == `ifChecked`) {
                $("#getMerchant > option").prop("selected", true);
                $("#getMerchant").trigger("change");     
                
                //this returns all the selected item
                let items= $(`#getMerchant`).val();       
                let valueData =""
                if(items != null )
                {
                    valueData = items.toString()
                }                
                $("#merchant").val(valueData)                
            }
            else
            {
                $("#getMerchant > option").prop("selected", false);
                $("#getMerchant").trigger("change");  
                $("#merchant").val("")
            }   
        })    
        

    })
</script>