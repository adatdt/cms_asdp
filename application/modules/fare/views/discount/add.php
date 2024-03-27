 <link href="<?php echo base_url(); ?>assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
 <!-- <link href="<?php echo base_url(); ?>assets/js/TimePicki-master/css/timepicki.css" rel="stylesheet" type="text/css" /> -->
     <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet"> -->
    

<style type="text/css">
    .wajib{
        color:red;
    }

</style>

<div class="col-md-12 col-md-offset-0">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">

            <?php echo form_open('fare/discount/action_add', 'id="ff" autocomplete="off"'); ?>
            <div class="box-body">
                 <div class="form-group">
                    <div class="row" id="form">

                        <div class="col-sm-3 form-group">
                            <label>Schema Discount<span class="wajib">*</span></label>
                            <select class="form-control select2" name="discount_schema" id="discount_schema" required >
                                <option value="null">Pilih</option>
                                <?php foreach($discount_schema as $key=>$value ) { ?>
                                    <option value="<?php echo $this->enc->encode("$value->schema_code")?>" ><?php echo strtoupper($value->description) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Kode Schema <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="schema_code" id="schema_code" required placeholder="Kode Schema" readonly>
                        </div>

                        <div class='col-sm-3 form-group'>                                    
                            <label>Mulai Berlakunya Konfigurasi<span class='wajib'>*</span></label>
                            <input type="text" class='form-control date' name='reservation_date' id='reservation_date' required placeholder="YYYY-MM-DD HH:II">
                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Tanggal Awal Berlaku <span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="start_date" id="start_date" required placeholder="YYYY-MM-DD HH:II">

                        </div>

                        <div class="col-sm-12 form-group"></div>
                        
                        <div class="col-sm-3 form-group">
                            <label>Tanggal Akhir Berlaku<span class="wajib">*</span></label>
                            <input type="text" class="form-control date" name="end_date" id="end_date" required placeholder="YYYY-MM-DD HH:II">

                        </div>

                        <div class="col-sm-3 form-group">
                            <label>Nama Promo <span class="wajib">*</span></label>
                            <input type="text" class="form-control" name="description" id="description" required placeholder="Nama Promo">

                        </div>


                        <div class='col-sm-3 form-group'>                                    
                            <label>Jam Awal Berlaku<span class='wajib'>*</span></label>
                            <input type="time" class='form-control start_time' name='start_time' id='start_time' required value="<?php echo substr('00:00',0,5) ?>" step="2">
                        </div>

                       <div class='col-sm-3 form-group'>                                    
                            <label>Jam Akhir Berlaku<span class='wajib'>*</span></label>
                            <input type="time" class='form-control end_time' name='end_time' id='end_time' required value="<?php echo substr('00:00',0,5) ?>" step="2">
                        </div>


                        <div class="col-sm-12 " id="get_form"><hr></div>

                    </div>
                </div>
            </div>


            <?php echo createBtnForm('Simpan'); ?>
            <?php echo form_close(); ?> 
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery-easyui-1.5.3/jquery.easyui.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/TimePicki-master/js/timepicki.js" type="text/javascript"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>   -->
<script type="text/javascript">

    $(document).ready(function(){

        $('#reservation_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            minuteStep:1,
            secondStep: true,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        var rules = {start_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'},end_time: {pattern: '[0-9,]{2}:[0-9]{2}:[0-9]{2}'} }

        validateForm('#ff',function(url,data){
            postData(url,data);
        });

        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        // $('.start_time').timepicki({
        //     // start_time: time_start,
        //     show_meridian:false,
        //     // showSeconds: true,
        //     min_hour_value:0,
        //     max_hour_value:23,
        //     step_size_minutes:1,
        //     step_size_second:1,
        //     overflow_minutes:true,
        //     increase_direction:'up',
        //     // disable_keyboard_mobile: true
        // });


       
        // $('.start_time').timepicker({
        //    showSecond: true,
        //    showMillisec: true,
        //    timeFormat: 'hh:mm:ss:l'
        // });

        //     $("#start_time").datetimepicker({
        //        format: 'hh:ii:ss',
        //        startView: 1,
        //        pickDate: false,
        //        autoclose: true 
        //     }).on("show", function(){
        //     $(".table-condensed .prev").css('visibility', 'hidden');
        //     $(".table-condensed .switch").text("Pick Time");
        //     $(".table-condensed .next").css('visibility', 'hidden');
        // });





        // $('.end_time').timepicki({
        //     // start_time: time_end,
        //     show_meridian:false,
        //     min_hour_value:0,
        //     max_hour_value:23,
        //     step_size_minutes:1,
        //     overflow_minutes:true,
        //     increase_direction:'up',
        //     // disable_keyboard_mobile: true
        // });

        // $('.date').datetimepicker({
        //     format: 'yyyy-mm-dd hh:ii',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     // endDate: new Date(),
        // });

        function getDataNow()
        {
            var d = new Date();
            var month = d.getMonth() +1;
            var day = d.getDate();
            var year = d.getFullYear();

            let getDay="";
            let getMonth="";

            if(day.length>1)
            {
                getDay=day;
            }
            else
            {
                getDay=`0${day}`
            }

            if(month.length>1)
            {
                getMonth=month;
            }
            else
            {
                getMonth=`0${month}`
            }            

            const returnData = `${year}-${getMonth}-${getDay}`;

            return returnData
        }

        // $('#start_date').datepicker({
        //     format: 'yyyy-mm-dd ',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,
        //     startDate: getDataNow()
        // });

        // $('#end_date').datepicker({
        //     format: 'yyyy-mm-dd ',
        //     changeMonth: true,
        //     changeYear: true,
        //     autoclose: true,
        //     todayHighlight: true,
        //     // endDate: "+1m",
        //    startDate: getDataNow()
        // });


        // $("#start_date").change(function() {

        //     var startDate = $(this).val();

        //     // destroy ini firts setting
        //     $('#end_date').datepicker('remove');

        //     // Re-int with new options
        //     $('#end_date').datepicker({
        //         format: 'yyyy-mm-dd',
        //         changeMonth: true,
        //         changeYear: true,
        //         autoclose: true,
        //         todayHighlight: true,
        //         // endDate: endDate,
        //         startDate: startDate
        //     });

        //     $('#end_date').val(startDate).datepicker("update")
        // });

           $('.date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

    })
</script>
<?php include "fileJs.php"; ?>