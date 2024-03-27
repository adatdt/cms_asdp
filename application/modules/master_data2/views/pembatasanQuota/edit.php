<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}

    .form-control[readonly]{
    background-color: #ffffff;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data2/pembatasanQuota/action_edit', ' id="ff" autocomplete="off" '); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Pelabuhan<span class="wajib">*</span></label>
                                <?= form_dropdown("port", $port, $selectedPort,' class="form-control select2" required id="port" disabled ' ) ?>
                                <input type="hidden" name="id" value="<?= $this->enc->encode($detail->id) ?>" id="id" >
                                <input type="hidden" name="restrictionQuotaCode" value="<?= $this->enc->encode($detail->restriction_quota_code) ?>" id="restrictionQuotaCode" >
                            </div>
                                                                                                                             
                            <div class="form-group">
                                <label>Awal Belaku <span class="wajib">*</span></label>
                                <input type="text" name="dateFrom" class="form-control" id="dateFrom" disabled placeholder="Awal Berlaku" required value='<?= date("Y-m-d",strtotime($detail->start_date)) ?>' >
                            </div>
                            <div class="form-group">
                                <label>Total Quota <span class="wajib">*</span></label>
                                <input type="number" min=1 name="totalQuota" id="totalQuota" class="form-control"  placeholder="Total Quota" required value='<?= $detail->quota ?>' disabled>
                            </div>
                  
                            <div class="form-group">
                            
                                <label>Golongan<span class="wajib">*</span></label>
                                <?= form_dropdown("vehicleClass",$vehicleClass,$selectedVehicleClass,' class="form-control select2" required id="vehicleClass" disabled ' ) ?>
                            
                            </div>

                            <div class=" form-group">
                                <label>Quota<span class="wajib">*</span></label>
                                <input type="text" name="quota" class="form-control" id="quota"  placeholder="Input Quota" required onkeypress="return isNumberKey(event)" autocomplete="off" >
                            </div>

                            <div class="form-group">
                                <label>Estimasi<span class="wajib">*</span></label>
                                <input type="text" name="estimation" class="form-control" id="estimation"  placeholder="Estimasi" required onkeypress="return isNumberKey(event)" readonly="" >
                            </div>
                                                                                                                                                                  
                        </div>

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Layanan <span class="wajib">*</span></label>
                                <?= form_dropdown("shipClass",$shipClass,$selectedShipClass,' class="form-control select2" required id="shipClass" disabled ' ) ?>
                            </div>
                            
                            <div class="form-group">
                                <label>Akhir Berlaku <span class="wajib">*</span></label>
                                <input type="text" name="dateTo" class="form-control date"  id="dateTo" readonly placeholder="Akhir Berlaku" required value='<?= date("Y-m-d",strtotime($detail->end_date))  ?>'  >
                            </div>
                            <div class="form-group">
                                <label>Line Meter <span class="wajib">*</span></label>
                                <input type="number" min=1 name="lineMeter" class="form-control"  placeholder="Line Meter" required value='<?= $detail->total_lm ?>'>
                            </div>
                            <div class="form-group">
                                <label>Jam<span class="wajib">*</span></label>

                                <select class="form-control " required id="jam" multiple="multiple" name="jam"  >
                                    <?php
                                    foreach ($jam as $key => $value) { 
                                        $disabled = date('H:i')>=$value?"disabled":"";
                                        $selected="";
                                        foreach ($detailJam as $key2 => $value2) {
                                            if(date("H:i",strtotime($value2->depart_time)) == $value )
                                            {
                                                $selected="selected";
                                            }
                                        }
                                    ?>
                                    <option <?= $selected; //$disabled ?> value="<?= $value ?>" ><?= $value ?></option>
                                    <?php } ?>

                                </select>
                                <input type="hidden" required name="jam2" id="jam2" value="<?= $selectedJam ?>">

                            </div>

                            <div class="form-group">
                                <label>Aksi<span class="wajib">*</span></label>
                                <?php echo form_dropdown("actions",$action,"", "class='form-control select2' id='actions' required ") ?>
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
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('#jam').select2({
            tags: false,
            tokenSeparators: [','],
            placeholder: "Pilih ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false,
            closeOnSelect: true
        });
        $("#jam").on("change",function(){
            
            $("#jam2").val($("#jam").val());
        })

        $("#actions").change(()=>{
            myData.estimation();
        })

        $("#quota").keyup(()=>{
            myData.estimation();
        })

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // startDate: new Date()
            startDate: `<?= date("Y-m-d",strtotime($detail->end_date)) ?>`
        });
        
        /*
        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                minuteStep:1,
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")
            // myData.reload();
        });
        */

    })
</script>