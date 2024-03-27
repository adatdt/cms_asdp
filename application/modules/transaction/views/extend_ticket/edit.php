<style type="text/css">
    .wajib{ color:red; }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>KODE <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Kode Pelabuhan" required value="<?php echo $row->port_code ?>" disabled>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Nama Pelabuhan <span class="wajib">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pelabuhan" required value="<?php echo $row->name ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Nama Kota <span class="wajib">*</span></label>
                            <input type="text" name="city" class="form-control" placeholder="Nama Kota" required value="<?php echo $row->city ?>">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Profit Center<span class="wajib">*</span></label>
                            <input type="text" name="profit_center" class="form-control" placeholder="Kode Profit Center" value="<?php echo $row->profit_center ?>" required>
                        </div>
                        <div class="col-sm-12"></div>
                        <div class="col-sm-6 form-group">
                            <label>Maximum Berat<span class="wajib">*</span></label>
                            <input type="text" name="weight_limit" class="form-control" placeholder="MAX Berat" required onkeypress="return isNumberKey(event)" value="<?php echo $row->weight_limit; ?>" >
                        </div>                        
                        <div class="col-sm-6 form-group">
                            <label>Event Khusus<span class="wajib">*</span></label>
                            <select class="form-control select2" name="cross_class" required>
                                <option value="f" <?php echo $row->cross_class=='f'?"selected":"" ?> >TIDAK</option>
                                <option value="t" <?php echo $row->cross_class=='t'?"selected":"" ?> >YA</option>
                            </select>
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
