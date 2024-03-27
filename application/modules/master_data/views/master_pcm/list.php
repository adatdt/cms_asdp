
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-10 col-md-offset-1">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">

            <div class="box-body">
                 <div class="form-group">
                    <div class="row">
                        <div class="col-md-12"><b><?php echo $this->session->userdata("rangeDatePcm") ?></b></div>
                    </div>
                </div>


                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="listTable">
                    <thead>
                        <tr>
                            <th>PELABUHAN</th>
                            <th>KELAS <br>LAYANAN</th>
                            <th>TANGGAL <br>KEBERANGKATAN</th>
                            <th>JAM <br>KEBERANGKATAN</th>
                            <th>QUOTA <br>DIINPUT</th>
                            <th>TOTAL QUOTA <br>TERSEDIA</th>
                            <th>QUOTA YANG <br>DI GUNAKAN</th>
                            <th>QUOTA KHUSUS <br>DI RESERVE</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($getListNotUpdated as $key=>$value) { ?>

                        <tr>
                            <th><?php echo $value->port_name ?></th>
                            <th><?php echo $value->ship_class_name ?></th>
                            <th><?php echo $value->depart_date ?></th>
                            <th><?php echo $value->depart_time ?></th>
                            <th><?php echo $value->quota ?></th>
                            <th><?php echo $value->total_quota ?></th>
                            <th><?php echo $value->used_quota ?></th>
                            <th><?php echo $value->quota_reserved?></th>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>                
            </div>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        
        $("#listTable").DataTable();
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: new Date(),
            startDate: new Date()
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });


    })
</script>