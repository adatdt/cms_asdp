<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pedestrian_inactivated/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Mulai <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" id="start_date" name="start_date" readonly placeholder="Masukan tanggal mulai" required>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Selesai <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" id="end_date" name="end_date" readonly placeholder="Masukan tanggal selesai" required>
                        </div>

												<div class="col-sm-3 form-group">
                            <label>Pelabuhan <span style="color:red">*</span></label>
                            <select class="form-control select2" required name="port_id">
                                <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Kelas Layanan <span style="color:red">*</span></label>
                            <select class="form-control select2" required name="ship_class_id">
                                <option value="">Pilih</option>
                                <?php foreach($ship_class as $key=>$value) { ?>
                                <option value="<?php echo $this->enc->encode($value->id) ?>"> <?php echo strtoupper($value->name); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='web' data-checkbox="icheckbox_flat-grey"  value="yes"> Web
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='mobile' data-checkbox="icheckbox_flat-grey"  value="yes"> Mobile
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input id="b2b" type="checkbox" class="allow" name='b2b' data-checkbox="icheckbox_flat-grey"  value="yes"> B2B
                                    </label>
                                </div>
                            </div>
                        </div>

												<div class="col-sm-2">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='ifcs' data-checkbox="icheckbox_flat-grey"  value="yes"> IFCS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label></label>
                            <div class="input-group">
                                <div class="icheck">
                                    <label>
                                        <input type="checkbox" class="allow" name='web_cs' data-checkbox="icheckbox_flat-grey"  value="yes"> WEB CS
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-sm-12"></div> -->
                        <div class="col-sm-3" style="display:none;" id="div-merchant">
                            <p></p>
                            <label>Merchant <span style="color:red">*</span> </label>
                            <div class="pull-right">Pilih Semua <input  type="checkbox" class="allow3" id="selectAllMerchant"  data-checkbox="icheckbox_flat-grey" value="t" ></div>
                                <p></p>

                            <div class="input-group">
                                <select class='form-control ' id='getMerchant'  multiple='multiple' name="getMerchant">
                                    <?php foreach ($merchant as $key => $value) {
                                        echo "<option value=".$this->enc->encode($value->merchant_id).">".$value->merchant_name."</option>";
                                    }?>    
                                </select>

                                <input type="hidden" name="merchant" required="" id="merchant" aria-required="true" value="">
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
        
        $('.date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
						startDate: new Date()
        });

				$("#start_date").change(function() {            
            var startDate = $(this).val();
						console.log(startDate);
            var someDate = new Date(startDate);

						$('#end_date').datetimepicker('remove');
              // Re-int with new options
            $('#end_date').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                startDate: someDate
            });
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
        $('#getMerchant').select2({
            placeholder: "Pilih",
            width: '100%',
            formatSelectionCssClass: function (data, container) { return "label label-primary"; },
        });                      

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        $('.allow3').iCheck({
            checkboxClass: 'icheckbox_square-blue service-icheck',
            radioClass: 'icheckbox_square-blue',
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