<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/port/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Nama Pelabuhan</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pelabuhan" required value="<?php echo $row->name ?>">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                        </div>
                        <div class="col-sm-6">
                            <label>Nama Kota</label>
                            <input type="text" name="city" class="form-control" placeholder="Nama Kota" required value="<?php echo $row->city ?>">
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