<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/shift/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-12 form-group">
                            <label>Nama Shift</label>
                            <input type="text" class="form-control" required name="shift" id="shift" placeholder="Nama Shift">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Jam Awal Shift</label>
                            <input type="time" class="form-control" required name="login_shift" id="login_shift" placeholder="00:00">

                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Jam Akhir Shift</label>
                            <input type="time" class="form-control" required name="logout_shift" id="logout_shift" placeholder="00:00">
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <input type="checkbox" class="allow" name='night' data-checkbox="icheckbox_flat-grey">
                                        Apakah beda hari atau lintas hari
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Add') ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.allow').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'icheckbox_square-blue',
        });

        validateForm('#ff',function(url,data){
            postData(url,data);
        });
    })
</script>