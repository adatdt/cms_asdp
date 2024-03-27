<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port/action_add', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        
                        <div class="col-sm-3 form-group">
                            <label>Shift</label>
                            <select name="shift" required class="form-control">\
                                <option value="">--Pilih--</option>
                                <option value="1">Siang</option>
                                <option value="2">Malam</option>    
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Regu</label>
                            <select name="regu" required class="form-control">
                                <option value="">--Pilih--</option>
                                <option value="1">Regu 1</option>
                                <option value="2">Regu 2</option>    
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Nama Pelabuhan</label>
                            <label>Pelabuhan</label>
                            <select name="pelabuhan" required class="form-control">
                                <option value="">--Pilih--</option>
                                <option value="1">Merak</option>
                                <option value="2">Bakaheuni</option>    
                            </select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Open Balance</label>
                            <input type="text" name="open_balance" class="form-control" placeholder="Open Balance" required>
                            <!-- <input type="text" name="city" class="form-control" placeholder="Nama Kota" required> -->
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
    })
</script>