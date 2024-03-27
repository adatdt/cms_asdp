<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('master_data/sof_id_finnet/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

						<div class="col-sm-6 form-group">
                            <label>Sof ID (tanpa spasi)<span class="wajib">*</span></label>
                            <input type="text" name="sof_id" class="form-control" placeholder='Sof ID' value="<?php echo $detail->sof_id ?>" required>

														<input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>
						<div class="col-sm-6 form-group">
                            <label>Sof Name<span class="wajib">*</span></label>
                            <input type="text" name="sof_name" class="form-control" placeholder='Sof Name' value="<?php echo $detail->sof_name ?>" required>
                        </div>
                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                                <label>Kategori <span class="wajib">*</span></label>
                            <select id="category" class="form-control js-data-example-ajax select2" dir="" name="category" required>
																<option value="<?php echo $detail->category ?>"><?php echo $detail->category ?></option>
																<option value="DIRECT PAYMENT">DIRECT PAYMENT</option>
																<option value="DOMPET ELEKTRONIK">DOMPET ELEKTRONIK</option>
																<option value="INTERNET BANKING">INTERNET BANKING</option>
																<option value="PAYMENT PAGE">PAYMENT PAGE</option>
																<option value="PENDING PAYMENT">PENDING PAYMENT</option>
																<option value="VIRTUAL ACCOUNT">VIRTUAL ACCOUNT</option>
																<option value="LAINNYA">LAINNYA</option>
														</select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Kode Mitra<span class="wajib">*</span></label>
                            <input type="text" name="mitraCode" class="form-control" placeholder='Kode MMitra' value="<?php echo $detail->mitra_code ?>" required>
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

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    })
</script>