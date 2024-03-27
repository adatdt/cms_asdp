
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('radius/rms_b2b/action_add_detail_outlet', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Merchant<span class="wajib">*</span></label>
                            <?= form_dropdown("merchant",$merchant,""," class=' form-control select2' id='merchant' required " ); ?>     
                        </div>
                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-12 form-group">
                            <label>Outlet ID <span class="wajib">*</span></label>          
                            <input type="text" class="form-control in-group" data-role="tagsinput"  name="outletId" id="outletId" placeholder="Masukan Outlet Id">      
                            <input type="hidden"  name="rmsCode" value="<?= $rmsCode ; ?>" >                                        
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
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script type="text/javascript">
    const rsmCode = `<?= $this->enc->decode($rmsCode) ; ?>`
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData2(url,data);
        });

    function postData2(url, data, y) {
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',

            beforeSend: function () {
                unBlockUiId('box')
            },

            success: function (json) {
                
                $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                // console.log(json)
                let csfrData = {};
                csfrData[json.csrfName] = json.tokenHash;
                $.ajaxSetup({
                        data: csfrData,
                });
                if (json.code == 1) {
                    // unblockID('#form_edit');				
                    closeModal();
                    

                    toastr.success(json.message, 'Sukses');
                    if (y) {
                        $('#grid').treegrid('reload');
                        // ambil_data();
                    }
                    else {
                        $(`#detailDataTables_${rsmCode}`).DataTable().ajax.reload(null, false);
                        // ambil_data();
                    }

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


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>