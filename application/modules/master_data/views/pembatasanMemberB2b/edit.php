 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }    
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pembatasanMemberB2b/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Kode Pembatasan<span class="wajib">*</span></label> 
                            <input type="text" readonly name="limitTransactionCode" class="form-control" required placeholder="Kode Pembatasan" value="<?= $detail->limit_transaction_code?>" >                        
                            <input type="hidden" readonly name="idDetail"  required  value='<?= $id ?>'> 
                        </div>              

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" id="dateFrom" name="startDate" class="form-control " style="background-color: #ffffff;"  required placeholder="YYYY-MM-DD HH:MM" readonly value="<?= $detail->start_date ?>" >                       
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" id="dateTo" name="endDate" class="form-control "  style="background-color: #ffffff;" required placeholder="YYYY-MM-DD HH:MM" readonly  value="<?= $detail->end_date ?>" >                       
                        </div>
                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-4 form-group">
                            <label>Merchant<span class="wajib">*</span></label> 
                            <?= form_dropdown("merchant",$merchant,$selectedMerchant,'  class="form-control select2" id="merchant" required disabled ' ) ?>                     
                        </div>     
                        
                        
                        <div class="col-sm-4 form-group">
                            <label>Range Waktu Pembatasan<span class="wajib">*</span></label> 
                            <?= form_dropdown("limitType",$limitType,$limitTypeSelected,'  class="form-control select2" id="limitType" required ' ) ?>                     
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Batas Jumlah Trx<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Batas Jumlah Trx" value="<?= $detail->value ?>"  >                     
                        </div>


                        <div class="col-sm-12 form-group"></div>
                        
                        <div class="col-sm-4 form-group ">
                            <div class="input-group">
                             
                                <div class="icheck-inline">                                    

                                    <input type="checkbox" class="allow" id="isCustom" name='isCustom' data-checkbox="icheckbox_flat-grey" value="1" <?=  $detail->custom_type=='t'?"checked":""; ?>>
                                    <label>Custom Range Waktu</label> 
                                    <span id="inputCustomValue">

                                        <?php 
                                            if($detail->custom_type=="t")
                                            {
                                                echo '<input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Waktu yang diinginkan"  value="'.$detail->custom_value.'"> '; 
                                            }
                                        
                                        ?>
                                    </span> 

                                
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
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $('#port').select2({
            tags: false,
            tokenSeparators: [','], 
            placeholder: "Pilih ",
            /* the next 2 lines make sure the user can click away after typing and not lose the new tag */
            selectOnClose: false, 
            closeOnSelect: true
        });
        $("#port").on("change",function(){
            
            $("#port2").val($("#port").val());         
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
        
        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
        });

        $('#dateFrom').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

        $('#dateTo').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "+1m",
            startDate: new Date()
        });
        
        $("#dateFrom").change(function() {            
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            // let endDate=myData.formatDate(someDate);

            // destroy ini firts setting
            $('#dateTo').datetimepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                minuteStep:1,
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                // endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datetimepicker("update")
            // myData.reload();
        });        
        
        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        })
        .on('ifChanged', function(e) {
            // Get the field name
            var isChecked = e.currentTarget.checked;
            let inputDataHtml ="";
            if (isChecked == true) {
                inputDataHtml +=`                    
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Waktu yang diinginkan"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
        });     
    })
</script>