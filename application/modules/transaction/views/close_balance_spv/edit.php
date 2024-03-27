 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('transaction/opening_balance/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Transaksi</label>
                            <input type="text" name="trx_date" value="<?php echo $detail->trx_date ?>" class="form-control" disabled>

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>User Name</label>
                            <input type="text" name="username" class="form-control" value="<?php echo $detail->username ?>" disabled>

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan</label>
                            <input type="text" name="total_cash" class="form-control" value="<?php echo $detail->port_name ?>" disabled>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Regu Dan Kode Penugasan</label>
                            <input type="text" name="assignment_code" class="form-control" value="<?php echo strtoupper($detail->team_name.' - '.$detail->assignment_code) ?>" disabled>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Shift</label>
                            <input type="text" name="shift" class="form-control" value="<?php echo $detail->shift_name ?>" disabled>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Total cash</label>
                            <input type="number" name="total_cash" class="form-control" value="<?php echo $detail->total_cash ?>" required >
                            <input type="hidden" name="id"  value="<?php echo $this->enc->encode($detail->ob_code) ?>">
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