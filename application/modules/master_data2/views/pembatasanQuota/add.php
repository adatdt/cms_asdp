<style>
.form-control[readonly]{
    background-color: #ffffff;
}
</style>

<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data2/pembatasanQuota/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Pelabuhan<span class="wajib">*</span></label>
                                <?= form_dropdown("port",$port,"",' class="form-control select2" required id="port" ' ) ?>
                            </div>
                                                                                                                             
                            <div class="form-group">
                                <label>Awal Belaku <span class="wajib">*</span></label>
                                <input type="text" name="dateFrom" class="form-control" id="dateFrom" readonly placeholder="Awal Berlaku" required>
                            </div>                   
                            <div class="form-group">
                                <label>Quota <span class="wajib">*</span></label>
                                <input type="number" min=1 name="quota" class="form-control"  placeholder="Quota" required>
                            </div>
                  
                            <div class="form-group">
                            
                                <label>Golongan<span class="wajib">*</span></label>
                                <?= form_dropdown("vehicleClass",$vehicleClass,"",' class="form-control " required id="vehicleClass" multiple="multiple" ' ) ?>
                                <input type="hidden" required name="vehicleClass2" id="vehicleClass2" >
                            
                            </div>                            
                                                                                                                                                                  
                        </div>

                        <div class="col-sm-6 form-group">
                            <div class="form-group">
                                <label>Layanan <span class="wajib">*</span></label>
                                <?= form_dropdown("shipClass",$shipClass,"",' class="form-control select2" required id="shipClass" ' ) ?>
                            </div>                                              
                            
                            <div class="form-group">
                                <label>Akhir Berlaku <span class="wajib">*</span></label>
                                <input type="text" name="dateTo" class="form-control"  id="dateTo" readonly placeholder="Akhir Berlaku" required>
                            </div>
                            <div class="form-group">
                                <label>Line Meter <span class="wajib">*</span></label>
                                <input type="number" min=1 name="lineMeter" class="form-control"  placeholder="Line Meter" required>
                            </div>                                      
                            <div class="form-group">
                                <label>Jam<span class="wajib">*</span></label>

                                <select class="form-control " required id="jam" multiple="multiple" name="jam">
                                    <?php
                                    foreach ($jam as $key => $value) { 
                                        $disabled = date('H:i')>=$value?"disabled":"";
                                    ?>
                                    <option <?= ""; //$disabled?> value="<?= $value ?>" ><?= $value ?></option>
                                    <?php } ?>

                                </select>
                                <input type="hidden" required name="jam2" id="jam2" >
                            </div>


                        </div>                        

                        <div class="col-sm-12"> </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
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

        $('#vehicleClass').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Pilih ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#vehicleClass").on("change",function(){
            
            $("#vehicleClass2").val($("#vehicleClass").val());
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
            startDate: new Date()
        });
        
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
    })
</script>