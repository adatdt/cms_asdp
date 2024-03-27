<style type="text/css">
    .tabel {
        border-collapse: collapse;
    }

    .tabel th,
    .tabel td {
        padding: 5px 5px;
        border: 1px solid #000;
    }

    .tabel th {
        font-weight: normal;
    }

    .tabel-no-border tr {
        border: 1px solid #000;
    }

    .tabel-no-border th,
    .tabel-no-border td {
        padding: 5px 5px;
        border: 0px;
    }

    .tabel-no-border-new tr {
        border: 0px solid #000;
    }

    .tabel-no-border-new th,
    .tabel-no-border td {
        padding: 5px 5px;
        border: 0px;
    }

    .full-width {
        width: 100%;
    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .bold {
        font-weight: bold;
    }

    .italic {
        font-style: italic;
    }

    .no-border-right {
        border-right: none;
    }

    td.border-right {
        border-right: 1px solid #000
    }

    .pad-top {
        padding-top: 5px;
    }

    .select2 {
        min-width: 150px;
    }
</style>



<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?><?= $this->session->userdata('port_id'); ?>

                </div>
            </div>


            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-toolbar" style="margin-bottom: 0px">
                            <div class="row">
                                <div class="col-md-12 form-inline">
                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <span class="input-group-addon">Keberangkatan</span>
                                        <input type="text" autocomplete="off" id="datefrom" class="form-control input-small" value="<?php echo date('Y-m-d'); ?>" readonly></input>
                                        <div class="input-group-addon">s/d</div>
                                        <input type="text" autocomplete="off" id="dateto" class="form-control input-small" value="<?php echo date('Y-m-d'); ?>" readonly></input>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <span class="input-group-addon">Pembayaran</span>
                                        <input type="text" autocomplete="off" id="paymentDateFrom" class="form-control input-small" placeholder="YYYY-MM-DD" readonly></input>
                                        <div class="input-group-addon">s/d</div>
                                        <input type="text" autocomplete="off" id="paymentDateTo" class="form-control input-small" placeholder="YYYY-MM-DD" readonly></input>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <span class="input-group-addon">Pelabuhan</span>
                                        <?php echo form_dropdown('port', $port, '', 'id="port" class="form-control select2 input-small"'); ?>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <span class="input-group-addon">Lintasan</span>
                                        <?php echo form_dropdown('route', $route, '', 'id="route" class="form-control select2 input-small"'); ?>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <div class="input-group-addon">Kelas Layanan</div>
                                        <?php
											if ($this->session->userdata('ship_class_id') != '') {
												$selected = 'disabled="disabled"';
											} else {
												$selected = '';
											}
											?>
											<select id="shipClass" <?php echo $selected ?> class="form-control js-data-example-ajax select2" dir=""><?php echo $shipClass ?></select>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <div class="input-group-addon">Status Expired</div>
                                        <?php echo form_dropdown('statusExpired', $statusExpired, '', 'id="statusExpired" class="form-control select2"'); ?>
                                    </div>

                                    <div class="input-group select2-bootstrap-prepend pad-top">
                                        <button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>

                </div>



                <div class="kt-portlet__body" style="padding-top: 20px">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item active">
                            <a id="tabPenumpang" class="label label-primary" data-toggle="tab" data-target="#penumpang" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Pejalan Kaki</a>
                        </li>

                        <li class="nav-item">
                            <a id="tabKendaraan" class="label label-primary" data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Kendaraan</a>
                        </li>
                    </ul>
                    <button id="excelkita" class="btn btn-sm btn-default" style="display:none"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>
                </div>

                <div id="ini_replace"></div>

                <div id="master_tabel" style="display: none">

                    <div class="form-inline" style="margin-top: 10px">
                        <button id="pdfDownload" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

                        <button id="excelDownload" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br>
                    </div><br>

                    <table class="tabel full-width" align="center">
                        <tr>
                            <td rowspan="4" class="no-border-right" style="width: 10%">
                                <img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
                            </td>
                            <td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
                                <span> LAPORAN TIKET EXPIRED</span>
                            </td>
                            <td style="width: 10%" class="no-border-right">No Dokumen</td>
                            <td style="width: 20%"> </td>
                        </tr>
                        <tr>
                            <td class="no-border-right">Revisi</td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td class="no-border-right">Berlaku Efektif</td>
                            <td> </td>
                        </tr>
                        <tr>
                            <td class="no-border-right">Halaman</td>
                            <td> </td>
                        </tr>
                    </table>

                    <br>

                    <table class="table table-333 table-bordered full-width" align="center">
                        <tr>
                            <td style="border-right:none; width: 15%">Waktu Keberangkatan</td>
                            <td style="border-right:none; border-left:none; width: 1%">:</td>
                            <td style="border-left: none" id="tBerangkat"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none; width: 15%">Waktu Pembayaran</td>
                            <td style="border-right:none; border-left:none; width: 1%">:</td>
                            <td style="border-left: none" id="tPembayaran"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Pelabuhan</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tPelabuhan"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Lintasan</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tLintasan"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Kelas Layanan</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tKLayanan"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Status Expired</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tStatus"></td>
                        </tr>
                    </table>

                    <div class="tab-content ">

                        <!-- tab data penumpang -->
                        <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: none">
                            <div class="table-scrollable">
                                <table class="tabel full-width" style="font-size: 10px">
                                    <tbody id="trPenumpang"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: none">
                            <div class="table-scrollable">
                                <table class="tabel full-width" style="font-size: 10px">
                                    <tbody id="trKendaraan"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <br>
            </div>


        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        $('#datefrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate', function(e) {
            $('#dateto').datepicker('setStartDate', e.date)
        });

        $('#dateto').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#datefrom').val(),
            endDate: new Date(),
        }).on('changeDate', function(e) {
            $('#datefrom').datepicker('setEndDate', e.date)
        });

        $('#paymentDateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate', function(e) {
            $('#paymentDateTo').datepicker('setStartDate', e.date)
        });

        $('#paymentDateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#paymentDateFrom').val(),
            endDate: new Date(),
        }).on('changeDate', function(e) {
            $('#paymentDateFrom').datepicker('setEndDate', e.date)
        });



        $('#cari').click(function() {
            prosessData();
        });

        $("#excelDownload").click(function() {
            var dateTo = $("#dateto").val(),
                dateFrom = $("#datefrom").val(),
                shipClass = $("#shipClass").val(),
                port_origin = $("#port").val(),
                statusExpired = $("#statusExpired").val(),
                route = $("#route").val(),
                paymentDateFrom = $("#paymentDateFrom").val(),
                paymentDateTo = $("#paymentDateTo").val(),
                statusData = $('.tab-content > .active').attr('id') == 'kendaraan' ? 'kendaraan' : 'penumpang';
            var url_download = `dateTo=${dateTo}&dateFrom=${dateFrom}&shipClass=${shipClass}&port_origin=${port_origin}&statusExpired=${statusExpired}&route=${route}&paymentDateFrom=${paymentDateFrom}&paymentDateTo=${paymentDateTo}&statusData=${statusData}`;
            window.open("<?php echo site_url('laporan/ticket_expired_v2/download_excel?') ?>" + url_download);
        });

        $("#pdfDownload").click(function() {
            var dateTo = $("#dateto").val(),
                dateFrom = $("#datefrom").val(),
                shipClassName = $("#shipClass").val(),
                port_origin = $("#port").val(),
                statusExpired = $("#statusExpired").val(),
                route = $("#route").val(),
                paymentDateFrom = $("#paymentDateFrom").val(),
                paymentDateTo = $("#paymentDateTo").val(),
                statusData = $('.tab-content > .active').attr('id') == 'kendaraan' ? 'kendaraan' : 'penumpang';
            var url_download = `dateTo=${dateTo}&dateFrom=${dateFrom}&shipClass=${shipClassName}&port_origin=${port_origin}&statusExpired=${statusExpired}&route=${route}&paymentDateFrom=${paymentDateFrom}&paymentDateTo=${paymentDateTo}&statusData=${statusData}`;
            window.open("<?php echo site_url('laporan/ticket_expired_v2/download_pdf?') ?>" + url_download);
        });

        $(document).bind("ajaxStart", function() {
            $("#cari").button('loading');
            $("#master_tabel").hide();
        }).bind("ajaxStop", function() {
            var tBerangkat = $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));
            if ($('#paymentDateFrom').val() == '' && $('#paymentDateTo').val() == '') {
                var tPembayaran = '-';
            } else if ($('#paymentDateFrom').val() == '' && $('#paymentDateTo').val() != '') {
                var tPembayaran = $.datepicker.formatDate("d M yy", new Date($('#paymentDateTo').val()));
            } else if ($('#paymentDateTo').val() == '' && $('#paymentDateFrom').val() != '') {
                var tPembayaran = $.datepicker.formatDate("d M yy", new Date($('#paymentDateFrom').val()));
            } else {
                var tPembayaran = $.datepicker.formatDate("d M yy", new Date($('#paymentDateFrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#paymentDateTo').val()));
            }
            var tPelabuhan = $("#port option:selected").text();
            var tLintasan = $("#route option:selected").text();
            var tKLayanan = $("#shipClass option:selected").text();
            var tStatus = $('#statusExpired option:selected').text();


            $("#master_tabel").show();
            $("#tBerangkat").html(tBerangkat);
            $("#tPembayaran").html(tPembayaran);
            $("#tPelabuhan").html((tPelabuhan == 'Pilih' || tPelabuhan == '') ? 'Semua' : tPelabuhan);
            $("#tLintasan").html((tLintasan == 'Pilih' || tLintasan == '') ? 'Semua' : tLintasan);
            $("#tKLayanan").html((tKLayanan == 'Pilih' || tKLayanan == '') ? 'Semua' : tKLayanan);
            $("#tStatus").html((tStatus == 'Pilih' || tStatus == '') ? 'Semua' : tStatus);
            $("#cari").button('reset');
        });


        // prosessData();

        function prosessData() {
            $.when(getData(true), getData(false))
        }

        function getData(ketStatus) {
            var urlGetData, idTableTr, initStatus;
            if (ketStatus == true) {
                urlGetData = '?s=kendaraan';
                idTableTr = '#trKendaraan';
                initStatus = 'kendaraan';
            } else {
                urlGetData = '?s=penumpang';
                idTableTr = '#trPenumpang';
                initStatus = 'penumpang';
            }

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('laporan/ticket_expired_v2') ?>" + urlGetData,
                dataType: 'json',
                data: {
                    dateTo: $("#dateto").val(),
                    dateFrom: $("#datefrom").val(),
                    shipClass: $("#shipClass").val(),
                    port_origin: $("#port").val(),
                    statusExpired: $("#statusExpired").val(),
                    route: $("#route").val(),
                    paymentDateFrom: $("#paymentDateFrom").val(),
                    paymentDateTo: $("#paymentDateTo").val(),
                },
                beforeSend: function() {
                    if (ketStatus == true) {
                        $("#tabKendaraan").button('loading');
                    } else {
                        $("#tabPenumpang").button('loading');
                    }
                },
                success: function(data) {


                    $(idTableTr).html(create_table(data, ketStatus));
                    if (ketStatus == true) {
                        $("#tabKendaraan").button('reset');
                    } else {
                        $("#tabPenumpang").button('reset');
                    }

                },
                error: function(error) {
                    toastr.error('Error', 'Peringatan');
                }
            });
        }

        function create_table(data, statusData) {
            // var json = JSON.parse(data);
            var html;

            // Header Table
            html += `
                <tr class="bold center">
                    <td>KODE BOOKING</td>
                    <td>NO TIKET</td>
                    `;
            if (statusData == true) {
                html += `<td>PANJANG PADA <br />PEMESANAN (METER)</td>`;
            }
            html += `
                    <td> GOLONGAN </td>
                    <td>KELAS LAYANAN</td>
                    <td>TARIF GOLONGAN (Rp.)</td>
                    <td>WAKTU PEMBAYARAN</td>
                    <td>JADWAL KEBERANGKATAN</td>
                    <td>LINTASAN DIPESAN</td>
                    <td>STATUS TIKET</td>
                    <td>STATUS EXPIRED</td>
                    <td>WAKTU PENGAKUAN PENDAPATAN</td>
                    <td>JUMLAH PENDAPATAN EXPIRED</td>
                </tr>
            `;

            // Data Table
            $.each(data, function(k, v) {
                html += `
                    <tr>
                        <td>${v.booking_code}</td>
                        <td>${v.ticket_number}</td>
                        `;
                if (statusData == true) {
                    html += `<td>${v.length_vehicle}</td>`;
                }
                html += `
                        <td>${v.golongan}</td>
                        <td>${v.ship_class_name}</td>
                        <td>${v.fare}</td>
                        <td>${v.payment_date}</td>
                        <td>${v.keberangkatan}</td>
                        <td>${v.route_name}</td>
                        <td>${v.description}</td>
                        <td>${v.description_expired}</td>
                        <td>${v.tanggal_pengakuan}</td>
                        <td>${v.pendapatan_expired}</td>
                    </tr>
                `;
            });


            return html;
        }





        function route(data) {

            // console.log(data);
            $.ajax({

                type: "post",
                dataType: "json",
                url: "<?php echo site_url() ?>laporan/ticket_expired_v2/getRoute",
                data: "port=" + data.port,
                success: (x) => {

                    var html = "<option value=''>Pilih</option>";

                    if (x.length > 0) {
                        for (var i = 0; i < x.length; i++) {
                            html += "<option value='" + x[i].id + "'>" + x[i].route_name + "</option>";
                        }

                    }

                    $("#route").html(html);
                }
            })
        }

        $("#port").change(() => {
            route({
                "port": $("#port").val()
            });
        })

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });
    });
</script>