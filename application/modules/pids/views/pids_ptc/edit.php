<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pids/pids_ptc/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="form_control_1">Pelabuhan
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("port",$port,"",' class="form-control select2"  id="port" required disabled') ?>
                                <input type="hidden" value="<?php echo $id; ?>" name="id" id="id" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="form_control_1">Dermaga
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("dock",$dock,"",' class="form-control select2"  id="dock" required disabled') ?>
                            </div>
                        </div>    

                        <div class="col-sm-12"></div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label>Nama Kapal
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("ship_pairing",$ship_pairing,"",' class="form-control select2"  id="ship_pairing" required disabled') ?>
                            </div>
                        </div>         

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label>Kapal Pengganti
                                </label>
                                <?php echo form_dropdown("ship_change",$ship_change,"",' class="form-control select2"  id="ship_change"  ') ?> 
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label>Keterangan
                                    <span class="required" aria-required="true">*</span>
                                </label>
                                <?php echo form_dropdown("description",$description,"",' class="form-control select2"  id="description" required ') ?> 
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
            postData2(url,data);
        });

        function postData2(url,data,y){
            $.ajax({
                url         : url,
                data        : data,
                type        : 'POST',
                dataType    : 'json',

                beforeSend: function(){
                    unBlockUiId('box')
                },

                success: function(json) {
                    if(json.code == 1)
                    {
                        closeModal();
                        toastr.success(json.message, 'Sukses');
                        listDermaga();
                    }
                    else
                    {
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


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>