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
            <?php echo form_open('vaccine_parameter/vaccineParam/action_add_detail_vehicle', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-12 form-group">
                            <label>Kelas kendaraan<span class="wajib">*</span></label>
                            <?= form_dropdown("vehicleClass",$vehicleClass,"",' class="form-control select2 " required id="vehicleClass" ' ) ?>
                            <input type="hidden" required name="idParamVaksin" id="idParamVaksin" value="<?= $idParamVaksin ?>">

                        </div>                

                            <input type="hidden" required name="startDate" id="startDate" value="<?= $paramVaccine->start_date ?>">
                            <input type="hidden" required name="endDate" id="endDate" value="<?= $paramVaccine->end_date ?>">
                            <input type="hidden"  name="vaccineActive" id="vaccineActive" value="<?= $paramVaccine->vaccine_active ?>">
                            <input type="hidden"  name="testVaccineActive" id="testVaccineActive" value="<?= $paramVaccine->test_covid_active ?>">


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