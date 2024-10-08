<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/schedule/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-md-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="port" id="port">
                                    <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Nama Kapal </label>
                            <select class="form-control select2"  name="ship" id="ship">
                                    <option value="">Pilih</option>
                                <?php foreach($ship as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>"><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Dermaga <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="dock" id="dock">
                                    <option value="">Pilih</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Tanggal Jadwal <span class="wajib">*</span></label>
                                <input type="text" name="schedule" class="form-control date " required placeholder="YYYY-MM-DD">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Tanggal Jam Sandar <span class="wajib">*</span></label>

                                <input type="text" name="docking_on" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Tanggal Jam Buka Boarding <span class="wajib">*</span></label>
                                <input type="text" name="open_boarding" class="form-control waktu" placeholder="YYYY-MM-DD HH:MM" required>
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-md-4 form-group">
                            <label>Jam Tutup Boarding <span class="wajib">*</span></label>
                                <input type="text" name="close_boarding" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" >
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-md-4 form-group">
                            <label>Jam Tutup Ramdoor <span class="wajib">*</span></label>
                                <input type="text" name="close_ramdoor" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-md-4 form-group">
                            <label>Tanggal Keberangkatan <span class="wajib">*</span></label>
                                <input type="text" name="sail_time" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-md-4">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="class" id="class">
                                <option value="">Pilih</option>
                                <?php foreach($tipe_kapal as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="col-md-4 form-group">
                            <label>Trip <span class="wajib">*</span></label>
                            <input type="number" name="trip" class="form-control input-small" required placeholder="99999">

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