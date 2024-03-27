<style>
    .popover{
        max-width:80% !important
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <div class="portlet box blue" id="box">
        <?php echo headerForm($title) ?>
        <div class="portlet-body">
            <?php echo form_open('sab/action_add', 'id="ff" autocomplete="off"'); ?>
            <!-- <form autocomplete="off" role="presentation" id="fbooking" action="<?php echo site_url('sab/action_add') ?>" method="POST"></form> -->
            <div class="box-body">
                <div class="form-group" id="jadwal">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Pelabuhan Asal<span style="color:red">*</span></label>
                                <select class="form-control select2" name="origin" id="portOrigin" data-placeholder="Pelabuhan Asal" required>
                                </select>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Pelabuhan Tujuan<span style="color:red">*</span></label>
                                <select class="form-control select2" name="destination" id="portDest" data-placeholder="Pelabuhan Tujuan" required>
                                </select>
                            </div>

                            <div class="col-sm-3 form-group">
                                <label>Kelas Layanan<span style="color:red">*</span></label>
                                <select class="form-control select2" name="ship_class" id="ship_class" data-placeholder="Kelas" required>
                                </select>
                            </div>

                            <div class="col-sm-3 form-group">
                                <label>Jenis Pengguna Jasa<span style="color:red">*</span></label>
                                <select class="form-control select2" name="service" id="service" data-placeholder="Layanan" required>
                                </select>
                            </div>

                            <div class="col-sm-4 form-group">
                                <!-- <label>Pergi<span style="color:red">*</span></label> -->
                                <label>Jadwal Masuk Pelabuhan <b>(Checkin)</b><span style="color:red">*</span></label>
                                <input type="text" name="depart_date" class="form-control" placeholder="Pilih Tanggal" id="depart_date" required readonly>
                                <!-- <input type="text" name="depart_date" class="form-control" placeholder="Tanggal Pergi" id="depart_date" required value="<?php echo date('Y-m-d') ?>" readonly> -->
                            </div>

                            <div class="col-sm-2 form-group">
                                <!-- <label>Jadwal Jam Pergi<span style="color:red;">*</span></label> -->
                                <label>&nbsp;</label>
                                <select class="form-control select2" name="depart_time" id="hours" data-placeholder="Pilih Jam" required>
                                </select>
                            </div>

                            <div class="col-sm-6 form-group" id="vehicleType">
                                <label>Golongan Kendaraan<span style="color:red">*</span></label>
                                <select class="form-control select2" name="vehicle" id="vehicle_class" data-placeholder="Jenis Kendaraan" disabled="" required>
                                </select>
                            </div>

                            <div class="col-sm-6 penumpang form-group">
                                <label>Penumpang<span style="color:red">*</span></label>
                                <div class="form-group trigger">
                                    <input type="text" name="passenger-info" id="passenger-info" class="form-control" value="" required palceholder="Min 1 Dewasa atau 1 Lansia" readonly>
                                    <input type="hidden" name="passengers" id="passengers" class="form-control" value="0" disabled>
                                    <input type="hidden" name="dewasa" id="dewasa" class="form-control dewasa valData" value="0">
                                    <input type="hidden" name="anak" id="anak" class="form-control anak valData" value="0">
                                    <input type="hidden" name="bayi" id="bayi" class="form-control bayi valData" value="0">
                                    <input type="hidden" name="lansia" id="lansia" class="form-control lansia valData" value="0">
                                </div>
                                <div class="content content-pop hide" id="contentTypePass">
                                    <!-- <div class="form-group">
                                        <label class="control-label col-md-5 col-xs-12">Dewasa <span class="passengers-desc"> (5 th keatas)</span><span style="color:red">*</span></label>
                                        <div class="input-group number-spinner col-md-7 col-xs-12">
                                            <input id="adult" class="form-control text-center" type="text" name="adult" value="0">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-xs-12">Lansia <span class="passengers-desc"> 50></span><span style="color:red">*</span></label>
                                        <div class="input-group number-spinner col-md-7 col-xs-12">
                                            <input id="elder" class="form-control text-center" type="text" name="elder">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-xs-12">Anak <span class="passengers-desc"> (2-5 th)</span><span style="color:red">*</span></label>
                                        <div class="input-group number-spinner col-md-7 col-xs-12">
                                            <input id="child" class="form-control text-center" type="text" name="child">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-5 col-xs-12">Bayi <span class="passengers-desc"> (< 2 th)</span><span style="color:red">*</span></label>
                                        <div class="input-group number-spinner col-md-7 col-xs-12">
                                            <input id="infant" class="form-control text-center" type="text" name="infant">
                                        </div>
                                    </div> -->
                                    <a class="btn btn-default my-btn my-btn-default btn-block demise" >Selesai</a>
                                </div>
                            </div>
                    </div>
                    <div class="box-footer text-right">
                        <button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="nextBtn"><i class="fa fa-angle-double-right"></i> Next</button>
                    </div>
                </div>
                <div class="form-group" id="manifest">
                </div>
            </div>
        </div>
    </div>
    <!-- start loading page ferizy -->
    <div id="loader-ferizy" class="loader-ferizy display-none">
        <div class="loader-ferizy-inner">
            <div class="text-center">
                <!-- <img src="<?php echo base_url('assets/img/ferizy.svg') ?>" width="115px" height="51px" /> -->
                <img src="<?php echo base_url('assets/img/asdp-min.png') ?>" width="115px" height="51px" />
                <div id="message-loading" style="line-height:34px"></div>
                <div class="lds-ellipsis">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    <!-- end loading page ferizy -->
</div>
<!-- App JS  -->

<input type="hidden" id="baseUrl" value='<?= base_url() ?>' />
<?php include "input-passanger.php" ?>
<script type="text/javascript">   
 var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

var FORM = $('#ff'),
    PORTORIGIN = $('#portOrigin'),
    PORTDEST = $('#portDest'),
    HOURS = $('#hours'),
    TSERVICE = $('#service'),
    TVEHICLE = $('#vehicleType'),
    VEHICLE = $('#vehicle_class'),
    DEPARTDATE = $('#depart_date');
    SPECIAL = $('#special'),
    SHIPCLASS = $('#ship_class'),
    INTERMODA = $('#intermoda'),
    adult = $('#dewasa'),
    child = $('#anak'),
    infant = $('#bayi'),
    PASSENGER_INFO = $('#passenger-info'),
    btnSearch = $('#btnSearch'),
    max_capacity = 0,
    total = $('#dewasa').val() + $('#anak').val(),
    max = 0,
    max_cal_adult = 0,
    max_cal_child = 0,
    max_cal_elder = 0,
    BASE_URL = $('#baseUrl').val();

    $(document).ready(function(){

        var height_box = $(".schedule-box").height();
        $('.search-schedule-content').height(height_box-50);

        PORTORIGIN.select2();
        PORTDEST.select2();
        VEHICLE.select2();
        SHIPCLASS.select2();  

        $.ajax({
            url     : BASE_URL+'sab/getInit', 
            type    : 'GET',
            dataType: 'json',
            success: function(json) {
                // console.log(json);
                if(json.code == 1){
                    PORTORIGIN.select2({
                        data: json.origin
                    });

                    TSERVICE.select2({
                        data: json.service    
                    });
                    // $('#service_txt').val($("#service option:selected").text());

                    // SHIPCLASS.select2({
                    //     data: json.ship_class
                    // });
                    // $('#ship_class_txt').val($("#ship_class option:selected").text());                
                    
                    max_capacity  = json.max_booking_pass.param_value;
                    if ($('#service option:selected').val() == 2){
                        
                        max = $('#vehicle_class option:selected').data('capacity');
                    }else{
                        max = max_capacity;
                                            
                    }

                    // console.log(max);
                    // max_cal_adult  = max - $('#anak').val()
                    // max_cal_child  = max - $('#dewasa').val();
                    // max_cal_elder  = max - $('#lansia').val();

                    max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
                    max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
                    max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

                    $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});               
                    $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
                    $("#elder").trigger("touchspin.updatesettings", {max: max_cal_elder});
                    $("#infant").trigger("touchspin.updatesettings", {max: max});
                    
                    // set date go live 17 April 2019
                    // var startdatelive = (json.config.min_booking_date > date_now()) ? '2019-05-17 00:00:00' : json.config.min_booking_date;
                }
            },

            error: function() {
                console.log('Silahkan Hubungi Administrator')
            },
			"fnDrawCallback": function(allRow) 
            {
                let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                let getToken = allRow.json[getTokenName];
                csfrData[getTokenName] = getToken;
                $.ajaxSetup({
                    data: csfrData
                });
            }

        });

        PORTORIGIN.on('select2:select', function (e) {
            clearScheduleTime();
            SHIPCLASS.val("").trigger("change");
            SHIPCLASS.html("");
            $.ajax({
                url     : BASE_URL+'sab/getSchedule',
                data    : {origin : $(this).val(),
                             <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val(),       
                         },
                type    : 'POST',
                dataType: 'json',

                success: function(json) {

                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });
                 
                    if (json.port_destination.length > 1){
                        PORTDEST.html(`<option value="" data-capacity="0" data-desc="0" >Pilih</option`);
                    }else{
                        PORTDEST.html('');
                    }
                        DEPARTDATE.val('');
                        HOURS.html('');
                        if(json.code == 1){
                            PORTDEST.select2({
                                data: json.port_destination
                            });
                            
                            if (json.port_destination.length == 1){
                                 getShipClass(PORTORIGIN.val(), PORTDEST.val());
                            }
                            // pangggil fungsi untuk seting passanger type
                            getPassangerType()

                        // $('#origin-name').val(titleCase($("#portOrigin option:selected").text()));
                        }

                    VEHICLE.html(`<option value="" data-capacity="0" data-desc="0" >Pilih</option`);
                },

                error: function() {
                    console.log('Silahkan Hubungi Administrator')
                },
            });
        });

        PORTDEST.on('select2:select', function (e) {
            DEPARTDATE.val('');
            HOURS.html('');
            getShipClass(PORTORIGIN.val(), $(this).val());

            VEHICLE.html(`<option value="" data-capacity="0" data-desc="0" >Pilih</option`);
            
        });

        SHIPCLASS.on('select2:select', function (e) {
            clearScheduleTime();
            // console.log("haloo")
            let getService = $(`#service`).val();

            if(getService==2) // hit sini jika sevice dipilih value 2 atau kendaraan
            {
                getVehicleType();
            }
        })

        function getShipClass(origin, destination) {
            $.ajax({
                url: BASE_URL + "sab/getShipClass",
                type: "GET",
                data: { origin: origin, destination: destination,
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val()
                 },
                dataType: "json",

                beforeSend: function () {
                $('#spinner-ship').show();
                SHIPCLASS.html("");
                SHIPCLASS.prop("disabled", true);
                },

                success: function (json) {
                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });
                SHIPCLASS.select2({
                    data: json.data,
                });

                $("#ship_class_txt").val($("#ship_class option:selected").text());
                },

                error: function () {
                console.log("Silahkan Hubungi Administrator");
                },

                complete: function () {
                $('#spinner-ship').hide();
                SHIPCLASS.prop("disabled", false);
                },
            });
        }

        // $('.select2').on('select2:select', function (e) {
        //     $(this).valid()
        // });

        HOURS.select2();
        HOURS.on('select2:select', function (e) {
            var depart_time = $(this).val().split("-");
            $('#depart_time_start').val(depart_time[0]);
            $('#depart_time_end').val(depart_time[1]);
        });

        TSERVICE.select2({
            minimumResultsForSearch: -1
        })

        // $('#service_txt').val($("#service option:selected").text());
        // TSERVICE.on('select2:select', function (e) {
        //     $('#service_txt').val($("#service option:selected").text());
        // });

        
        // VEHICLE.on('select2:select', function (e) {
        //     $('#vehicle_txt').val($("#vehicle_class option:selected").text());
        // });

        // SHIPCLASS.on('select2:select', function (e) {
        //     $('#ship_class_txt').val($("#ship_class option:selected").text());
        // });

        TSERVICE.on('select2:select', function (e){

            getPassangerType();

            clearScheduleTime();
            val = this.value;
            if(val == 1){
                valPassenger()
                VEHICLE.prop("disabled", true); 
                VEHICLE.removeAttr('name'); 
                // $('#vehicle_txt').removeAttr('name');   
                $('#input-type-vehicle').toggleClass('inp-disable', true);  
                getInit(); 
            }

            if(val == 2){
                VEHICLE.prop("disabled", false); 
                VEHICLE.attr('name', 'vehicle_type');
                // $('#vehicle_txt').attr('name', 'vehicle_txt');
                $('#input-type-vehicle').toggleClass('inp-disable', false);
                getVehicleType()
                valPassenger()

                VEHICLE.change(function(){
                    valPassenger()

                    max             = $('option:selected', this).data('capacity');
                    // max_cal_adult   = max - $('#anak').val()
                    // max_cal_child  = max - $('#dewasa').val();
                    // max_cal_elder  = max - $('#lansia').val();

                    max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
                    max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
                    max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;
                    

                })
            }

        });

        // $('#portOrigin').on('select2:select', function (e) {
        //     var data = e.params.data.id;
        //     if (data == 99) {
        //         redirect('https://tiket2.indonesiaferry.co.id');
        //     }
        // });

        $("#ship_class").on("change",function(){
            getPassangerType()
        })

        /* FORM VALIDATION */
        FORM.validate({
            ignore      : 'input[type=hidden], .select2-search__field', 
            errorClass  : 'validation-error-label',
            successClass: 'validation-valid-label',
            rules   	: rules,
            messages	: messages,

            highlight   : function(element, errorClass) {
                $(element).addClass('val-error');
            },

            unhighlight : function(element, errorClass) {
                $(element).removeClass('val-error');
            },

            errorPlacement: function(error, element) {
                if (element.parents('div').hasClass('has-feedback')) {
                    error.appendTo( element.parent() );
                }

                else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo( element.parent() );
                }

                else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function(form) {
                var depart_time = HOURS.val().split("-");
                var depart_time_start = depart_time[0], depart_time_end = depart_time[1];
                var fields = $("#ff").serializeArray();
                fields.push({name: "depart_time_start", value: depart_time_start});
                fields.push({name: "depart_time_end", value: depart_time_end});
                $.ajax({
                    url: BASE_URL + "sab/action_add",
                    method: "POST",
                    data: fields,
                    dataType: "json",
                    timeout: 120000,
                    beforeSend: function () {
                        // $("#loader-ferizy")
                        //     .show()
                        //     .find("#message-loading")
                        //     .html("Mohon tunggu beberapa saat")
                        //     .addClass("ellipsis");
                        unBlockUiId('box');
                    },
                    success: function (json) {
                        $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                        csfrData[json['csrfName']] = json['tokenHash'];
                        $.ajaxSetup({
                            data: csfrData
                        });

                        if (json.code == 1) {
                            Swal.fire({
                                title: "Sukses!",
                                text: decodeHTMLEntities(json.message),
                                type: "success",
                                confirmButtonText: "Ok",
                            });
                            myData.reload('tabel_penumpang');
                            myData.reload('tabel_kendaraan');
                            $.magnificPopup.instance.close();
                        } else {
                            Swal.fire({
                                title: "Maaf, terjadi kesalahan!",
                                text: decodeHTMLEntities(json.message),
                                type: "error",
                                confirmButtonText: "Ok",
                            });
                            return false;
                        }
                    },

                    error: function (xmlhttprequest, textstatus, message) {
                        if (textstatus === "timeout") {
                            Swal.fire({
                            title: "Maaf, terjadi kesalahan!",
                            text: "Waktu permintaan ke server telah habis",
                            type: "error",
                            confirmButtonText: "Ok",
                            allowEscapeKey: false,
                            allowOutsideClick: false
                            });
                            // grecaptcha.reset();
                            return false;
                        } else {
                            Swal.fire({
                            title: "Maaf, terjadi kesalahan!",
                            text: `${textstatus}: ${message}`,
                            type: "error",
                            confirmButtonText: "Ok",
                            });
                            // grecaptcha.reset();
                            return false;
                        }
                    },

                    complete: function () {
                        $("#loader-ferizy").hide().find("#message-loading").html("");
                        // grecaptcha.reset();
                    },
                    "fnDrawCallback": function(allRow) 
                    {
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = allRow.json[getTokenName];
                        csfrData[getTokenName] = getToken;
                        $.ajaxSetup({
                            data: csfrData
                        });
                    }
                });
            }
        });


        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        // FORM.submit(function(){
        //     postData('sab/action_add',getFormData(FORM));
        // });

        $('#nextBtn').click(function(){

            // validateForm('#ff',function(url,data){
            //     postData(url,data);
            // });
            if(PORTORIGIN.val() == '' || PORTDEST.val() == '' || HOURS.val() == '' || DEPARTDATE.val() == ''|| SHIPCLASS.val() == '' || TSERVICE.val() == '' || $("#passenger-info").val() == '' ){
                FORM.submit();
            }else{
                var depart_time = HOURS.val().split("-");
                var depart_time_start = depart_time[0], depart_time_end = depart_time[1];
                var fields = $("#ff").serializeArray();
                fields.push({name: "depart_time_start", value: depart_time_start});
                fields.push({name: "depart_time_end", value: depart_time_end});

                $.ajax({
                    url: BASE_URL + "sab/validasi_jadwal",
                    method: "POST",
                    data: fields,
                    dataType: "json",
                    timeout: 120000,
                    beforeSend: function () {
                        // $.blockUI({message: '<h4><i class="fa fa-spinner fa-spin"></i> Loading</h4>'});
                        unBlockUiId('box')
                        // $("#loader-ferizy")
                        //     .show()
                        //     .find("#message-loading")
                        //     .html("Mohon tunggu beberapa saat")
                        //     .addClass("ellipsis");
                    },
                    success: function (json) {
                        $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                        csfrData[json['csrfName']] = json['tokenHash'];
                        $.ajaxSetup({
                            data: csfrData
                        });

                    if (json.code == 1) {
                        $('#jadwal').css('display', 'none');
                        $('#manifest').css('display', 'block');
                        service = $('#service').val();
                        bayi = $('#bayi').val();
                        anak = $('#anak').val();
                        dewasa = $('#dewasa').val();
                        lansia = $('#lansia').val();
                        total = parseInt(dewasa)+parseInt(anak)+parseInt(bayi)+parseInt(lansia);
                        // console.log(parseInt(dewasa)+' + '+parseInt(anak));
            
                        html = '<div class="row">\
                                    <div class="col-sm-12">\
                                        <h4>Data Instansi</h4>\
                                    </div>\
                                    <div class="col-sm-12 form-group">\
                                        <label>Nama Instansi</label>\
                                        <input type="text" class="form-control" id="name" name="name" required>\
                                    </div>\
                                    <div class="col-sm-6 form-group">\
                                        <label>Nomor Telepon</label>\
                                        <input type="number" class="form-control" minlength="10" maxlength="14" id="phone" name="phone" equired>\
                                    </div>\
                                    <div class="col-sm-6 form-group">\
                                        <label>Alamat e-mail</label>\
                                        <input type="email" class="form-control" id="email" name="email" required>\
                                    </div>\
                                </div>';
            
                        if(service == 2){
                            html += '<div class="row">\
                                        <div class="col-sm-12">\
                                            <h4>Data Kendaraan</h4>\
                                        </div>\
                                        <div class="col-sm-4 form-group">\
                                            <label>Nomor Polisi</label>\
                                            <input type="text" class="form-control" id="no_stnk" name="no_police"  required>\
                                        </div>\
                                    </div>';
                        }
            
                        html += '<div class="row">\
                                    <div class="col-sm-12">\
                                        <h4>Detail Penumpang</h4>\
                                    </div>\
                                </div>';

                        x = 0;
                        if(dewasa > 0){
                            for (i = 0; i < dewasa; i++) {
                                html += '<div class="row">\
                                        <input type="hidden" value="1" name="manifest['+x+'][passenger_type]" id="passenger_type">\
                                        <div class="col-sm-6">\
                                            <h4>Penumpang Dewasa '+(i+1)+'</h4>\
                                        </div>\
                                        <div class="col-sm-12 form-group">\
                                            <label>Nama Lengkap</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][name]" id="name" >\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Tanggal Lahir</label>\
                                            <input type="text" class="form-control datepick" name="manifest['+x+'][birthdate]" id="birthdate_'+x+'" data-type="adult" placeholder="YYYYMMDD" readonly="readonly" required>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Usia</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][age]" id="age_'+x+'" >\
                                            <span style="font-size:12px">'+passanger_type[0]['description']+'</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis ID</label>\
                                            <select class="form-control select2" name="manifest['+x+'][id_type]" id="id_type'+i+'" required>\
                                                <option value="">Pilih</option>\
                                                <?php
                                                foreach ($id_type as $key=>$value) {?>
                                                    <option value="<?php echo $value->id ?>"><?php echo strtoupper($value->name )?></option>\
                                                <?php } ?>
                                            </select>\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Nomor Identitas</label>\
                                            <input type="text" class="form-control" onkeypress="return numberOnly(event)" required name="manifest['+x+'][id_number]" id="no_'+i+'" maxlength="16">\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis Kelamin</label>\
                                            <select class="form-control select2" name="manifest['+x+'][gender]" required>\
                                                <option value="">Pilih</option>\
                                                <option value="L">Laki-laki</option>\
                                                <option value="P">Perempuan</option>\
                                            </select>\
                                        </div>'
                                    
                                        // html +=`<div class="col-sm-9 form-group">
                                        //         <label>Kota Asal</label>
                                        //         <select name="manifest[${x}][city]" id="city${x}"  class="form-control select2 required" >
                                        //         <?php foreach ($city as $key => $value) { ?>
                                        //             <option value="<?= $key ?>" ><?= $value ?></option>
                                        //         <?php } ?>
                                                
                                        //         </select>
                                        //         <span style="font-size:12px">cth. Jakarta</span>
                                        //     </div>
                                        // </div>`

                                        html +=`<div class="col-sm-9 form-group">
                                                <label>Kota Asal</label>
                                                <div id="cityPasport${i}" class="hidden">
                                                    <input type="hidden" name="manifest[${x}][city]" id="cityValue${i}">
                                                    <input type="text" class="form-control" id="cityP${i}" >
                                                </div>
                                                <div id="citySelect${i}" >
                                                    <select id="cityS${i}" class="form-control select2" >
                                                        <?php foreach ($city as $key => $value) { ?>
                                                            <option value="<?= $key ?>" ><?= $value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span style="font-size:12px">cth. Jakarta</span>
                                            </div>
                                        </div>`

                                        x++;
                            }
                        }
                        if(lansia > 0){
                            for (i = 0; i < lansia; i++) {
                                html += '<div class="row">\
                                        <input type="hidden" value="4" name="manifest['+x+'][passenger_type]" id="passenger_type">\
                                        <div class="col-sm-6">\
                                            <h4>Penumpang Lansia '+(i+1)+'</h4>\
                                        </div>\
                                        <div class="col-sm-12 form-group">\
                                            <label>Nama Lengkap</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][name]" id="name" >\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Tanggal Lahir</label>\
                                            <input type="text" class="form-control datepick" name="manifest['+x+'][birthdate]" id="birthdate_'+x+'" data-type="elder" placeholder="YYYYMMDD" readonly="readonly" required>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Usia</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][age]" id="age_'+x+'" >\
                                            <span style="font-size:12px">'+passanger_type[3]['description']+'</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis ID</label>\
                                            <select class="form-control select2" name="manifest['+x+'][id_type]" id="idl_type'+i+'" required>\
                                                <option value="">Pilih</option>\
                                                <?php
                                                foreach ($id_type as $key=>$value) {?>
                                                    <option value="<?php echo $value->id ?>"><?php echo strtoupper($value->name )?></option>\
                                                <?php } ?>
                                            </select>\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Nomor Identitas</label>\
                                            <input type="text" class="form-control" onkeypress="return numberOnly(event)" required name="manifest['+x+'][id_number]" id="nol_'+i+'" maxlength="16">\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis Kelamin</label>\
                                            <select class="form-control select2" name="manifest['+x+'][gender]" required>\
                                                <option value="">Pilih</option>\
                                                <option value="L">Laki-laki</option>\
                                                <option value="P">Perempuan</option>\
                                            </select>\
                                        </div>'

                                        // html +=`<div class="col-sm-9 form-group">
                                        //         <label>Kota Asal</label>
                                        //         <select name="manifest[${x}][city]" id="city${x}"  class="form-control select2 required" >
                                        //         <?php foreach ($city as $key => $value) { ?>
                                        //             <option value="<?= $key ?>" ><?= $value ?></option>
                                        //         <?php } ?>
                                                
                                        //         </select>
                                        //         <span style="font-size:12px">cth. Jakarta</span>
                                        //     </div>
                                        // </div>`

                                        html +=`<div class="col-sm-9 form-group">
                                                <label>Kota Asal</label>
                                                <div id="cityPasportLansia${i}" class="hidden">
                                                    <input type="hidden" name="manifest[${x}][city]" id="cityValueLansia${i}">
                                                    <input type="text" class="form-control" id="cityPLansia${i}" >
                                                </div>
                                                <div id="citySelectLansia${i}" >
                                                    <select id="citySLansia${i}" class="form-control select2" >
                                                        <?php foreach ($city as $key => $value) { ?>
                                                            <option value="<?= $key ?>" ><?= $value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span style="font-size:12px">cth. Jakarta</span>
                                            </div>
                                        </div>`

                                        x++;
                            }
                        }                        
                        if(anak > 0){
                            for (i = 0; i < anak; i++) {
                                html += '<div class="row">\
                                        <input type="hidden" value="2" name="manifest['+x+'][passenger_type]" id="passenger_type">\
                                        <div class="col-sm-6">\
                                            <h4>Penumpang Anak '+(i+1)+'</h4>\
                                        </div>\
                                        <div class="col-sm-12 form-group">\
                                            <label>Nama Lengkap</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][name]" id="name" >\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Tanggal Lahir</label>\
                                            <input type="text" class="form-control datepick" name="manifest['+x+'][birthdate]" id="birthdate_'+x+'" data-type="child" placeholder="YYYYMMDD" readonly="readonly" required>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Usia</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][age]" id="age_'+x+'" >\
                                            <span style="font-size:12px">'+passanger_type[1]['description']+'</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis ID</label>\
                                            <select class="form-control select2" name="manifest['+x+'][id_type]" id="id_type_a'+i+'" required>\
                                                <option value="">Pilih</option>\
                                                <option value="4">LAIN-LAIN</option>\
                                            </select>\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Nomor Identitas</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][id_number]" id="no_a'+i+'" maxlength="16">\
                                            <span style="font-size:12px">Bila tidak ada masukkan tanggal lahir (<strong>DDMMYYYY</strong>)</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis Kelamin</label>\
                                            <select class="form-control select2" name="manifest['+x+'][gender]" required>\
                                                <option value="">Pilih</option>\
                                                <option value="L">Laki-laki</option>\
                                                <option value="P">Perempuan</option>\
                                            </select>\
                                        </div>'

                                        html +=`<div class="col-sm-9 form-group">
                                                <label>Kota Asal</label>
                                                <select name="manifest[${x}][city]" id="city${x}"  class="form-control select2" required>
                                                <?php foreach ($city as $key => $value) { ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php } ?>
                                                
                                                </select>
                                                <span style="font-size:12px">cth. Jakarta</span>
                                            </div>
                                        </div>`

                                        x++;
                            }
                        }
                        if(bayi > 0){
                            for (i = 0; i < bayi; i++) {
                                html += '<div class="row">\
                                        <input type="hidden" value="3" name="manifest['+x+'][passenger_type]" id="passenger_type">\
                                        <div class="col-sm-6">\
                                            <h4>Penumpang Bayi '+(i+1)+'</h4>\
                                        </div>\
                                        <div class="col-sm-12 form-group">\
                                            <label>Nama Lengkap</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][name]" id="name" >\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Tanggal Lahir</label>\
                                            <input type="text" class="form-control datepick" name="manifest['+x+'][birthdate]" id="birthdate_'+x+'" data-type="infant" placeholder="YYYYMMDD" readonly="readonly" required>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Usia</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][age]" id="age_'+x+'" >\
                                            <span style="font-size:12px">'+passanger_type[2]['description']+'</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis ID</label>\
                                            <select class="form-control select2" name="manifest['+x+'][id_type]" id="id_type_b'+i+'" required>\
                                                <option value="">Pilih</option>\
                                                <option value="4">LAIN-LAIN</option>\
                                            </select>\
                                        </div>\
                                        <div class="col-sm-9 form-group">\
                                            <label>Nomor Identitas</label>\
                                            <input type="text" class="form-control" required name="manifest['+x+'][id_number]" id="no_b'+i+'" maxlength="16">\
                                            <span style="font-size:12px">Bila tidak ada masukkan tanggal lahir (<strong>DDMMYYYY</strong>)</span>\
                                        </div>\
                                        <div class="col-sm-3 form-group">\
                                            <label>Jenis Kelamin</label>\
                                            <select class="form-control select2" name="manifest['+x+'][gender]" required>\
                                                <option value="">Pilih</option>\
                                                <option value="L">Laki-laki</option>\
                                                <option value="P">Perempuan</option>\
                                            </select>\
                                        </div>'

                                        html +=`<div class="col-sm-9 form-group">
                                                <label>Kota Asal</label>
                                                <select name="manifest[${x}][city]" id="city${x}"  class="form-control select2 required" >
                                                <?php foreach ($city as $key => $value) { ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php } ?>
                                                
                                                </select>
                                                <span style="font-size:12px">cth. Jakarta</span>
                                            </div>
                                        </div>`

                                        x++;
                            }
                        }
                        html += '<div class="box-footer text-right">\
                                    <button type="button" class="btn btn-sm btn-default" onclick="prev()"><i class="fa fa-angle-double-left"></i> Back</button>\
                                    <button type="submit" class="btn btn-sm btn-primary" id=btn-submit><i class="fa fa-check"></i> Save</button>\
                                </div>\
                                <?php //echo form_close(); ?>';
                        $('#manifest').html(html);
                        $('.select2:not(.normal)').each(function () {
                            $(this).select2({
                                dropdownParent: $(this).parent()
                            });
                        });
                        for (let i = 0; i < total; i++) {
                            OnDateSelect(i);
                        }

                        $("#email").rules( "add", {
                            email: true
                        });

                        function looping(val, getFunc) {
                            for (var i = 0; i < val; i++) {
                                getFunc(i);
                            }
                        }

                        looping(dewasa, getID);
                        looping(lansia, getIDLansia);
                        looping(anak, getIDanak);
                        looping(bayi, getIDbayi);


                        $('input[id^="no_"]').each(function () {
                            $('select[id^="id_type"]').on('change', function () {
                                let thenum = this.id.replace(/^\D+/g, '');
                                let id = "#no_" + thenum;
                                let min = $(id).data('min');
                                let max = $(id).data('max');

                                if (this.value == 1) {
                                    $(id).rules('add', {
                                        nik: true
                                    });
                                }
                                else {
                                    $(id).rules('add', {
                                        nik: false,
                                    });
                                }
                            });

                            $('input[id^="no_l"]').each(function () {
                                $('select[id^="id_typel"]').on('change', function () {
                                    let thenum = this.id.replace(/^\D+/g, '');
                                    let id = "#no_l" + thenum;
                                    let min = $(id).data('min');
                                    let max = $(id).data('max');

                                    if (this.value == 1) {
                                        $(id).rules('add', {
                                            nik: true
                                        });
                                    }
                                    else {
                                        $(id).rules('add', {
                                            nik: false,
                                        });
                                    }
                                });
                            })



                            $(this).rules('add', {
                            nik: true,
                            });

                            $('input[id^="no_a"]').each(function () {
                                // let min = $(this).data('min');
                                // let max = $(this).data('max');
                                $(this).rules('add', {
                                    nik: false,
                                    // niklain: true,
                                    // minDate: [moment().subtract({ year: max + 1 }).format('YYYY-MM-DD')],
                                    // maxDate: [moment().subtract({ year: min }).format('YYYY-MM-DD')]
                                });
                                });

                                $('input[id^="no_b"]').each(function () {
                                // let min = $(this).data('min');
                                // let max = $(this).data('max');

                                $(this).rules('add', {
                                    nik: false,
                                    // niklain: true,
                                    // minDate: [moment().subtract({ year: max + 1 }).format('YYYY-MM-DD')],
                                    // maxDate: [moment().subtract({ year: min }).format('YYYY-MM-DD')]
                                });
                            });

                        });
                    }
                    if (json.code == 131) {
                        Swal.fire({
                            title: "Maaf, terjadi kesalahan!",
                            text: decodeHTMLEntities(json.message),
                            type: "error",
                            confirmButtonText: "Ok",
                        });
                        return false;
                    }

                    },

                    error: function (xmlhttprequest, textstatus, message) {
                    },

                    complete: function () {
                        // $("#loader-ferizy").hide().find("#message-loading").html("");
                        // $.unblockUI();                        
                        $('#box').unblock(); 
                        // grecaptcha.reset();
                        // return false;
                    },
                    "fnDrawCallback": function(allRow) 
                    {
                        let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                        let getToken = allRow.json[getTokenName];
                        csfrData[getTokenName] = getToken;
                        $.ajaxSetup({
                            data: csfrData
                        });
                    }
                });
                return false;
            }
        });

        var min_booking = "<?php echo $min_booking ?>"
        DEPARTDATE.datepicker({
            numberOfMonths: 1,
            format: 'yyyy-mm-dd',
            startDate: min_booking,
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function () {
            HOURS.html('');
            getHoursDeparture(PORTORIGIN.val(),PORTDEST.val(), DEPARTDATE.val(), SHIPCLASS.val());
        });
    });


    function validationnik(value) {
        let charList = [];
        for (let i = 0; i < value.length; i++) {
            const temp = value.substring(i, i + 1);

            if (charList.length === 0) {
                charList.push({ char: temp, count: 1 });
            } else {
                for (let j = 0; j < charList.length; j++) {
                const objCharlist = charList[j];

                if (objCharlist.char.toLowerCase() === temp.toLowerCase()) {
                    const charTemp = { ...objCharlist, count: objCharlist.count + 1 };

                    charList[j] = charTemp;
                    j = charList.length + 5;
                } else {
                    if (j === charList.length - 1) {
                    charList.push({ char: temp, count: 1 });
                    j = charList.length + 5;
                    }
                }
                }
            }
        }

        var max_same = <?= $param_reservation['limit_same_char_nik'] ?>;

        let isSameCharMoreThanMax = false;

        charList.forEach((obj) => {
        if (obj.count >= max_same) {
            isSameCharMoreThanMax = true;
        }
        });

        return !isSameCharMoreThanMax;
    }

    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    $.validator.addMethod(
        "nik",
        function (value) {
        return validationnik(value);
        },
        "Format NIK tidak sesuai"
    );

    $.validator.addMethod(
        "email",
        function (value) {
        return validateEmail(value);
        },
        "Format Email tidak sesuai"
    );

    jQuery.extend(jQuery.validator.messages, {
        maxlength: jQuery.validator.format("Tidak boleh lebih dari {0} karakter."),
        minlength: jQuery.validator.format("Tidak boleh kurang dari {0} karakter.")
    });

    var max_sim = <?= $param_reservation['max_length_sim'] ?>;
    var min_sim = <?= $param_reservation['min_length_sim'] ?>;
    var max_passport = <?= $param_reservation['max_length_passport'] ?>;
    var min_passport = <?= $param_reservation['min_length_passport'] ?>;
    var passanger_type = <?= json_encode($passanger_type) ?>;

    // console.log(passanger_type);

    // function getID(val) 
    // {

    //     return $("#id_type" + val).on("select2:select", function (e) {

    //         if ($(this).val() == 2) {
    //             $("#no_" + val).attr("maxlength", max_sim);
    //             $("#no_" + val).attr("minlength", min_sim);
    //             $("#no_" + val).attr("placeholder", "");
    //             $("#no_" + val).attr("readonly", false);
    //             $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
    //             $("#no_" + val).attr("onkeyup", "numberMobile(event)");
    //             if($("#no_" + val).data('datepicker')){
    //                 $("#no_" + val).datepicker("remove");
    //             }
    //             $("#no_" + val).valid();
    //         }
    //         else if ( $(this).val() == 3) {
    //             $("#no_" + val).attr("maxlength", max_passport);
    //             $("#no_" + val).attr("minlength", min_passport);
    //             $("#no_" + val).attr("placeholder", "");
    //             $("#no_" + val).attr("readonly", false);
    //             $("#no_" + val).removeAttr("onkeypress");
    //             $("#no_" + val).removeAttr("onkeyup");
    //             if($("#no_" + val).data('datepicker')){
    //                 $("#no_" + val).datepicker("remove");
    //             }
    //             $("#no_" + val).valid();
                
    //         }
    //         else if ($(this).val() == 4) {
    //             $("#no_" + val).attr("minlength", "8");
    //             $("#no_" + val).attr("maxlength", "8");
    //             $("#no_" + val).attr("readonly", true);
    //             $("#no_" + val).attr("placeholder", "tanggal lahir (ddmmyyyy)");
    //             $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
    //             $("#no_" + val).attr("onkeyup", "numberMobile(event)");
    //             $("#no_" + val).valid();

    //             let min =  `-${parseInt(passanger_type[0]['max_age']) + 1}y +1d`,
    //             max = `-${passanger_type[0]['min_age']}y`;

    //             $('#no_' + val).datepicker({
    //                 numberOfMonth: 1,
    //                 format: 'ddmmyyyy',
    //                 autoclose: true,
    //                 startDate: min,
    //                 endDate: max,
    //             }).on('changeDate', function (e) {
    //                 $(this).valid();
    //                 var birth_date = new Date(e.date)
    //                 // console.log(birth_date)
    //                 $("#no_" + val).val(birth_date);
    //             })
    //         }
    //         else {
    //             $("#no_" + val).attr("minlength", "16");
    //             $("#no_" + val).attr("maxlength", "16");
    //             $("#no_" + val).attr("readonly", false);
    //             $("#no_" + val).valid();
    //             $("#no_" + val).attr("placeholder", "");
    //             $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
    //             $("#no_" + val).attr("onkeyup", "numberMobile(event)");
    //             if($("#no_" + val).data('datepicker')){
    //                 $("#no_" + val).datepicker("remove");
    //             }
    //         }

    //     });
    // }

    // function getIDLansia(val) 
    // {
    //     return $("#idl_type" + val).on("select2:select", function (e) {

    //     if ($(this).val() == 2) {
    //         $("#nol_" + val).attr("maxlength", max_sim);
    //         $("#nol_" + val).attr("minlength", min_sim);
    //         $("#nol_" + val).attr("placeholder", "");
    //         $("#nol_" + val).attr("readonly", false);
    //         $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
    //         $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
    //         if($("#nol_" + val).data('datepicker')){
    //             $("#nol_" + val).datepicker("remove");
    //         }
    //         $("#nol_" + val).valid();
    //     }
    //     else if ( $(this).val() == 3) {
    //         $("#nol_" + val).attr("maxlength", max_passport);
    //         $("#nol_" + val).attr("minlength", min_passport);
    //         $("#nol_" + val).attr("placeholder", "");
    //         $("#nol_" + val).attr("readonly", false);
    //         $("#nol_" + val).removeAttr("onkeypress");
    //         $("#nol_" + val).removeAttr("onkeyup");
    //         if($("#nol_" + val).data('datepicker')){
    //             $("#nol_" + val).datepicker("remove");
    //         }
    //         $("#nol_" + val).valid();
    //     }
    //     else if ($(this).val() == 4) {
    //         $("#nol_" + val).attr("minlength", "8");
    //         $("#nol_" + val).attr("maxlength", "8");
    //         $("#nol_" + val).attr("readonly", true);
    //         $("#nol_" + val).attr("placeholder", "tanggal lahir (ddmmyyyy)");
    //         $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
    //         $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
    //         $("#nol_" + val).valid();

    //         let min =  `-${parseInt(passanger_type[3]['max_age']) + 1}y +1d`,
    //         max = `-${passanger_type[3]['min_age']}y`;

    //         $('#nol_' + val).datepicker({
    //             numberOfMonth: 1,
    //             format: 'ddmmyyyy',
    //             autoclose: true,
    //             startDate: min,
    //             endDate: max,
    //         }).on('changeDate', function (e) {
    //             $(this).valid();
    //             var birth_date = new Date(e.date)
    //             // console.log(birth_date)
    //             $("#nol_" + val).val(birth_date);
    //         })
    //     }
    //     else {
    //         $("#nol_" + val).attr("minlength", "16");
    //         $("#nol_" + val).attr("maxlength", "16");
    //         $("#nol_" + val).attr("readonly", false);
    //         $("#nol_" + val).valid();
    //         $("#nol_" + val).attr("placeholder", "");
    //         $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
    //         $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
    //         if($("#nol_" + val).data('datepicker')){
    //             $("#nol_" + val).datepicker("remove");
    //         }
    //     }
    //     });
    // }

    function getID(val) 
    {
        return $("#id_type" + val).on("select2:select", function (e) {
        
        let manifestVal = $("#cityPasport" + val).find("#cityValue" + val).attr('name');
        
        // console.log(manifestVal)
        if ($(this).val() == 1) {
            $("#no_" + val).attr("maxlength", "16");
            $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#citySelect" + val).attr("class", "");
            $("#cityPasport" + val).attr("class", "hidden");
            $("#cityS" + val).attr("name",manifestVal );
            $("#cityP" + val).attr("name", "");
            // $("#cityS" + val).prop("required", true);
            // $("#cityP" + val).prop("required", false);
            $("#cityS" + val).attr("required", true);
            $("#cityP" + val).removeAttr("required");


        }
        else if ($(this).val() == 2) {
            $("#no_" + val).attr("maxlength", max_sim);
            $("#no_" + val).attr("minlength", min_sim);
            $("#no_" + val).attr("placeholder", "");
            $("#no_" + val).attr("readonly", false);
           
            $("#no_" + val).attr("onkeyup", "numberMobile(event)");
            if($("#no_" + val).data('datepicker')){
                $("#no_" + val).datepicker("remove");
            }
            $("#no_" + val).valid();
            $("#citySelect" + val).attr("class", "");
            $("#cityPasport" + val).attr("class", "hidden");
            $("#cityS" + val).attr("name", manifestVal);
            $("#cityP" + val).attr("name", "");
            // $("#cityS" + val).prop("required", true);
            // $("#cityP" + val).prop("required", false);
            $("#cityS" + val).attr("required", true);
            $("#cityP" + val).removeAttr("required");

        }
        else if ( $(this).val() == 3) {
            $("#no_" + val).attr("maxlength", max_passport);
            $("#no_" + val).attr("minlength", min_passport);
            $("#no_" + val).attr("placeholder", "");
            $("#no_" + val).attr("readonly", false);
            $("#no_" + val).removeAttr("onkeypress");
            $("#no_" + val).removeAttr("onkeyup");
            if($("#no_" + val).data('datepicker')){
                $("#no_" + val).datepicker("remove");
            }
            $("#no_" + val).valid();
            $("#cityPasport" + val).attr("class", "");
            $("#citySelect" + val).attr("class", "hidden");
            $("#cityP" + val).attr("name", manifestVal);
            $("#cityS" + val).attr("name", "");
            // $("#cityP" + val).prop("required", true);
            // $("#cityS" + val).prop("required", false);
            $("#cityP" + val).attr("required", true);
            $("#cityS" + val).removeAttr("required");
         
        }
        else if ($(this).val() == 4) {
            $("#no_" + val).attr("minlength", "8");
            $("#no_" + val).attr("maxlength", "8");
            $("#no_" + val).attr("readonly", true);
            $("#no_" + val).attr("placeholder", "tanggal lahir (ddmmyyyy)");
            $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#no_" + val).attr("onkeyup", "numberMobile(event)");
            $("#no_" + val).valid();
            $("#citySelect" + val).attr("class", "");
            $("#cityPasport" + val).attr("class", "hidden");
            $("#cityS" + val).attr("name", manifestVal);
            $("#cityP" + val).attr("name", "");
            // $("#cityS" + val).prop("required", true);
            // $("#cityP" + val).prop("required", false);
            $("#cityS" + val).attr("required", true);
            $("#cityP" + val).removeAttr("required");

            let min =  `-${parseInt(passanger_type[0]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[0]['min_age']}y`;

            $('#no_' + val).datepicker({
                numberOfMonth: 1,
                format: 'ddmmyyyy',
                autoclose: true,
                startDate: min,
                endDate: max,
            }).on('changeDate', function (e) {
                $(this).valid();
                var birth_date = new Date(e.date)
                // console.log(birth_date)
                $("#no_" + val).val(birth_date);
            })
        }
        else {
            $("#no_" + val).attr("minlength", "16");
            $("#no_" + val).attr("maxlength", "16");
            $("#no_" + val).attr("readonly", false);
            $("#no_" + val).valid();
            $("#no_" + val).attr("placeholder", "");
            $("#no_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#no_" + val).attr("onkeyup", "numberMobile(event)");
            if($("#no_" + val).data('datepicker')){
                $("#no_" + val).datepicker("remove");
            }

            $("#citySelect" + val).attr("class", "");
            $("#cityPasport" + val).attr("class", "hidden");
            $("#cityS" + val).attr("name", manifestVal);
            $("#cityP" + val).attr("name", "");
            // $("#cityS" + val).prop("required", true);
            // $("#cityP" + val).prop("required", false);
            $("#cityS" + val).attr("required", true);
            $("#cityP" + val).removeAttr("required");
        }
        });
    }

    function getIDLansia(val) 
    {
        return $("#idl_type" + val).on("select2:select", function (e) {

        let manifestVal = $("#cityPasportLansia" + val).find("#cityValueLansia" + val).attr('name');
        
        // console.log(manifestVal)
        if ($(this).val() == 1) {
            $("#nol_" + val).attr("maxlength", "16");
            $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
            
            $("#citySelectLansia" + val).attr("class", "");
            $("#cityPasportLansia" + val).attr("class", "hidden");
            $("#citySLansia" + val).attr("name",manifestVal );
            $("#cityPLansia" + val).attr("name", "");
            $("#citySLansia" + val).attr("required", true);
            $("#cityPLansia" + val).removeAttr("required");

        }
        else if ($(this).val() == 2) {
            $("#nol_" + val).attr("maxlength", max_sim);
            $("#nol_" + val).attr("minlength", min_sim);
            $("#nol_" + val).attr("placeholder", "");
            $("#nol_" + val).attr("readonly", false);
            $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
            if($("#nol_" + val).data('datepicker')){
                $("#nol_" + val).datepicker("remove");
            }
            $("#nol_" + val).valid();
            $("#citySelectLansia" + val).attr("class", "");
            $("#cityPasportLansia" + val).attr("class", "hidden");
            $("#citySLansia" + val).attr("name", manifestVal);
            $("#cityPLansia" + val).attr("name", "");
            $("#citySLansia" + val).attr("required", true);
            $("#cityPLansia" + val).removeAttr("required");
        }
        else if ( $(this).val() == 3) {
            $("#nol_" + val).attr("maxlength", max_passport);
            $("#nol_" + val).attr("minlength", min_passport);
            $("#nol_" + val).attr("placeholder", "");
            $("#nol_" + val).attr("readonly", false);
            $("#nol_" + val).removeAttr("onkeypress");
            $("#nol_" + val).removeAttr("onkeyup");
            if($("#nol_" + val).data('datepicker')){
                $("#nol_" + val).datepicker("remove");
            }
            $("#nol_" + val).valid();
            $("#cityPasportLansia" + val).attr("class", "");
            $("#citySelectLansia" + val).attr("class", "hidden");
            $("#cityPLansia" + val).attr("name", manifestVal);
            $("#citySLansia" + val).attr("name", "");
            $("#cityPLansia" + val).attr("required", true);
            $("#citySLansia" + val).removeAttr("required");
        }
        else if ($(this).val() == 4) {
            $("#nol_" + val).attr("minlength", "8");
            $("#nol_" + val).attr("maxlength", "8");
            $("#nol_" + val).attr("readonly", true);
            $("#nol_" + val).attr("placeholder", "tanggal lahir (ddmmyyyy)");
            $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
            $("#nol_" + val).valid();
            $("#citySelectLansia" + val).attr("class", "");
            $("#cityPasportLansia" + val).attr("class", "hidden");
            $("#citySLansia" + val).attr("name", manifestVal);
            $("#cityPLansia" + val).attr("name", "");
            $("#citySLansia" + val).attr("required", true);
            $("#cityPLansia" + val).removeAttr("required");

            let min =  `-${parseInt(passanger_type[3]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[3]['min_age']}y`;

            $('#nol_' + val).datepicker({
                numberOfMonth: 1,
                format: 'ddmmyyyy',
                autoclose: true,
                startDate: min,
                endDate: max,
            }).on('changeDate', function (e) {
                $(this).valid();
                var birth_date = new Date(e.date)
                // console.log(birth_date)
                $("#nol_" + val).val(birth_date);
            })
        }
        else {
            $("#nol_" + val).attr("minlength", "16");
            $("#nol_" + val).attr("maxlength", "16");
            $("#nol_" + val).attr("readonly", false);
            $("#nol_" + val).valid();
            $("#nol_" + val).attr("placeholder", "");
            $("#nol_" + val).attr("onkeypress", "return numberOnly(event)");
            $("#nol_" + val).attr("onkeyup", "numberMobile(event)");
            if($("#nol_" + val).data('datepicker')){
                $("#nol_" + val).datepicker("remove");
            }

            $("#citySelectLansia" + val).attr("class", "");
            $("#cityPasportLansia" + val).attr("class", "hidden");
            $("#citySLansia" + val).attr("name", manifestVal);
            $("#cityPLansia" + val).attr("name", "");
            $("#citySLansia" + val).attr("required", true);
            $("#cityPLansia" + val).removeAttr("required");
        }
        });
    }

    function getIDanak(i) {
        $("#no_a" + i).attr("readonly", true);
        let min =  `-${parseInt(passanger_type[1]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[1]['min_age']}y`;

        $('#no_a' + i).datepicker({
                numberOfMonth: 1,
                format: 'ddmmyyyy',
                autoclose: true,
                startDate: min,
                endDate: max,
            }).on('changeDate', function (e) {
                $(this).valid();
                var birth_date = new Date(e.date)
                // console.log(birth_date)
                $("#no_a" + val).val(birth_date);
            })
    }

    function getIDbayi(i) {
        $("#no_b" + i).attr("readonly", true);
        let min =  `-${parseInt(passanger_type[2]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[2]['min_age']}y`;

        $('#no_b' + i).datepicker({
            numberOfMonth: 1,
            format: 'ddmmyyyy',
            autoclose: true,
            startDate: min,
            endDate: max,
        }).on('changeDate', function (e) {
            $(this).valid();
            var birth_date = new Date(e.date)
            // console.log(birth_date)
            $("#no_b" + val).val(birth_date);
        })
    }

    function numberOnly(evt) {
        var charCode = evt.which ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
        return true;
    }

    function numberMobile(e){
        e.target.value = e.target.value.replace(/[^\d]/g,'');
        return false;
    }

    function OnDateSelect(j){
        
        type = $('#birthdate_'+j).attr('data-type');
        var date = new Date();
        var min 		= '' ;
        var max 		= '' ;
        if (type == 'adult') {
            min =  `-${parseInt(passanger_type[0]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[0]['min_age']}y`;
        }else if (type == 'child' ) {
            min =  `-${parseInt(passanger_type[1]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[1]['min_age']}y`;
        }
        else if (type == 'elder' ) {
            min =  `-${parseInt(passanger_type[3]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[3]['min_age']}y`;
        }        
        else{
            min =  `-${parseInt(passanger_type[2]['max_age']) + 1}y +1d`,
            max = `-${passanger_type[2]['min_age']}y`;
        }

        $('#birthdate_'+j).datepicker({
            numberOfMonths: 1,
            format: 'yyyy-mm-dd',
            startDate: min,
            endDate: max,
            autoclose: true
        }).on('changeDate', function (e) {
            var birthdate = new Date(e.date);
            var today = new Date();
            // var age = Math.floor((today-birthdate) / (365.25 * 24 * 60 * 60 * 1000));
            var age = GetAge(birthdate);
            $('#age_'+j).val(age)
        });
    }

    function GetAge(birthDate) {
        var today = new Date();
        var birthDate1 = new Date(birthDate);
        var age = today.getFullYear() - birthDate1.getFullYear();
        var m = today.getMonth() - birthDate1.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate1.getDate())) {
        age--;
        }
        return age;
    }

    function prev(){
        $('#manifest').css('display', 'none');
        $('#jadwal').css('display', 'block');
        $('.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    }

    function getInit(){
        $.ajax({
            url     : BASE_URL+'sab/getInit', 
            type    : 'GET',
            dataType: 'json',

            beforeSend: function(){},

            success: function(json) {

                max_capacity  = json.max_booking_pass.param_value;
                if ($('#service option:selected').val() == 2){
                    max = $('#vehicle_class option:selected').data('capacity');
                }else{
                    max = max_capacity;
                }

                // max_cal_adult  = max - $('#anak').val()
                // max_cal_child  = max - $('#dewasa').val();
                // max_cal_elder  = max - $('#lansia').val();

                max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
                max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
                max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

                $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});
                $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
                $("#lansia").trigger("touchspin.updatesettings", {max: max_cal_elder});
                $("#infant").trigger("touchspin.updatesettings", {max: max});

            
            },

            error: function() {
                console.log('Silahkan Hubungi Administrator')
            },

            complete: function(){},
        });
    }

    $('#cbx').change(function(){
        if (this.checked) {
        // getHoursReturn(PORTDEST.val(),PORTORIGIN.val(), DATERETURN.val());
        }
    });

    function getHoursDeparture(origin, destination, depart_date, ship_class){
        $.ajax({
            url     : BASE_URL+'sab/getHours',
            data    : {
                origin: origin,
                destination: destination,
                depart_date: depart_date,
                ship_class: ship_class,
                <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val()
            },
            type    : 'POST',
            dataType: 'json',

            success: function(json) {
                $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });

                HOURS.html('')
                if(json.code == 1){
                    HOURS.select2({
                        data: json.times
                    })
                }
            },

            error: function() {
                console.log('Silahkan Hubungi Administrator');
            },
			"fnDrawCallback": function(allRow) 
            {
                let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                let getToken = allRow.json[getTokenName];
                csfrData[getTokenName] = getToken;
                $.ajaxSetup({
                    data: csfrData
                });
            }

        });
    }

    function getVehicleType(){
        
        const portOrigin = $("#portOrigin").val();
        const ship_class = $("#ship_class").val();
        if(portOrigin !=="" && ship_class !=="" )
        {
            const data = {portOrigin : portOrigin ,ship_class : ship_class, <?php echo $this->security->get_csrf_token_name(); ?>:$("input[name=" + csfrData.csrfName + "]").val()}
            $.ajax({
                url     : BASE_URL+'sab/getVehicle',
                // data : `portOrigin=${$("#portOrigin").val()}&ship_class=${$("#ship_class").val()}`,
                data    : data,
                dataType: 'json',

                success: function(json) {
                    
                    $("input[name=" + json.csrfName + "]").val(json.tokenHash);
                    csfrData[json['csrfName']] = json['tokenHash'];
                    $.ajaxSetup({
                        data: csfrData
                    });

                    if(json.code == 1){

                        /*
                        VEHICLE.select2(
                            {
                                data: json.data,
                                templateSelection: function (data, container) {
                                    // Add custom attributes to the <option> tag for the selected option
                                    $(data.element).attr('data-capacity', data.capacity);
                                    $(data.element).attr('data-desc', data.description);
                                    return data.text;
                                },
                                templateResult: function (data) {
                                    return '<div class="my-semi-bold">'+data.text+'</div><div><small>'+data.description+'</small></div>';
                                }, 
                                escapeMarkup: function (m) {
                                    return m;
                                }
                            }

                        );
                        */

                        // console.log(json.data);
                        let dataVehicle = json.data
                        let html = "";
                        dataVehicle.forEach(data => {
                            html +=` <option data-capacity="${data.capacity}" data-desc="${data.description}" value=${data.id}>                                    
                                        ${data.text}
                                    </option>`;
                        });

                        VEHICLE.html(html);
                        VEHICLE.select2({
                            data: json.data,
                            templateSelection: function (data, container) {
                                // Add custom attributes to the <option> tag for the selected option
                                $(data.element).attr('data-capacity', data.capacity);
                                $(data.element).attr('data-desc', data.description);
                                return data.text;
                            },                        
                            templateResult: function (data){
                                return '<div class="my-semi-bold">'+data.text+'</div><div><small>'+data.description+'</small></div>';
                            }
                            , 
                            escapeMarkup: function (m) {
                                return m;
                            }
                        })

                        
                        function formatOption (option) {
                            var $option = $(
                            '<div><strong>' + option.text + '</strong></div><div>' + option.title + '</div>'
                            );
                            return $option;
                        };

                        valPassenger()
                        max = $('#vehicle_class option:selected').data('capacity');

                        max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
                        max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
                        max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

                        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});
                        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
                        $("#lansia").trigger("touchspin.updatesettings", {max: max_cal_elder});
                        $("#infant").trigger("touchspin.updatesettings", {max: max});
                        
                    }
                },

                error: function() {
                    console.log('Silahkan Hubungi Administrator');
                },

            });
        }
    }

    function valPassenger(){
        // $('#dewasa').val(1);
        $('#dewasa').val(0);
        $('#anak').val(0);
        $('#bayi').val(0);
        $('#lansia').val(0);
        // PASSENGER_INFO.val('1 Dewasa');

        PASSENGER_INFO.val('');

        $popover.on('shown.bs.popover', function() { 
            $('#adult').val($('#dewasa').val());
            $('#child').val($('#anak').val());
            $('#infant').val($('#bayi').val());
            $('#elder').val($('#lansia').val());
        });
    };

    function decodeHTMLEntities(str) {
        if (str && typeof str === "string") {
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gim, "");
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gim, "");
        }

        return str;
    };

    function clearScheduleTime() {
        DEPARTDATE.val("");
        HOURS.val("").trigger("change");
        HOURS.html("");
    }
    

    $('body').on('change','#elder', function(){
        $('#lansia').val(this.value);

        let dataElder = $(this).val();
        let dataAdult = parseInt($("#adult").val());

        if(dataElder>0 || dataAdult > 0 )
        {
            $("#child").prop("disabled", false);
            $("#child").css("background-color","#ffff")

            $("#infant").prop("disabled", false);
            $("#infant").css("background-color","#ffff")
        }
        else
        {
            $("#child").prop("disabled", true);
            $("#child").css("background-color","#eef1f5")
            $("#anak").val(0)
            $("#child").val(0)

            $("#infant").prop("disabled", true)
            $("#infant").css("background-color","#eef1f5")
            $("#bayi").val(0)
            $("#infant").val(0)
            
        }


        
        if ($('#service option:selected').val() == 1){
            max = max_capacity;

        }else{
            max = $('#vehicle_class option:selected').data('capacity');
                                
        }

        max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
        max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
        max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});
        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
        $("#elder").trigger("touchspin.updatesettings", {max: max_cal_elder});
        $("#infant").trigger("touchspin.updatesettings", {max: max});

        passengersInfoText();
    });

    $('body').on('change','#adult', function(){
        $('#dewasa').val(this.value);

        let dataAdult = $(this).val();
        let dataElder = parseInt($("#elder").val());

        if(dataAdult>0 || dataElder>0)
        {
            $("#child").prop("disabled", false);
            $("#child").css("background-color","#ffff")

            $("#infant").prop("disabled", false);
            $("#infant").css("background-color","#ffff")
        }
        else
        {
            $("#child").prop("disabled", true)
            $("#child").css("background-color","#eef1f5")
            $("#anak").val(0)
            $("#child").val(0)

            $("#infant").prop("disabled", true)
            $("#infant").css("background-color","#eef1f5")
            $("#bayi").val(0)
            $("#infant").val(0)
        }


        if ($('#service option:selected').val() == 1){
            max = max_capacity;

        }else{
            max = $('#vehicle_class option:selected').data('capacity');
                                
        }

        max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
        max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
        max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});
        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
        $("#elder").trigger("touchspin.updatesettings", {max: max_cal_elder});
        $("#infant").trigger("touchspin.updatesettings", {max: max});

        passengersInfoText();

    });

    $('body').on('change','#child', function(){
        $('#anak').val(this.value);


        if ($('#service option:selected').val() == 1){
            max = max_capacity;

        }else{
            max = $('#vehicle_class option:selected').data('capacity');
                                
        }

        max_cal_adult  = max - $('#anak').val() - $('#lansia').val();
        max_cal_child  = max - $('#dewasa').val() - $('#lansia').val();
        max_cal_elder  = max - $('#anak').val() - $('#dewasa').val() ;

        $("#adult").trigger("touchspin.updatesettings", {max: max_cal_adult});
        $("#child").trigger("touchspin.updatesettings", {max: max_cal_child});
        $("#elder").trigger("touchspin.updatesettings", {max: max_cal_elder});
        $("#infant").trigger("touchspin.updatesettings", {max: max});

        passengersInfoText();
    });



    $('body').on('change','#infant', function(){
        $('#bayi').val(this.value);
        passengersInfoText();
    });    
</script>