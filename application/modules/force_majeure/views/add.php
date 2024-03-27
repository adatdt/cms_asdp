<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('force_majeure/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Tanggal</label>
                            <input type="text" name="date" id="date" class="form-control" placeholder="Tanggal" autocomplete="off" required />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Keterangan</label>
                            <textarea name="remark" class="form-control" rows="5" placeholder="Keterangan" style="resize: none;" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Simpan') ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true, 
            startDate: new Date(),   
        }).on('changeDate',function(e) {
            $('#date').valid()
        });

        validateForm('#ff',function(url,data){
            postData(url,data);
        });
    })
</script>