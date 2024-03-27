<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/vehicle_activated/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" name="port">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Golongan</label>
                            <select class="form-control select2"  name="vehicle_class">
                                <option value="">Pilih</option>
                                <?php foreach($class as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Kelas Layanan</label>
                            <select class="form-control select2" required name="ship_class">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey"  value="yes"> Web
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='pos_motor_bike' data-checkbox="icheckbox_flat-grey"  value="yes"> Pos Motor
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='mpos_motor_bike' data-checkbox="icheckbox_flat-grey"  value="yes"> MPOS Motor
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey"  value="yes"> Mobile
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='pos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes"> Pos Kendaraan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='mpos_vehicle' data-checkbox="icheckbox_flat-grey"  value="yes"> MPOS Kendaraan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey"  value="yes"> IFCS
                                    </label>
                                </div>
                            </div>
                        </div>                   

                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input id="b2b" type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey"  value="yes"> B2B
                                    </label>
                                    <p></p>
                                  
                                    <div class="divMerchant">

                                        <div class="pull-right"> Pilih Semua <input  type="checkbox" class="allow" id="selectAllMerchant"  data-checkbox="icheckbox_flat-grey" value="t" ></div>
                                    
                                        <?php 
                                            $option = "";
                                            foreach ($merchant as $key => $value) {
                                                $option .= " <option value='".$key."' id='id_".$key."' >".$value."</option>";
                                            }
                                        ?>

                                        <select class=' form-control ' id='getMerchant' required multiple='multiple' >
                                            <?= $option; ?>
                                        </select>
                                        
                                        <!-- data merchant witch to send  -->
                                        <input type="hidden" name="merchant" required id="merchant">                                
                                        <!-- <input type="hidden" name="isOutlet" id="isOutlet" >  -->
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey"  value="yes"> WEB CS
                                    </label>
                                </div>
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
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var data_input ="<div class='col-md-12 form-group'><a class='remove_field pull-right btn btn-danger' style='margin-bottom:10px;'><i class='fa fa-trash'></i></a><input type='text' class='form-control' placeholder='Langkah Pembayaran'><div>"

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed                
                $(wrapper).append(data_input); 
                x++; 
            }
        });
        
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
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

        $('.divMerchant').hide();

        $(`#b2b`).on('ifChecked ifUnchecked', function(event){

                $("#getMerchant > option").prop("selected", false);
                $("#getMerchant").trigger("change");
                $(`#merchant`).val("")

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