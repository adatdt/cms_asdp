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
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Tanggal Approved</span>
                                            <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly></input>
                                            <div class="input-group-addon">s/d</div>
                                            <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly></input>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Pelabuhan</span>
                                            <select id="port" class="form-control select2" dir="">
                                                <option value="">Semua</option>
                                                <?php foreach ($port as $key => $value) { ?>
                                                    <option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="table-toolbar" style="margin-bottom: 0px">
                            <div class="row">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">SALES CHANNEL</div>
                                            <select id="channel" class="form-control select2" dir="">
                                                <option value="">Semua</option>
                                                <option value="ifcs">IFCS</option>
                                                <option value="web">ONLINE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="ini_replace"></div>

                <div id="master_tabel" style="display: none">

                    <div class="form-inline">
                        <button id="pdfDownload" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>

                        <button id="excelDownload" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br>
                    </div><br>

                    <table class="tabel full-width" align="center">
                        <tr>
                            <td rowspan="4" class="no-border-right" style="width: 10%">
                                <img src="<?php echo base_url(); ?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto">
                            </td>
                            <td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
                                <span> LAPORAN TIKET REFUND</span>
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
                            <td style="border-right:none; width: 15%">Tanggal</td>
                            <td style="border-right:none; border-left:none; width: 1%">:</td>
                            <td style="border-left: none" id="tTanggal"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Pelabuhan</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tPelabuhan"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Kelas Layanan</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tKLayanan"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Sales Channel</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none" id="tChannel"></td>
                        </tr>
                        <tr>
                            <td style="border-right:none">Payment Channel</td>
                            <td style="border-right:none; border-left:none">:</td>
                            <td style="border-left: none">(Semua/VA Permata/VA BNI/Alfamart/Yo-Mart/PT Pos/...)</td>
                        </tr>
                    </table>

                    <table class="tabel full-width">
                        <tbody id="tr"></tbody>
                    </table>
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

        $('#cari').click(function() {
            getData();
        });

        $("#excelDownload").click(function() {
            var datefrom = $("#datefrom").val(),
                dateto = $("#dateto").val(),
                port = $("#port").val(),
                ship_class = $("#ship_class").val(),
                channel = $("#channel").val(),
                portname = $("#port option:selected").text(),
                ship_classku = $("#ship_class option:selected").text();
            var url_download = `datefrom=${datefrom}&dateto=${dateto}&port=${port}&ship_class=${ship_class}&channel=${channel}&portname=${portname}&ship_classku=${ship_classku}`;
            window.open("<?php echo site_url('laporan/ticket_refund/download_excel?') ?>" + url_download);
        });

        $("#pdfDownload").click(function() {
            var datefrom = $("#datefrom").val(),
                dateto = $("#dateto").val(),
                port = $("#port").val(),
                ship_class = $("#ship_class").val(),
                channel = $("#channel").val(),
                portname = $("#port option:selected").text(),
                ship_classku = $("#ship_class option:selected").text();
            var url_download = `datefrom=${datefrom}&dateto=${dateto}&port=${port}&ship_class=${ship_class}&channel=${channel}&portname=${portname}&ship_classku=${ship_classku}`;
            window.open("<?php echo site_url('laporan/ticket_refund/download_pdf?') ?>" + url_download);
        });

        // getData();

        function getData() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('laporan/ticket_refund') ?>",
                dataType: 'json',
                data: {
                    datefrom: $("#datefrom").val(),
                    dateto: $("#dateto").val(),
                    port: $("#port").val(),
                    ship_class: $("#ship_class").val(),
                    channel: $("#channel").val(),
                },
                beforeSend: function() {
                    $("#cari").button('loading');
                    $("#master_tabel").hide();
                },
                success: function(data) {
                    var tglnya = $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

                    $("#tr").html(create_table(data));
                    $("#master_tabel").show();
                    $("#tTanggal").html(tglnya);
                    $("#tPelabuhan").html($("#port option:selected").text());
                    $("#tKLayanan").html($("#ship_class option:selected").text());
                    $("#tChannel").html($("#channel option:selected").text());
                    $("#cari").button('reset');
                },
                error: function(error) {
                    toastr.error('Error', 'Peringatan');
                }
            });
        }

        function create_table(data) {
            // var json = JSON.parse(data);
            var html;
            var trtFare = 0,
                trtProduksi = 0,
                trtAdmFee = 0,
                trtRefundFee = 0,
                trtChargeAmount = 0,
                trtBTF = 0,
                trtTotalAmount = 0;

            for (var num = 0; num < data.length; num++) {
                var dataChannel = data[num];
                var totFare = 0,
                    totProduksi = 0,
                    totAdmFee = 0,
                    totRefundFee = 0,
                    totChargeAmount = 0,
                    totBTF = 0,
                    totTotalAmount = 0;
                // Title Table
                html += `
                    <tr class="bold"><td colspan="8">${(num+10).toString(36).toUpperCase()}. ${dataChannel.title}</td></tr> 
                `;

                // Header Table
                html += `
                    <tr class="bold center">
                        <td rowspan="2">Uraian</td>
                        <td rowspan="2">Tarif</td>
                        <td rowspan="2">Produksi</td>
                        <td colspan="3">Biaya Administrasi + Refund</td>
                        <td rowspan="2">Charge Bank</td>
                        <td rowspan="2">Pengembalian</td>
                    </tr>
                    <tr class="bold center">
                        <td>Biaya Admin</td>
                        <td>Biaya Refund</td>
                        <td>Total</td>
                    </tr>
                `;

                // Data Table
                $.each(dataChannel.data, function(k, v) {
                    // Data Title
                    html += `<tr class="bold"><td colspan="8">${v.title}</td></tr>`;

                    // Data Row
                    var stFare = 0,
                        stProduksi = 0,
                        stAdmFee = 0,
                        stRefundFee = 0,
                        stChargeAmount = 0,
                        stBTF = 0,
                        stTotalAmount = 0;

                    $.each(v.data, function(kc, vc) {
                        stFare += Number(vc.fare);
                        stProduksi += Number(vc.produksi);
                        stAdmFee += Number(vc.adm_fee);
                        stRefundFee += Number(vc.refund_fee);
                        stChargeAmount += Number(vc.charge_amount);
                        stBTF += Number(vc.bank_transfer_fee);
                        stTotalAmount += Number(vc.total_amount);

                        html += `
                            <tr>
                                <td>${vc.golongan}</td>
                                <td class="right">${formatIDR(vc.fare)}</td>
                                <td>${vc.produksi}</td>
                                <td class="right">${formatIDR(vc.adm_fee)}</td>
                                <td class="right">${formatIDR(vc.refund_fee)}</td>
                                <td class="right">${formatIDR(vc.charge_amount)}</td>
                                <td class="right">${formatIDR(vc.bank_transfer_fee)}</td>
                                <td class="right">${formatIDR(vc.total_amount)}</td>
                            </tr>
                        `;
                    });

                    // SubTotal
                    html += `
                        <tr class="bold">
                            <td>Subtotal</td>
                            <td class="right">${(isNaN(stFare)) ? 0 : formatIDR(stFare)}</td>
                            <td>${isNaN(stProduksi) ? 0 : stProduksi}</td>
                            <td class="right">${(isNaN(stAdmFee)) ? 0 : formatIDR(stAdmFee)}</td>
                            <td class="right">${(isNaN(stRefundFee)) ? 0 : formatIDR(stRefundFee)}</td>
                            <td class="right">${(isNaN(stChargeAmount)) ? 0 : formatIDR(stChargeAmount)}</td>
                            <td class="right">${(isNaN(stBTF)) ? 0 : formatIDR(stBTF)}</td>
                            <td class="right">${(isNaN(stTotalAmount)) ? 0 : formatIDR(stTotalAmount)}</td>
                        </tr>
                    `;

                    totFare += Number(stFare);
                    totProduksi += Number(stProduksi);
                    totAdmFee += Number(stAdmFee);
                    totRefundFee += Number(stRefundFee);
                    totChargeAmount += Number(stChargeAmount);
                    totBTF += Number(stBTF);
                    totTotalAmount += Number(stTotalAmount);

                });

                html += `
                    <tr class="bold">
                        <td>Total ${dataChannel.title}</td>
                        <td class="right">${(isNaN(totFare)) ? 0 : formatIDR(totFare)}</td>
                        <td>${isNaN(totProduksi) ? 0 : totProduksi}</td>
                        <td class="right">${(isNaN(totAdmFee)) ? 0 : formatIDR(totAdmFee)}</td>
                        <td class="right">${(isNaN(totRefundFee)) ? 0 : formatIDR(totRefundFee)}</td>
                        <td class="right">${(isNaN(totChargeAmount)) ? 0 : formatIDR(totChargeAmount)}</td>
                        <td class="right">${(isNaN(totBTF)) ? 0 : formatIDR(totBTF)}</td>
                        <td class="right">${(isNaN(totTotalAmount)) ? 0 : formatIDR(totTotalAmount)}</td>
                    </tr>
                `;

                trtFare += Number(totFare);
                trtProduksi += Number(totProduksi);
                trtAdmFee += Number(totAdmFee);
                trtRefundFee += Number(totRefundFee);
                trtChargeAmount += Number(totChargeAmount);
                trtBTF += Number(totBTF);
                trtTotalAmount += Number(totTotalAmount);

            }

            html += `
                <tr class="bold">
                    <td>Total Refund</td>
                    <td class="right">${(isNaN(trtFare)) ? 0 : formatIDR(trtFare)}</td>
                    <td>${isNaN(trtProduksi) ? 0 : trtProduksi}</td>
                    <td class="right">${(isNaN(trtAdmFee)) ? 0 : formatIDR(trtAdmFee)}</td>
                    <td class="right">${(isNaN(trtRefundFee)) ? 0 : formatIDR(trtRefundFee)}</td>
                    <td class="right">${(isNaN(trtChargeAmount)) ? 0 : formatIDR(trtChargeAmount)}</td>
                    <td class="right">${(isNaN(trtBTF)) ? 0 : formatIDR(trtBTF)}</td>
                    <td class="right">${(isNaN(trtTotalAmount)) ? 0 : formatIDR(trtTotalAmount)}</td>
                </tr>
            `;


            return html;
        }


        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $(".menu-toggler").click(function() {
            $('.select2').css('width', '100%');
        });
    });
</script>