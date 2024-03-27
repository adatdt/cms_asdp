<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/vehicle_inactivated/action_edit', 'id="ff" autocomplete="on"'); 
            
            ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Tanggal Mulai</label>
                            <input type="text" class="form-control date input-medium" id="start_date" name="start_date" value="<?php echo  date("Y-m-d H:i", strtotime($detail->start_date)); ?>" readonly required placeholder="Masukan tanggal mulai">
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Selesai</label>
                            <input type="text" class="form-control date input-medium" id="end_date" name="end_date" value="<?php echo date("Y-m-d H:i", strtotime($detail->end_date)); ?>" readonly required  placeholder="Masukan tanggal selesai">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port_id">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->port_id == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Golongan</label>
                            <select class="form-control select2" required name="vehicle_class_id">
                                <option value="">Pilih</option>
                                <?php foreach($class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->vehicle_class_id == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kelas Layanan</label>
                            <select class="form-control select2" required name="ship_class_id">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->ship_class == $value->id?"selected":""; ?> > <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                    <!--
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
                                    <input type="checkbox" class="allow" name='pos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->pos_vehicle == "t" ?"checked":""; ?> > Pos Kendaraan
                                </label>
                            </div>
                        </div>
                    </div>
                    -->

                    <div class="col-sm-3 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web == "t" ?"checked":""; ?> > Web
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label><input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mobile == "t" ?"checked":""; ?> > Mobile</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input id="b2b" type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->b2b == "t" ?"checked":""; ?> > B2B
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                <div class="col-sm-3 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->ifcs == "t" ?"checked":""; ?> > IFCS
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 form-group">
                        <label></label>
                        <div class="input-group">
                            <div class="icheck">
                                <label>
                                    <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->web_cs == "t" ?"checked":""; ?> > WEB CS
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3" id="div-merchant" style="<?= $detail->b2b == "t"?"":"display:none;" ?>"  >
                            <label>Merchant <span style="color:red">*</span> </label>
                            <div class="pull-right">Pilih Semua <input  type="checkbox" class="allow3" id="selectAllMerchant"  data-checkbox="icheckbox_flat-grey" value="t" ></div>
                                <p></p>                            
                            <div class="input-group">
                                <select class='form-control ' id='getMerchant'  multiple='multiple' name="getMerchant" <?= $detail->b2b == "t"?"required":""; ?> >
                                    
                                    <?php 
                                    foreach ($merchant as $key => $value) {
                                        
                                        $selectedMerchant = empty($dataSelected[$value->merchant_id])?"":"selected";
                                        echo "<option value=".$this->enc->encode($value->merchant_id)." $selectedMerchant >".$value->merchant_name."</option>";
                                    }?>    
                                </select>

                                <input type="hidden" name="merchant" required="" id="merchant" aria-required="true" value="<?= implode(",",$merchantString); ?>" >
                            </div>
                        </div>                                         

                    <!--
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
                                <label>
                                    <input type="checkbox" class="allow" name='mpos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes" <?php echo $detail->mpos_vehicle == "t" ?"checked":""; ?> > MPOS Kendaraan
                                </label>
                            </div>
                        </div>
                    </div>
                    -->

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

        // $('.date').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,           
        // });

        $('#start_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });
        
        $('#end_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
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
                todayHighlight: true,
                // endDate: endDate,
                startDate: startDate
            });

            $('#end_date').val("").datetimepicker("update")
            // myData.reload();
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
            width: '100%',
            formatSelectionCssClass: function (data, container) { return "label label-primary"; },
        }); 

        $(`#b2b`).on('ifChecked ifUnchecked', function(event){
            $("#getMerchant > option").prop("selected", false);
            $("#getMerchant").trigger("change");
            $(`#merchant`).val("")

            if (event.type == `ifChecked`) {
                $(`#div-merchant`).show();
                $(`#getMerchant`).prop('required',true);

            }
            else
            {
                $(`#div-merchant`).hide();
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
        
        $('.allow3').iCheck({
            checkboxClass: 'icheckbox_square-blue service-icheck',
            radioClass: 'icheckbox_square-blue',
        });    
        
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