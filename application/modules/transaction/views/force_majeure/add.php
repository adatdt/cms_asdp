
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/force_majeure/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select name="port" class="form-control select2" required>
                                <option value=''>Pilih</option>
                                <?php foreach($port as $key=>$value){ ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php }  ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Tanggal <span class="wajib">*</span></label>
                            <div class="input-group">
                                <input type="text" name="date"  class="form-control date" id="date" readonly placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d')?>" required>
                                <div class="input-group-addon"><i class="icon-calendar"></i></div>
                            </div>

                        </div>

                        <div class="col-sm-12 form-group"></div>
                        <div class="col-sm-6 form-group">
                            <label>Tipe Force Majeure <span class="wajib">*</span></label>
                                <select class="form-control select2" name="force_type" required>
                                    <option value="">Pilih</option>
                                    <option value="<?php echo $this->enc->encode(1); ?>">Eksekutif</option>
                                    <option value="<?php echo $this->enc->encode(2); ?>">General</option>
                                </select>

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Waktu Perpanjangan /Jam <span class="wajib">*</span></label>
                            <input type="text" name="extend" class="form-control" placeholder="Waktu Perjam" onkeypress="return isNumberKey(event)"  required>

                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-6 form-group">
                            <label>Keterangan <span class="wajib">*</span></label>
                                <textarea name="remark" class="form-control" placeholder="Keterangan" required></textarea>

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
            postData(url,data);
        });
    })
</script>

<?php include "fileJs.php" ?>