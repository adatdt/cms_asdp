<style type="text/css">
    .wajib{color: red}
    .select2-container {
        width: unset !important;
    }
</style>

<div class="col-md-4 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('refund/actionKomentarUsaha', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row" style="margin-bottom: 5px">
                        <div class="col-sm-12">
                            <label>Kode Refund</label>
                            <input type="text" value="<?php echo $detail->refund_code?>" name="refund_code" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="upload-bukti">
                            <label>Catatan Revisi<span class="wajib">*</span></label>
                            <input type="text" name="komentar_usaha" id="komentar_usaha" class="form-control" placeholder="Catatan Revisi" required>
                        </div>
                        <input type="hidden" value="<?php echo $id?>" name="id">
                        <input type="hidden" value="<?php echo $detail->booking_code?>" name="booking_code">
                    </div>

                </div>                                                                

            </div>
            <?php echo createBtnForm('Revisi') ?>
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
    });
</script>