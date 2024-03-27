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
                                                <!-- <option value="">Semua</option> -->
                                                <?php foreach ($port as $key => $value) { ?>
                                                    <option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Tanggal</span>
                                            <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                            </input>
                                            <!-- <div class="input-group-addon">s/d</div>
                                            <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                            </input> -->
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
                                        REKAPITULASI RINCIAN GOLONGAN KENDARAAN TERHADAP PENGGUNAAN LINEMETER PER JAM
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
                                <!-- <tr>
                                    <td style="border-right: none !important;width: 15%">CABANG</td>
                                    <td style="border-left: none !important;width: 35%"><span id="cabangku"></span></td>
                                    <td style="border-right: none !important;">SHIFT</td>
                                    <td style="border-left: none !important;"><span id="shiftku"></td>
                                </tr> -->
                                <tr>
                                    <td style="border-right: none !important;">PELABUHAN</td>
                                    <td style="border-left: none !important;"><span id="pelabuhanku"></span></td>
                                    <!-- <td style="border-right: none !important;">REGU</td>
                                    <td style="border-left: none !important;"><span id="reguku"></span></td> -->
                                </tr>
                                <tr>
                                    <!-- <td style="border-right: none !important;">LINTASAN</td>
                                    <td style="border-left: none !important;"><span id="lintasanku"></span> </td> -->
                                    <td style="border-right: none !important;">TANGGAL</td>
                                    <td style="border-left: none !important;"><span id="tanggalku"></span></td>
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
                // endDate: '+1d',
            });
            // .on('changeDate', function(e) {
            //     $('#dateto').datepicker('setStartDate', e.date)
            // });

            // $('#dateto').datepicker({
            //     format: 'yyyy-mm-dd',
            //     changeMonth: true,
            //     changeYear: true,
            //     autoclose: true,
            //     startDate: $('#datefrom').val(),
            //     endDate: new Date(),
            // }).on('changeDate', function(e) {
            //     $('#datefrom').datepicker('setEndDate', e.date)
            // });

            $("#printerkita").on("click", function(e) {
                port = $("#port").val();
                datefrom = $("#datefrom").val();
                // dateto = $("#dateto").val();
                // petugas = $("#petugas").val();
                time = $("#time").val();
                ship_class = $("#ship_class").val();

                if (port != null) {
                    window.location = "<?php echo site_url('laporan/rekap_kendaraan_linemeter/get_pdf?port=') ?>" + port +
                        "&datefrom=" + datefrom +
                        // "&dateto=" + dateto +
                        "&time=" + time +
                        "&ship_class=" + ship_class +
                        "&cabangku=" + $('#port').find(":selected").text() +
                        "&pelabuhanku=" + $('#port').find(":selected").text() +
                        // "&shiftku=" + $('#shift').find(":selected").text() +
                        "&ship_classku=" + $('#ship_class').find(":selected").text();
                }
            });

            $("#excelkita").on("click", function(e) {
                port = $("#port").val();
                datefrom = $("#datefrom").val();
                // dateto = $("#dateto").val();
                // petugas = $("#petugas").val();
                time = $("#time").val();
                ship_class = $("#ship_class").val();

                if (port != null) {
                    window.location = "<?php echo site_url('laporan/rekap_kendaraan_linemeter/get_excel?port=') ?>" + port +
                        "&datefrom=" + datefrom +
                        // "&dateto=" + dateto +
                        "&time=" + time +
                        // "&ship_class=" + ship_class +
                        // "&cabangku=" + $('#port').find(":selected").text() +
                        "&pelabuhanku=" + $('#port').find(":selected").text() +
                        // "&shiftku=" + $('#shift').find(":selected").text() +
                        "&ship_classku=" + $('#ship_class').find(":selected").text();
                }
            });

            $("#cari").on("click", function(e) {
                $(this).button('loading');
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('laporan/rekap_kendaraan_linemeter') ?>",
                    dataType: 'json',
                    data: {
                        port: $("#port").val(),
                        datefrom: $("#datefrom").val(),
                        // dateto: $("#dateto").val(),
                        // regu: $("#regu").val(),
                        // petugas: $("#petugas").val(),
                        time: $("#time").val(),
                        ship_class: $("#ship_class").val(),
                    },

                    success: function(json) {
                        // console.log(time);
                        $("#cari").button('reset');
                        if (json.code == 200) {
                            html = "<table class='table table-no-border'>";
                            html += "<tr>";
                            html += "<td class='text-center no-border-right'> NO </td>";
                            html += "<td class='text-center'> TANGGAL </td>";
                            html += "<td class='text-center'> WAKTU </td>";
                            html += "<td class='text-center'> GOLONGAN </td>";
                            html += "<td class='text-center'> PRODUKSI (Lbr)</td>";
                            html += "<td class='text-center'> LEBAR (m) </td>";
                            html += "<td class='text-center'> PANJANG (m) </td>";
                            html += "<td class='text-center'> LUAS (m2) </td>";
                            html += "<td class='text-center'> JUMLAH (m2) </td>";
                            html += "</tr>";
                            angka = 65;
                            total_produksi_penumpang = 0;
                            total_pendapatan_penumpang = 0;

                            html += "</table>";
                            html += "<table>";

                            total_penggunaan_linemeter = 0;
                            total_pendapatan_kendaraan = 0;
                            total_ketersediaan_linemeter = 0;
                            total_sisa_ketersediaan_linemeter = 0;
                            persentase = 0;

                            angka = 1;

                            $.each(json.kendaraan, function(i, item) {
                                $("#master_tabel").show();
                                total_penggunaan_linemeter += Number(item.jumlah);
                                total_ketersediaan_linemeter += Number(item.total_lm);
                                total_sisa_ketersediaan_linemeter = Number(total_ketersediaan_linemeter) - Number(total_penggunaan_linemeter);
                                persentase = Number(total_penggunaan_linemeter) / Number(total_ketersediaan_linemeter) * 100;
                                per = persentase.toFixed(2);
                                hargaku = "";
                                produksiku = 0;
                                pendapatanku = 0;
                                lebarku = 0;
                                panjangku = 0;
                                jumlahku = 0;
                                luasku = 0;

                                if (item.lebar != null) {
                                    lebarku = formatIDR(item.lebar)
                                }

                                if (item.produksi != null) {
                                    produksiku = formatIDR(item.produksi)
                                }

                                if (item.panjang != null) {
                                    panjangku = formatIDR(item.panjang)
                                }

                                if (item.jumlah != null) {
                                    jumlahku = formatIDR(item.jumlah)
                                }

                                if (item.golongan == null) {
                                    golku = "-";
                                } else {
                                    golku = (item.golongan)
                                }


                                if (item.luas != null) {
                                    luasku = formatIDR(item.luas)
                                }

                                if (persentase >= 100) {
                                    status = "OVER";
                                } else if (persentase <= 100) {
                                    status = "UNDER";
                                }

                                html += "<tr>";
                                html += "<td class='text-center'>" + angka++ + "</td>";
                                html += "<td class='text-left'>" + item.depart_date + "</td>";
                                html += "<td class='text-left'>" + item.depart_time + "</td>";
                                html += "<td class='text-left'>" + golku + "</td>";
                                html += "<td class='text-right'>" + produksiku + "</td>";
                                html += "<td class='text-right'>" + lebarku + "</td>";
                                html += "<td class='text-right'>" + panjangku + "</td>";
                                html += "<td class='text-right'>" + luasku + "</td>";
                                html += "<td class='text-right'>" + jumlahku + "</td>";
                                html += "</tr>";
                            });

                            jumlah_pendapatan = Number(total_pendapatan_penumpang + total_pendapatan_kendaraan);

                            html += "<tr style='font-size:1em;'><td colspan='8'> <b>TOTAL PENGGUNAAN LINEMETER PER JAM</b></td><td class='text-right'>" + formatIDR(total_penggunaan_linemeter) + "</td></tr>";
                            html += "<tr style='font-size:1em;'><td colspan='8'> <b>TOTAL KETERSEDIAAN LINEMETER PER JAM</b></td><td class='text-right'>" + formatIDR(total_ketersediaan_linemeter) + "</td></tr>";
                            html += "<tr style='font-size:1em;'><td colspan='8'> <b>SISA KETERSEDIAAN LINEMETER</b></td><td class='text-right'>" + formatIDR(total_sisa_ketersediaan_linemeter) + "</td></tr>";
                            html += "<tr style='font-size:1em;'><td colspan='8'> <b>PERSENTASE</b></td><td class='text-right'>" + (per) + " % " + "</td></tr>";
                            html += "<tr style='font-size:1em;'><td colspan='8'> <b>STATUS</b></td><td class='text-right'>" + (status) + "</td></tr>";
                            html += "</table>";

                            $("#tr").html(html);

                            var cabangku = ": " + $('#port').find(":selected").text();
                            // var shiftku = ": " + $('#shift').find(":selected").text();
                            var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + "";

                            var kelaskuu = $('#ship_class').find(":selected").text();
                            // var team_name = ": " + json.regu;
                            // var lintasanku = json.lintasan;
                            // var status_approve = json.status_approve;

                            // $("#status_approve").html(status_approve);
                            // $("#cabangku").html(cabangku);
                            $("#pelabuhanku").html(cabangku);
                            // $("#shiftku").html(shiftku);
                            $("#tanggalku").html(tanggalku);
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