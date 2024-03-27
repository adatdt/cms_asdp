 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/pcm_trx_global/action_edit_restrict', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">                        


                        <div class="col-sm-6 form-group">
                            <label>Golongan<span class="wajib">*</span></label>
                            <?= form_dropdown("vehicle",$vehicleClass,$selectedVehicleClass,' class="form-control select2"  placeholder="Quota yang digunakan" required disabled') ?>

                            <input type="hidden" required name="shipClass" value="<?= $detail['shipClass'] ?>" />
                            <input type="hidden" required name="vehicleClass" value="<?= $detail['vehicleClass'] ?>" />
                            <input type="hidden" required name="portId" value="<?= $detail['portId'] ?>" />
                            <input type="hidden" required name="departTime" value="<?= $detail['departTime'] ?>" />
                            <input type="hidden" required name="departDate" value="<?= $detail['departDate'] ?>" />
                            <input type="hidden" required name="totalLm" value="<?= $detail['totalLm'] ?>" />
                            <input type="hidden" required name="totalQuota" id="totalQuota" value="<?= $detail['totalQuota'] ?>" />
                            <input type="hidden" required name="usedQuota" value="<?= $detail['usedQuota'] ?>" />
                            <input type="hidden" required name="id" value="<?= $detail['id'] ?>" />

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Quota yang digunakan<span class="wajib">*</span></label>
                            <input type="text"  class="form-control  "  placeholder="Quota yang digunakan" required disabled value='<?= $detail['usedQuota'] ?>' >
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Quota yang Tersedia<span class="wajib">*</span></label>
                            <input type="text" class="form-control "  placeholder="Quota yang Tersedia" required disabled value='<?= $detail['totalQuota'] ?>' >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Total Quota<span class="wajib">*</span></label>
                            <input type="text"  class="form-control "  placeholder="Quota yang Tersedia" required disabled value='<?= $detail['quota'] ?>' >
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6  form-group">
                            <label>Quota<span class="wajib">*</span></label>
                            <input type="text" name="quota" class="form-control" id="quota"  placeholder="Input Quota" required onkeypress="return isNumberKey(event)" autocomplete="off" >
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Aksi<span class="wajib">*</span></label>
                            <?php echo form_dropdown("actions",$action,"", "class='form-control select2' id='actions' required ") ?>
                        </div>
                        
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6  form-group">
                            <label>Estimasi<span class="wajib">*</span></label>
                            <input type="text" name="estimation" class="form-control" id="estimation"  placeholder="Estimasi" required onkeypress="return isNumberKey(event)" readonly="" >
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

    rules   = {quota: {number: true}}
    messages= {quota: {number: "Format Harus Angka"}}

    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData3('pcm_trx_global/action_edit_restrict',data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#actions").change(()=>{
            myData.estimation()
        })

        $("#quota").keyup(()=>{
            myData.estimation()
        })

    })
    function postData3(url,data){

        form = $('form')[0];
        formData = new FormData(form);

        $.ajax({
            url         : url,
            data        :formData,
            type        : 'POST',
            // enctype: 'multipart/form-data',
            processData: false,  // Important!
            contentType: false,
            cache:false,
            dataType    : 'json',

            beforeSend: function(){
                unBlockUiId('box')
            },

            success: function(json) {
                if(json.code == 1){
                    // unblockID('#form_edit');
                    closeModal();
                    toastr.success(json.message, 'Sukses');

                    $(`#detailDataTables_<?= $idTable ?>`).DataTable().ajax.reload( null, false );
                        // ambil_data();


                }else{
                    toastr.error(json.message, 'Gagal');
                }
            },

            error: function() {
                toastr.error('Silahkan Hubungi Administrator', 'Gagal');
            },

            complete: function(){
                $('#box').unblock(); 
            }
        });
    }
</script>