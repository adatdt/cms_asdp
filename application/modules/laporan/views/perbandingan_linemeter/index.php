<style type="text/css">
    .judul-tabel-atas tr {
        border-collapse: collapse;
    }

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
</style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>

                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-toolbar" style="margin-bottom: 0px">
                            <div class="row">
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Pelabuhan</span>
                                            <select id="port" class="form-control js-data-example-ajax select2" dir="">
                                                <option value="">Semua</option>
                                                <?php foreach ($port as $key => $value) { ?>
                                                    <option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Tanggal</span>
                                            <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                            </input>
                                            <div class="input-group-addon">s/d</div>
                                            <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                            </input>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">Kelas Layanan</div>
                                            <?php
                                            if ($this->session->userdata('ship_class_id') != '') {
                                                $selected = 'disabled="disabled"';
                                            } else {
                                                $selected = '';
                                            }
                                            ?>
                                            <select id="ship_class" <?php echo $selected ?> class="form-control js-data-example-ajax select2" dir=""><?php echo $class ?></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left: 5px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3" style="padding-right: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Golongan</span>
                                        <select id="golongan" class="form-control js-data-example-ajax select2" dir="">
                                            <option value="">Semua</option>
                                            <?php foreach ($golongan as $key => $value) { ?>
                                                <option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-right: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Keterangan</span>
                                        <select id="ket" class="form-control js-data-example-ajax select2" dir="">
                                            <option value="">Semua</option>
                                            <option value="0">Normal</option>
                                            <option value="1">Overpaid</option>
                                            <option value="2">Underpaid</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-right: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">Jam</div>
                                        <?php echo form_dropdown('time', $time, '', 'id="time" class="form-control select2"'); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div id="ini_replace"></div>

                        <div id="master_tabel" style="display: none">

                            <button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

                            <button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br><br>

                            <table class="tabel full-width" align="center">
                                <tr>
                                    <td rowspan="4" class="no-border-right" style="width: 10%">
                                        <img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
                                    </td>
                                    <td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
                                        REPORT PERBANDINGAN LINEMETER
                                        </span>
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
                                    <td style="border-right: none !important;">PELABUHAN</td>
                                    <td style="border-left: none !important;"><span id="pelabuhanku"></span></td>
                                    <td style="border-right: none !important;">TANGGAL</td>
                                    <td style="border-left: none !important;"><span id="tanggalku"></span></td>
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">KETERANGAN</td>
                                    <td style="border-left: none !important;"><span id="keteranganku"></span> </td>
                                    <td style="border-right: none !important;">GOLONGAN</td>
                                    <td style="border-left: none !important;"><span id="golonganku"></span></td>
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">KELAS LAYANAN</td>
                                    <td style="border-left: none !important;"><span id="kelasku"></span> </td>
                                    <td style="border-right: none !important;"></td>
                                    <td style="border-left: none !important;"><span id="test"></span></td>
                                </tr>
                            </table>
                            <table class="tabel full-width">
                                <tbody id="tr"></tbody>
                            </table>

                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
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

            $("#printerkita").on("click", function(e) {
                port = $("#port").val();
                datefrom = $("#datefrom").val();
                dateto = $("#dateto").val();
                time = $("#time").val();
                ket = $("#ket").val();
                golongan = $("#golongan").val();
                ship_class = $("#ship_class").val();

                if (port != null) {
                    window.location = "<?php echo site_url('laporan/perbandingan_linemeter/get_pdf?port=') ?>" + port +
                        "&datefrom=" + datefrom +
                        "&dateto=" + dateto +
                        "&time=" + time +
                        "&ket=" + ket +
                        "&golongan=" + golongan +
                        "&keteranganku=" + $('#ket').find(":selected").text() +
                        "&pelabuhanku=" + $('#port').find(":selected").text() +
                        "&golonganku=" + $('#golongan').find(":selected").text() +
                        "&ship_classku=" + $('#ship_class').find(":selected").text();
                }

            });

            $("#excelkita").on("click", function(e) {
                port = $("#port").val();
                datefrom = $("#datefrom").val();
                dateto = $("#dateto").val();
                time = $("#time").val();
                ket = $("#ket").val();
                golongan = $("#golongan").val();
                ship_class = $("#ship_class").val();

                if (port != null) {
                    window.location = "<?php echo site_url('laporan/perbandingan_linemeter/get_excel?port=') ?>" + port +
                        "&datefrom=" + datefrom +
                        "&dateto=" + dateto +
                        "&time=" + time +
                        "&ket=" + ket +
                        "&golongan=" + golongan +
                        "&keteranganku=" + $('#ket').find(":selected").text() +
                        "&pelabuhanku=" + $('#port').find(":selected").text() +
                        "&golonganku=" + $('#golongan').find(":selected").text() +
                        "&ship_classku=" + $('#ship_class').find(":selected").text();
                }
            });

            $("#cari").on("click", function(e) {
                $(this).button('loading');
                e.preventDefault();
                
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('laporan/perbandingan_linemeter') ?>",
                    dataType: 'json',
                    data: {
                        port: $("#port").val(),
                        datefrom: $("#datefrom").val(),
                        dateto: $("#dateto").val(),
                        golongan: $("#golongan").val(),
                        ket: $("#ket").val(),
                        time: $("#time").val(),
                        ship_class: $("#ship_class").val(),
                    },

                    success: function(json) {
                        $("#cari").button('reset');
                        if (json.code == 200) {
                            html = "<table class='table table-no-border'>";
                            html += `<tr>
                            <td class='text-center no-border-right' rowspan = '2'> NO </td>
                            <td class='text-center' rowspan = '2'> Tanggal </td>
                            <td class='text-center' rowspan = '2'> Waktu </td>
                            <td class='text-center' rowspan = '2'> Golongan</td>
                            <td class='text-center' rowspan = '2'> Kode Booking</td>
                            <td class='text-center' colspan ='2'>Default Linemeter (m)</td>
                            <td class='text-center' colspan ='2'>Pengisian Linemeter (m)</td>
                            <td class='text-center' colspan ='2'>Realisasi Linemeter (m)</td>
                            <td class='text-center' rowspan = '2'>Keterangan</td></tr>`;
                            html += "<tr>";
                            html += "";
                            html += "<td class='text-center'> Panjang (m) </td>";
                            html += "<td class='text-center'> Lebar (m) </td>";
                            html += "<td class='text-center'> Panjang (m) </td>";
                            html += "<td class='text-center'> Lebar(m) </td>";
                            html += "<td class='text-center'> Panjang (m) </td>";
                            html += "<td class='text-center'> Lebar(m) </td>";
                            html += "</tr>";
                            angka = 65;

                            html += "</table>";
                            html += "<table>";

                            angka = 1;

                            $.each(json.perbandingan, function(i, item) {
                                $("#master_tabel").show();
                                lebarD = 0;
                                panjangD = 0;
                                lebarP = 0;
                                panjangP = 0;
                                lebarR = 0;
                                panjangR = 0;
                                golongan = '-';

                                if (item.name != null) {
                                    golongan = item.name
                                }

                                if (item.lebar_default != null) {
                                    lebarD = formatIDR(item.lebar_default)
                                }

                                if (item.panjang_default != null) {
                                    panjangD = formatIDR(item.panjang_default)
                                }

                                if (item.lebar_pengisian != null) {
                                    lebarP = formatIDR(item.lebar_pengisian)
                                }

                                if (item.panjang_pengisian != null) {
                                    panjangP = formatIDR(item.panjang_pengisian)
                                }

                                if (item.lebar_real != null) {
                                    lebarR = formatIDR(item.lebar_real)
                                }

                                if (item.panjang_real != null) {
                                    panjangR = formatIDR(item.panjang_real)
                                }

                                if (item.keterangan == 0) {
                                    status = "Normal";
                                } else if (item.keterangan == 1) {
                                    status = "Overpaid";
                                } else {
                                    status = "Underpaid";
                                }

                                html += "<tr>";
                                html += "<td class='text-center'>" + angka++ + "</td>";
                                html += "<td class='text-left'>" + item.depart_date + "</td>";
                                html += "<td class='text-left'>" + item.depart_time_start + "</td>";
                                html += "<td class='text-left'>" + golongan + "</td>";
                                html += "<td class='text-center'>" + item.kode_booking + "</td>";
                                html += "<td class='text-right'>" + panjangD + "</td>";
                                html += "<td class='text-right'>" + lebarD + "</td>";
                                html += "<td class='text-right'>" + panjangP + "</td>";
                                html += "<td class='text-right'>" + lebarP + "</td>";
                                html += "<td class='text-right'>" + panjangR + "</td>";
                                html += "<td class='text-right'>" + lebarR + "</td>";
                                html += "<td class='text-right'>" + status + "</td>";
                                html += "</tr>";
                            });

                            html += "</table>";

                            $("#tr").html(html);

                            var cabangku = ": " + $('#port').find(":selected").text();
                            var golonganku = ": " + $('#golongan').find(":selected").text();
                            var keteranganku = ": " + $('#ket').find(":selected").text();
                            var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val())) + "";
                            var kelasku = ": " + $('#ship_class').find(":selected").text();
                            // var team_name = ": " + json.regu;
                            // var lintasanku = json.lintasan;
                            // var status_approve = json.status_approve;

                            // $("#status_approve").html(status_approve);
                            $("#golonganku").html(golonganku);
                            $("#pelabuhanku").html(cabangku);
                            $("#keteranganku").html(keteranganku);
                            $("#tanggalku").html(tanggalku);
                            $("#kelasku").html(kelasku);
                            // $("#reguku").html(team_name);
                            // $("#lintasanku").html(": " + lintasanku.toUpperCase());
                        } else {
                            toastr.warning(json.message, 'Peringatan');
                            document.getElementById('master_tabel').style.display = 'none';
                        }
                    },

                    error: function(result) {
                        alert('error');
                    }

                });
            });

            setTimeout(function() {
                $('.menu-toggler').trigger('click');
            }, 1);

            $(".menu-toggler").click(function() {
                $('.select2').css('width', '100%');
            });

        });
    </script>