<style type="text/css">
    .wajib{
        color: red;
    }
</style>

<div class="col-md-6 col-md-offset-3">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/mobileVersion/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="form-group">
                        <div class="row">

                        <div class="col-sm-6 form-group">
                            <label>Nama Tipe <span class="wajib">*</span></label>
                            <input type="text" name="type" class="form-control" placeholder="Nama Tipe " required value="<?php echo $detail->type ?>">
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Versi <span class="wajib">*</span></label>
                            <input type="text" name="version" class="form-control" placeholder="Versi" required value="<?php echo $detail->version ?>">
                        </div>

                        <div class="col-sm-12 "></div>

                        <div class="col-sm-6 form-group">
                            <label>Keterangan<span class="wajib">*</span></label>
                            <input type="text" name="description" class="form-control" placeholder="Keterangan" required value="<?php echo $detail->description ?>">

                            <input type="hidden" name="id" required value="<?php echo $this->enc->encode($detail->id) ?>">
                        </div>                   

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
    function get_dock()
    {
        $.ajax({
            type:"post",
            url:"<?php echo site_url()?>pelabuhan/schedule/get_dock",
            data: 'port='+$('#port').val(),
            dataType :"json",
            success:function(x){

                var html="<option value=''>Pilih</option>";

                for(var i=0; i<x.length; i++)
                {
                    html +="<option value='"+x[i].id+"'>"+x[i].name+"</option>";                   
                }

                $("#dock").html(html);
                // console.log(html);
            }
        });
    }
    $(document).ready(function(){
        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        $("#port").on("change",function(){
            get_dock();
        });

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $('.waktu').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

    })
</script>