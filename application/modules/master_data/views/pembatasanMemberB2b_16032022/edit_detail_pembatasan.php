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
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pembatasanMember/action_edit_detail_pembatasan', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Kode Pembatasan<span class="wajib">*</span></label> 
                            <input type="text" readonly name="limitTransactionCode" class="form-control" required placeholder="Kode Pembatasan" value="<?= $detail->limit_transaction_code?>" >                        
                            <input type="hidden" readonly name="idDetail"  required  value='<?= $id ?>'> 
                        </div>              

                        <div class="col-sm-4 form-group">
                            <label>Email<span class="wajib">*</span></label> 
                            <input readonly type="text" name="email" class="form-control" required placeholder="Email" value="<?= $detail->email?>"  >                        
                        </div>   
                        
                        <div class="col-sm-4 form-group">
                            <label>Pembatasan Transaksi<span class="wajib">*</span></label> 
                            <input type="number" name="value" class="form-control" required placeholder="Pembatasan Transaksi" min="1"  value="<?= $detail->value?>"  >                        
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Tipe Pembatasan<span class="wajib">*</span></label> 
                            <?= form_dropdown("limitType",$limitType,$limitTypeSelected,'  class="form-control select2" id="limitType" required ' ) ?>                     
                        </div>                                                   
                        <div class="col-sm-4 form-group ">
                            <div class="input-group">
                             
                                <div class="icheck-inline">                                    

                                    <input type="checkbox" class="allow" id="isCustom" name='isCustom' data-checkbox="icheckbox_flat-grey" value="1" <?=  $detail->custom_type=="t"?"checked":""; ?>>
                                    <label>Custom</label> 
                                    <span id="inputCustomValue">

                                        <?php 
                                            if($detail->custom_type=="t")
                                            {
                                                echo '<input type="number" name="customValue" class="form-control " min=1 required placeholder="Value Custom"  value="'.$detail->custom_value.'"> '; 
                                            }
                                        
                                        ?>
                                    </span> 

                                
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
            postData2(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
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
                    <input type="number" name="customValue" class="form-control " min=1 required placeholder="Value Custom"  >      

                `
            }

            $("#inputCustomValue").html(inputDataHtml);
        });                 
        
    })


    function postData2(url, data) 
        {
            console.log(data);
            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                dataType: 'json',

                beforeSend: function () {
                    unBlockUiId('box')
                },

                success: function (json) {
                    if (json.code == 1) {
                        // unblockID('#form_edit');
                        closeModal();
                        toastr.success(json.message, 'Sukses');

                            $(`#<?= $idTable ?>`).DataTable().ajax.reload(null, false);
                            // ambil_data();
                    } else {
                        toastr.error(json.message, 'Gagal');
                    }
                },

                error: function () {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function () {
                    $('#box').unblock();
                }
            });


        }       
</script>