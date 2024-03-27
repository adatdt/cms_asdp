<div class="col-md-5 col-md-offset-4">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('shift_management/team/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Kode Regu</label>
                            <input type="text" class="form-control" required name="team_code" id="team_code" placeholder="Regu"  value ="<?php echo $detail->team_code ?>" disabled>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Pelabuhan</label>
                            <select class="form-control select2" required name="port">
                                    <option value="">Pilih</option>
                                <?php foreach($port as $port ) { ?>
                                    <option value="<?php echo $this->enc->encode($port->id) ?>" <?php echo $port->id==$detail->port_id?"selected":""; ?> ><?php echo strtoupper($port->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Regu</label>
                            <input type="text" class="form-control" required name="team" id="team" placeholder="Regu" value ="<?php echo $detail->team_name ?>">
                        </div>



                        <input type="hidden" value="<?php echo $this->enc->encode($detail->id); ?>" name="id" class="id" autocomplete="off">

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