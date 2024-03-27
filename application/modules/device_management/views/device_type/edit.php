<style type="text/css">
    .wajib{
        color: red;
    }
</style>

<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('device_management/device_type/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">

                    <div class="form-group">
                        <div class="row">

                            <div class="col-sm-4 form-group">
                                <label>Nama Tipe Perangkat<span class="wajib">*</span></label>
                                <input type="text" name="device_type" class="form-control" placeholder="Nama Tipe Perangkat" required value="<?php echo $detail->terminal_type_name; ?>">
                            </div>

                            <div class="col-sm-4 form-group">
                                <label>Nama Channel<span class="wajib">*</span></label>
                                <input type="text" name="channel" class="form-control" placeholder="channel" required value="<?php echo $detail->channel; ?>" disabled>
                            </div>

                            <div class="col-sm-4 form-group">
                                <label>Service<span class="wajib">*</span></label>
                                <select id="service" class="form-control  select2 " dir="" name="service">
                                    <option value="">Pilih</option>
                                    <option value="<?php echo $this->enc->encode(0); ?>">UMUM</option>
                                    <?php foreach($service as $key=>$value ) {?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $detail->service_id==$value->id?"selected":"" ?> ><?php echo strtoupper($value->name) ?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="device_type_id" value="<?php echo $this->enc->encode($detail->terminal_type_id); ?>">
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