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
                            <input type="text" name="startDate"  style="background-color: #ffffff;" class="form-control " id="dateFrom" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir<span class="wajib">*</span></label> 
                            <input type="text"  style="background-color: #ffffff;"  name="endDate" class="form-control " id="dateTo" required placeholder="YYYY-MM-DD HH:MM" readonly >                       
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Batas Jumlah Trx<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control " min=1 required placeholder="Batas Jumlah Trx"  >                     
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Range Waktu Pembatasan<span class="wajib">*</span></label> 
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
                                    <label>Custom Range Waktu</label> 
                                    <span id="inputCustomValue"></span> 

                                
                                </div>
                            </div>

                            <input type="hidden" id="idData" name="idData" value="1">
                            <div id="inputExceptUserDiv"></div>
                        </div>  
                        <?php echo form_close(); ?> 
                        <div class="col-sm-12 form-group "></div>

                        <div class="col-sm-6 form-group " id="divPembatasan"></div>
                        <div class="col-sm-6 form-group " id="divPengecualian"></div>                          

                        <div class="col-sm-12 form-group "></div>


                    
                    </div>
                </div>
            </div>
            <?php //echo form_close(); ?> 
        <div class="box-footer text-right">
            <button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Batal</button> 
            <button type="button" class="btn btn-sm btn-primary" id="saveBtn"><i class="fa fa-check"></i> Simpan</button>
        </div>            
        
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $("#saveBtn").on("click", function(){
            $('#ff').submit()
        })

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
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Custom Waktu Yang Diinginkan"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
        });     


        $("#merchant").on("change",function(){
            
            let getMercantId=$(this).val();
            myData.getUserOutlet(getMercantId)
        })


    


    })
</script>