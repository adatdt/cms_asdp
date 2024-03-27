
<link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .wajib{color: red}
</style>
<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <button type="button" class="btn btn-warning btn-sm" id="add">Tambah Ticket</button>
            <p></p>
            <p></p>
            <?php echo form_open('transaction/force_majeure/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row" id="row-data">

                        <div class="col-sm-6 form-group">
                            <label>Input Ticket<span class="wajib">*</span></label>
                                <input type="text" name="ticket_number[0]" class="form-control" required placeholder="Nomer Tiket">

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

        var n=0;

        $("#add").on("click",function(){
            n++;


            $("#row-data").append("<div class='col-sm-6 form-group'><label>Input Ticket<span class='wajib'>*</span></label><input type='text' name='ticket_number["+n+"]' class='form-control' required placeholder='Nomer Tiket'></div>");

            console.log(n);

        });
    })
</script>

<?php include "fileJs.php" ?>