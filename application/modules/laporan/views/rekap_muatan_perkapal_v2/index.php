<style type="text/css">
    .judul-tabel-atas tr{
        border-collapse: collapse;
    }

    .tabel {
        border-collapse: collapse;
    }
    .tabel th, .tabel td {
        padding: 5px 5px;
        border: 1px solid #000;
    }
    .tabel th {
        font-weight: normal;
    }
    .tabel-no-border tr {
        border: 1px solid #000;
    }

    .tabel-no-border th, .tabel-no-border td {
        padding: 5px 5px;
        border: 0px;
    }     
    .tabel-no-border-new tr {
        border: 0px solid #000;
    }

    .tabel-no-border-new th, .tabel-no-border td {
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

                                            <?php
                                                    echo form_dropdown("port",$port,"",' id="port" class="form-control js-data-example-ajax select2"  ')                                              
                                                ?>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Perusahaan</span>                                            
                                                <?php
                                                    echo form_dropdown("company",$ship_company,"",' id="company" class="form-control js-data-example-ajax select2" ')                                              
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">Kapal</span>
                                            <?php echo form_dropdown("ship",$ship,"",' id="ship" class="form-control js-data-example-ajax select2" ') ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-right: 10px;">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">Kelas Layanan</div>
                                            <select id="ship_class" class="form-control js-data-example-ajax select2" dir="">
                                                <option value="">Semua</option>
                                                <?php foreach ($class as $key => $value) { ?>
                                                    <option value="<?=$this->enc->encode($value->id) ?>"><?=$value->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">Tanggal Shift</span>
                                        <input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
                                        <div class="input-group-addon">s/d</div>
                                        <input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>"></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-left: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">Dermaga </div>
                                        <select id="dock" class="form-control js-data-example-ajax select2" dir="">
                                            <option value="">Semua</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-left: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">SHIFT </div>
                                        <select id="shift" class="form-control js-data-example-ajax select2" dir="">
                                            <option value="">Semua</option>
                                            <?php foreach ($shift as $key => $value) { ?>
                                                <option value="<?=$this->enc->encode($value->id) ?>"><?=$value->shift_name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- tipe tiket untuk laporan tiket manual, tapi tiket manual belum jelas dan belum deploy untuk tiket manual -->
<!--                             
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">TIPE TIKET </div>
                                        <?= form_dropdown("ticketType",$ticketType,"",' id="ticketType" class="form-control js-data-example-ajax select2"') ?>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-md-1" style="padding-left: 0px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div id="ini_replace"></div>

                    <div id="master_tabel" style="display: none">
                        <?= generate_button('laporan/rekap_muatan_perkapal_v2', 'download_pdf', '<button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button>') ?>
					    <?= generate_button('laporan/rekap_muatan_perkapal_v2', 'download_excel', '<button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button>') ?>
												

                        <!-- <button id="printerkita" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o" style="color: #ea5460"></i> PDF</button> -->
                        <!-- <button id="excelkita" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o" style="color: #ea5460"></i> EXCEL</button><br><br> -->

                        <table class="tabel full-width" align="center"> 
                            <tr>
                                <td rowspan="4" class="no-border-right" style="width: 10%">
                                    <img src="<?php echo base_url();?>assets/img/asdp-logo2.jpg" style="width:100px; height: auto"> 
                                </td>
                                <td rowspan="4" class="center bold" style="width: 60%;font-size: 14pt; line-height: 1.5">
                                    FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP</span>
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
                                <td style="border-right: none !important;width: 15%">NAMA KAPAL</td>
                                <td style="border-left: none !important;width: 35%"><span id="ship_name"></span></td>
                                <td style="border-right: none !important;">LINTASAN</td>
                                <td style="border-left: none !important;"><span id="lintasanku"></td>                        
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">PERUSAHAAN</td>
                                    <td style="border-left: none !important;"><span id="ship_company"></span></td>
                                    <td style="border-right: none !important;">DERMAGA</td>
                                    <td style="border-left: none !important;"><span id="dermagaku"></span></td>
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">CABANG</td>
                                    <td style="border-left: none !important;"><span id="cabangku"></span> </td>
                                    <td style="border-right: none !important;">TANGGAL</td>
                                    <td style="border-left: none !important;"><span id="tanggalku"></span></td>
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">PELABUHAN</td>
                                    <td style="border-left: none !important;"><span id="pelabuhanku"></span> </td>
                                    <td style="border-right: none !important;">JAM</td>
                                    <td style="border-left: none !important;"><span id="jamku"></span></td>
                                </tr>
                                <tr>
                                    <td style="border-right: none !important;">JUMLAH TRIP</td>
                                    <td style="border-left: none !important;">: <span id="jumlah_trip"></span></td>
                                    <td style="border-right: none !important;">STATUS</td>
                                    <td style="border-left: none !important;">: <b><span id="status_approve"></span></b></td>
                                </tr>
                                <tr>
                                    <!-- <td style="border-right: none !important;">TIPE TIKET</td>
                                    <td style="border-left: none !important;"> <span id="ticketTypeku"></span></td> -->
                                    <td style="border-right: none !important;"></td>
                                    <td style="border-left: none !important;"> </td>
                                    <td style="border-right: none !important;"></td>
                                    <td style="border-left: none !important;"></td>
                                </tr>                                
                        </table>

                            <br>

                            <table class="tabel full-width">
                                <tbody id="tr"></tbody>
                            </table>
                        </div>
                    </div>
                    <br></div>
                </div>        
            </div>
        </div>
<script type="text/javascript">

  function formatIDR_2(angka) {
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');

    return ribuan;
}
  
    function getDock()
    {
        $.ajax({
                method: "GET",
                url: "rekap_muatan_perkapal_v2/get_dock/"+$("#port").val(),
                type: "html"
            })
            .done(function( msg ) {
                $("#dock").html(msg);
            });
            // console.log("haloo")
    }
    jQuery(document).ready(function () {
        getDock()
        $("#port").change(function() {
            getDock()
        });

        // $( "#port" ).on( "load", function() {
        //     // Handler for `load` called.
        //     console.log("halo")
        // } );


        $("#company").change(function() {
            $.ajax({
                method: "GET",
                url: "rekap_muatan_perkapal_v2/get_ship/"+$("#company").val(),
                type: "html"
            })
            .done(function( msg ) {
                $("#ship").html(msg);
            });
        });

        $('#datefrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateto').datepicker('setStartDate', e.date)
        });

        $('#dateto').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#datefrom').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#datefrom').datepicker('setEndDate', e.date)
        });

        $("#printerkita").on("click",function(e){
            port = $("#port").val();
            datefrom = $("#datefrom").val();
            dateto = $("#dateto").val();
            shift = $("#shift").val();
            ship_class = $("#ship_class").val();
            ship_company = $("#company").val();
            ship = $("#ship").val();
            dock = $("#dock").val();
            ticketType = $("#ticketType").val();

            pelabuhanku = $('#port').find(":selected").text();
            shiftku = $('#shift').find(":selected").text();
            ship_classku = $('#ship_class').find(":selected").text();
            ship_companyku = $('#company').find(":selected").text();
            shipku = $('#ship').find(":selected").text();
            dockku = $('#dock').find(":selected").text();
            ticketTypeku = $('#ticketType').find(":selected").text();

            window.location = "<?php echo site_url('laporan/rekap_muatan_perkapal_v2/get_pdf?port=') ?>"+port+
            "&datefrom=" +datefrom+
            "&dateto=" +dateto+
            "&shift="+shift+
            "&ship_class="+ship_class+
            "&ship_company="+ship_company+
            "&ship="+ship+
            "&dock="+dock+
            "&pelabuhanku="+pelabuhanku+
            "&shiftku="+shiftku+
            "&ship_classku="+ship_classku+
            "&ship_companyku="+ship_companyku+
            "&shipku="+shipku+
            "&dockku="+dockku+
            "&ticketType="+ticketType+
            "&ticketTypeku="+ticketTypeku
        });

        $("#excelkita").on("click",function(e){
            port = $("#port").val();
            datefrom = $("#datefrom").val();
            dateto = $("#dateto").val();
            shift = $("#shift").val();
            ship_class = $("#ship_class").val();
            ship_company = $("#company").val();
            ship = $("#ship").val();
            dock = $("#dock").val();
            ticketType = $("#ticketType").val();

            pelabuhanku = $('#port').find(":selected").text();
            shiftku = $('#shift').find(":selected").text();
            ship_classku = $('#ship_class').find(":selected").text();
            ship_companyku = $('#company').find(":selected").text();
            shipku = $('#ship').find(":selected").text();
            dockku = $('#dock').find(":selected").text();
            ticketTypeku = $('#ticketType').find(":selected").text();

            window.location = "<?php echo site_url('laporan/rekap_muatan_perkapal_v2/get_excel?port=') ?>"+port+
            "&datefrom=" +datefrom+
            "&dateto=" +dateto+
            "&shift="+shift+
            "&ship_class="+ship_class+
            "&ship_company="+ship_company+
            "&ship="+ship+
            "&dock="+dock+
            "&pelabuhanku="+pelabuhanku+
            "&shiftku="+shiftku+
            "&ship_classku="+ship_classku+
            "&ship_companyku="+ship_companyku+
            "&shipku="+shipku+
            "&dockku="+dockku+
            "&ticketType="+ticketType+
            "&ticketTypeku="+ticketTypeku
        });

        $("#cari").on("click",function(e){
            $(this).button('loading');
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('laporan/rekap_muatan_perkapal_v2/detail') ?>",
                dataType: 'json',
                data: { 
                    port: $("#port").val(),
                    ship_company: $("#company").val(),
                    ship: $("#ship").val(),
                    ship_class: $("#ship_class").val(),
                    datefrom: $("#datefrom").val(),
                    dateto: $("#dateto").val(),
                    dock: $("#dock").val(),
                    shift: $("#shift").val(),
                    ticketType: $("#ticketType").val(),
                },

            success: function(json) {
                $("#cari").button('reset');
                if (json.code == 200) {

                    html = "<table class='table table-no-border'>";
                    html += "<tr>";
                    html += "<td class='text-center no-border-right'> NO </td>";
                    html += "<td class='text-center'> JENIS TIKET </td>";
                    html += "<td class='text-center'> TARIF </td>";
                    html += "<td class='text-center'> PRODUKSI </td>";
                    html += "<td class='text-center'> PENDAPATAN </td>";
                    html += "<td class='text-center'> KETERANGAN</td>";
                    html += "</tr>";
                    html += "<tr><th colspan='6'>1. PENUMPANG</th><tr>";
                    angka = 1;
                    total_produksi_penumpang = 0;
                    total_pendapatan_penumpang = 0;
                    admfee_penumpang = 0;
                    jasa_kepil = json.jasa_kepil;
                    jasa_sandar = json.dock_fare;
                    jumlah_trip = json.jumlah_trip;

                    $.each(json.penumpang, function(i, item) {
                        $("#master_tabel").show();
                        total_produksi_penumpang += Number(item.produksi);
                        total_pendapatan_penumpang += Number(item.pendapatan);
                        admfee_penumpang += Number(item.adm_fee);

                        html += "<tr>";
                        html += "<td class='text-center'>"+angka++ +"</td>";
                        html += "<td class='text-left'>" + item.golongan + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.harga) + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.produksi) + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.pendapatan) + "</td>";
                        html += "<td class='text-right'></td>";
                        html += "</tr>";
                    });

                    html += "</table>";
                    html += "<table>";
                    html += "<tr><td colspan='3' class='text-center'><b>Sub Jumlah</b></td><td class='text-right'>" +formatIDR_2(total_produksi_penumpang)+ "</td><td class='text-right'>" +formatIDR_2(total_pendapatan_penumpang)+ "</td><td></td></tr>";

                    html += "<tr><th colspan='6'>2. KENDARAAN</th><tr>";
                    total_produksi_kendaraan = 0;
                    total_pendapatan_kendaraan = 0;
                    admfee_kendaraan = 0;

                    angka = 1;

                    $.each(json.kendaraan, function(i, item) {
                        $("#master_tabel").show();
                        total_produksi_kendaraan += Number(item.produksi);
                        total_pendapatan_kendaraan += Number(item.pendapatan);
                        admfee_kendaraan += Number(item.adm_fee);

                        html += "<tr>";
                        html += "<td class='text-center'>" + angka++ + "</td>";
                        html += "<td class='text-left'>" + item.golongan + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.harga) + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.produksi) + "</td>";
                        html += "<td class='text-right'>" + formatIDR_2(item.pendapatan) + "</td>";
                        html += "<td class='text-right'></td>";
                        html += "</tr>";
                    });

                    jumlah_produksi = Number(total_produksi_penumpang+total_produksi_kendaraan);
                    jumlah_pendapatan = Number(total_pendapatan_penumpang+total_pendapatan_kendaraan);
                    admin_tiket = Number(admfee_penumpang)+Number(admfee_kendaraan);
                    bea_pelabuhan = Number(admin_tiket)+Number(jasa_sandar)+Number(jasa_kepil);

                    html += "<tr style='font-size:1em;'><td colspan='3' class='text-center'> <b>Sub Jumlah</b></td><td class='text-right'>" +formatIDR_2(total_produksi_kendaraan)+ "</td><td class='text-right'>" +formatIDR_2(total_pendapatan_kendaraan)+ "</td><td></td></tr>";

                    html += "<tr><td colspan='3' class='text-center'> <b>Jumlah (Penumpang + Kendaraan)</b></td><td class='text-right'>" +formatIDR_2(jumlah_produksi)+ "</td><td class='text-right'>" +formatIDR_2(jumlah_pendapatan)+ "</td><td></td></tr>";

                    html += "<tr><th colspan='6'>3. BEA JASA PELABUHAN</th><tr>";

                    html += "<tr><td class='text-center'>a</td><td class='text-left'>Jasa Adm. Tiket</td><td class='text-right' colspan='3'>" +formatIDR_2(admin_tiket)+ "</td><td></td></tr>";

                    html += "<tr><td class='text-center'>b</td><td class='text-left'>Jasa Sandar</td><td class='text-right' colspan='3'>" +formatIDR_2(jasa_sandar)+ "</td><td></td></tr>";

                    html += "<tr><td class='text-center'>c</td><td class='text-left'>Jasa Kepil</td><td class='text-right' colspan='3'>" +formatIDR_2(jasa_kepil)+ "</td><td></td></tr>";

                    html += "<tr><td colspan='2' class='text-center'><b>Jumlah</b></td><td class='text-right' colspan='3'>" +formatIDR_2(bea_pelabuhan)+ "</td><td></td></tr>";

                    html += "</table>";

                    $( "#tr" ).html(html);

                    var pelabuhanku = ": " + $('#port').find(":selected").text();
                    var ship_company = ": " + $('#company').find(":selected").text();
                    var ship_name = ": " + $('#ship').find(":selected").text();
                    var dermagaku = ": " + $('#dock').find(":selected").text();
                    var ticketTypeku = ": " + $('#ticketType').find(":selected").text();

                    var status_approve = json.status_approve;
                    var jamku = ": " + json.jam;
                    var lintasan = ": " + json.lintasan;

                    var tanggalku = ": " + $.datepicker.formatDate("d M yy", new Date($('#datefrom').val())) + " - " + $.datepicker.formatDate("d M yy", new Date($('#dateto').val()));

                    $( "#status_approve").html(status_approve);
                    $( "#jumlah_trip").html(jumlah_trip);
                    $( "#ship_name" ).html(ship_name);
                    $( "#ship_company" ).html(ship_company);
                    $( "#cabangku" ).html(pelabuhanku);
                    $( "#dermagaku" ).html(dermagaku);
                    $( "#pelabuhanku" ).html(pelabuhanku);
                    $( "#tanggalku" ).html(tanggalku);
                    $( "#jamku" ).html(jamku);
                    $( "#lintasanku" ).html(": " + json.lintasan);
                    $( "#ticketTypeku" ).html(ticketTypeku);
                }else{
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