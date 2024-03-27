<style type="text/css">
    .wajib{
        color: red;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('pelabuhan/schedule/action_edit', 'id="ff" autocomplete="on"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row">

                        <div class="col-sm-4 form-group">
                            <label>Pelabuhan <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="port" id="port">
                                    <option value="">Pilih</option>
                                <?php foreach($port as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->port_id==$value->id?"selected":"" ?>><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Nama Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2"  name="ship" id="ship">
                                    <option value="">Pilih</option>
                                <?php foreach($ship as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->ship_id==$value->id?"selected":"" ?>><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Dermaga <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="dock" id="dock">
                                    <option value="">Pilih</option>

                                    <?php foreach($dock as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id) ?>" <?php echo $detail->dock_id==$value->id?"selected":""; ?>><?php echo strtoupper($value->name); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <label>Tipe Kapal <span class="wajib">*</span></label>
                            <select class="form-control select2" required name="class" id="class">
                                <option value="">Pilih</option>
                                <?php foreach($tipe_kapal as $key=>$value) { ?>
                                    <option value="<?php echo $this->enc->encode($value->id); ?>" <?php echo $value->id==$detail->ship_class?"selected":"" ?> ><?php echo strtoupper($value->name) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Jadwal <span class="wajib">*</span></label>
                                <input type="text" name="schedule" class="form-control date " required placeholder="YYYY-MM-DD" value="<?php echo $detail->schedule_date ?>">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Jam Sandar <span class="wajib">*</span></label>
                                <input type="text" name="docking_on" class="form-control waktu" required placeholder="YYYY-MM-DD HH:MM" value="<?php echo $detail->docking_on ?>">
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->
                        </div>

                        <div class="col-md-12"></div>

                        
                        <div class="col-sm-4 form-group">
                            <label>Tanggal Jam Buka Layanan </label>
                                <input type="text" name="open_boarding" class="form-control waktu" placeholder="YYYY-MM-DD HH:MM"   autocomplete="off" 
                                <?php empty($detail->open_boarding_on)?"":"value='".$detail->open_boarding_on."'"; ?>
                                >
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->
                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Jam Tutup Layanan </label>
                                <input type="text" name="close_boarding" class="form-control waktu"  placeholder="YYYY-MM-DD HH:MM" autocomplete="off"
                                <?php empty($detail->close_boarding_on)?"":"value='".$detail->close_boarding_on."'"; ?>
                                >
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Jam Tutup Ramdoor</label>
                                <input type="text" name="close_ramdoor" class="form-control waktu"  placeholder="YYYY-MM-DD HH:MM" autocomplete="off"
                                <?php empty($detail->close_rampdoor_on)?"":"value='".$detail->close_rampdoor_on."'"; ?>
                                >
                                <!-- <span class="input-group-addon"><i class="icon-calendar"></i></span> -->

                        </div>

                        <div class="col-md-12"></div>

                        <div class="col-sm-4 form-group">
                            <label>Tanggal Keberangkatan</label>

                                <input type="text" name="sail_time" class="form-control waktu"  placeholder="YYYY-MM-DD HH:MM"autocomplete="off" 
                                <?php empty($detail->close_rampdoor_on)?"":"value='".$detail->close_rampdoor_on."'"; ?>
                                >

                        </div>

                        <div class="col-sm-4 form-group">
                            <label>Trip <span class="wajib">*</span></label>
                                <input type="number" name="trip" class="form-control input-small" required placeholder="99" value="<?php echo $detail->trip ?>">

                                <input type="hidden" name="schedule_code" class="form-control input-small" required value="<?php echo $this->enc->encode($detail->schedule_code); ?>">

                        </div>

                    </div>
                </div>
            </div>
            <?php echo createBtnForm('Edit') ?>
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