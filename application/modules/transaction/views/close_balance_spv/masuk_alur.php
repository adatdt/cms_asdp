 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('fare/route/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan Asal</label>
                            <select class="form-control select2" name="ship" required="">
                                <option value="">Pilih</option>
                                <?php foreach($ship as $key=>$value) {?>
                                <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Kode Jadwal</label>
                            <input type="text" class="form-control" name="code" value="<?php echo $schedule_code ?>">
                        </div>

                        <div class="col-sm-12 form-group"></div>

                        <div class="col-sm-2 form-group">

                                <label>Jam</label>
                                <button  class="btn btn-warning form-control"  style="background-color: #F1C40F" >Masuk Alur</button>
                        </div>

                        <div class="col-sm-2 form-group">

                                <label>Jam</label>
                                <button  class="btn btn-warning form-control"  style="background-color: #F1C40F" >sandar</button>
                        </div>

                        <div class="col-sm-2 form-group">

                                <label>Jam</label>
                                <button type="button" class="btn btn-warning form-control"  style="background-color: #F1C40F">Mulai Pelayanan</button>
                        </div>

                        <div class="col-sm-2 form-group">
                                <label>Jam</label>
                                <button type="button" class="btn red form-control">Selesai Pelayanan</button>
                        </div>

                        <div class="col-sm-2 form-group">
                                <label>Jam</label>
                                <button type="button" class="btn btn-default form-control" >Tutup Ramdor</button>
                        </div>

                        <div class="col-sm-2 form-group">
                                <label>Jam</label>
                                <button type="button" class="btn btn-default form-control" >Layar</button>
                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Update') ?>
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