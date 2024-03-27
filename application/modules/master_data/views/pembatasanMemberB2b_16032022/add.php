<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />


<style type="text/css">
    .wajib{
        color: red;
    }
    .datetimepicker-minutes {
      max-height: 200px;
      overflow: auto;
      display:inline-block;
    }


</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pembatasanMemberB2b/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Mulai<span class="wajib">*</span></label> 
                            <input type="text" name="startDate" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text" name="endDate" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Nominal Pembatasan Transaksi<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Pembatasan Transaksi"  >                     
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tipe Pembatasan<span class="wajib">*</span></label> 
                            <?= form_dropdown("limitType",$limitType,'','  class="form-control select2" id="limitType" required ' ) ?>                     
                        </div>
                        <div class="col-sm-12 form-group "></div>
                        <div class="col-sm-3 form-group">
                            <label>Merchant<span class="wajib">*</span></label> 
                            <?= form_dropdown("merchant",$merchant,'','  class="form-control select2" id="merchant" required ' ) ?>                     
                        </div>                        
                        <div class="col-sm-3 form-group ">
                            <div class="input-group">
                             
                                <div class="icheck-inline">                                    

                                    <input type="checkbox" class="allow" id="isCustom" name='isCustom' data-checkbox="icheckbox_flat-grey" value="1">
                                    <label>Custom Jenis Pembatasan</label> 
                                    <span id="inputCustomValue"></span> 

                                
                                </div>
                            </div>
                        </div>  


                    
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

        
        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "<?php echo date('Y-m-d H:i',strtotime('-5 minutes')) ?>",
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
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Nominal Jenis Pembatasan"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
        });     


    


    })
</script>