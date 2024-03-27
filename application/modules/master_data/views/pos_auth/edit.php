 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pos_auth/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Kode Aksi POS <span class="wajib">*</span></label>
                                <?php echo form_dropdown("action_code",$action_pos,$selected_action_pos," class='form-control select2' required "); ?>
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Nama Group <span class="wajib">*</span></label>
                             <?php echo form_dropdown("group",$group,$selected_group," class='form-control select2' required "); ?> 
                        </div>

                        <div class="col-sm-12 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                             <?php echo form_dropdown("port",$port,$selected_port," class='form-control select2' required "); ?> 
                        </div>                                                                   

                        <div class="col-sm-12 form-group">
                            <label>Keterangan</label>
                            <textarea name="description" placeholder="Keterangan" class="form-control" required disabled><?php echo $description; ?></textarea>
                            <input type="hidden" value="<?php echo $id ?>" name="id">
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

        function getDescription()
        {
            $.ajax({
                type : "post",
                url : "<?php echo site_url() ?>master_data/pos_auth/get_description",
                data :"action_code="+$("[name='action_code']").val(),
                dataType: "json",
                beforeSend: function(){
                    unBlockUiId('box')
                },
                success : function(x)
                {
                    // console.log(x);
                    $("[name='description']").val(x);
                },
                error: function() {
                    toastr.error('Silahkan Hubungi Administrator', 'Gagal');
                },

                complete: function(){
                    $('#box').unblock(); 
                }            
            })
        }        

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("[name='action_code']").change(function(){
            getDescription()
        })

    })
</script>