
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/pos_auth/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-12 form-group">
                            <label>Nama Kode Aksi POS <span class="wajib">*</span></label>
                            <?php echo form_dropdown("action_code",$action_pos,""," class='form-control select2' required "); ?>
                        </div>


                        <div class="col-sm-12 form-group">
                            <label>Nama Group <span class="wajib">*</span></label>
                            <?php echo form_dropdown("group",$group,""," class='form-control select2' required "); ?>
                        </div>                        

                        <div class="col-sm-12 form-group">
                            <label>Keterangan</label>
                            <!-- <input type="text" name="description" class="form-control"  placeholder="Keterangan" required> -->
                            <textarea name="description" placeholder="Keterangan" class="form-control" required disabled></textarea>
                        </div>


                        <div class="col-sm-12 form-group">
                            <fieldset>
                                <legend style="font-size: 14px;">Pelabuhan <span class="wajib">*</span></legend> 
                                <?php $idx=0; foreach($port as $key => $value  ) { ?>
                                    <div class="col-sm-4 form-group">
                                        <input type="checkbox" class="allow" name='port[<?php echo $idx ?>]' data-checkbox="icheckbox_flat-grey" value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?> 
                                    </div>
                                <?php $idx++; } ?>
                            </fieldset>
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


    $(document).ready(function(){

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("[name='action_code']").change(function(){
            getDescription()
        })

        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });        

    })
</script>