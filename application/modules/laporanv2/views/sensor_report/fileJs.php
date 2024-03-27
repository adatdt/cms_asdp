<style type="text/css">
    .pad-top{padding-top: 5px;}
    .textboxid
    {
        height:100%;
        width:160px;
        font-size:12pt;
        padding-left: 5px;
        margin-left: 10px;
    }
    #ex1 {
        width:200px;
        display:inline-block;
        /* margin: auto;
        border: 3px solid #73AD21; */
    }
    .borr {
        border:1px solid;
        width:150px;
        height: 28px;
        padding-left: 5px;
        padding-top: 2px;
        font-size:11pt;
    }
    .borr2 {
        border:1px solid;
        width:180px;
        height: 28px;
        padding-left: 5px;
        padding-top: 2px;
        font-size:11pt;
    }
</style>
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home . '</a>'; ?>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-0 days")); $mingdep=date('Y-m-d',strtotime("+7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $btn_add; ?>
                        <?php if ($btn_excel) {?>
                        <button  class="btn btn-sm btn-warning download" style="padding-left: 5px" id="download_excel">Download Excel</button>
                        <?php } ?>
                    </div>
                    <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $import_excel; ?></div>
                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

														<div class="col-md-2">
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

												<div class="col-md-4" style="padding-right: 5px;padding-left: 0px;">
													<div class="input-group select2-bootstrap-prepend pad-top">
															<span class="input-group-addon">Tanggal </span>
															<input type="text" autocomplete="off" id="datefrom" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly></input>
															<div class="input-group-addon">s/d</div>
															<input type="text" autocomplete="off" id="dateto" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly></input>
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="input-group ">
															<span class="input-group-addon">Kelas Layanan</span>
															<select id="class" class="form-control select2" dir="">
																<option value="">Semua</option>
																<?php foreach ($class as $key => $value) { ?>
																	<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->name ?></option>
																<?php } ?>
															</select>
													</div> 
												</div>
                                               
											  <div class="col-md-2">
													<div class="input-group ">
															<span class="input-group-addon">Shift</span>
															<select id="shift" class="form-control select2" dir="">
																<option value="">Semua</option>
																<?php foreach ($shift as $key => $value) { ?>
																	<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->shift_name ?></option>
																<?php } ?>
															</select>
													</div>
												</div>
                        
												<div class="col-md-2">
													<div class="input-group ">
															<span class="input-group-addon">Regu</span>
															<select id="regu" class="form-control select2" dir="">
																<option value="">Semua</option>
															</select>
													</div>
												</div>
                                                
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                
														<div class="col-md-2">
															<div class="input-group ">
																	<span class="input-group-addon">Petugas</span>
																	<select id="petugas" class="form-control select2" dir="">
																		<option value="">Semua</option>
																		<?php foreach ($petugas as $key => $value) { ?>
																			<option value="<?= $this->enc->encode($value->id) ?>"><?= $value->first_name . " " . $value->last_name ?></option>
																		<?php } ?>
																	</select>
															</div>
														</div>
														

														<div class="input-group select2-bootstrap-prepend pad-top">
																<button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
														</div>             

                            </div>
                        </div>
                        <br>                    
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Jumlah Transaksi</div> -->
                                    <span style="float: left;" class="borr">Jumlah Transaksi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlah" name="jumlah" disabled> </div>                                   
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Jumlah Transaksi</div> -->
                                    <span style="float: left;" class="borr">Jumlah Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlahdibayar" name="jumlahdibayar" disabled> </div>                                   
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Jumlah Transaksi</div> -->
                                    <span style="float: left;" class="borr2" >Jumlah Belum Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="jumlahbelum" name="jumlahbelum" disabled> </div>                                   
                                </div>        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-inline">
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Total Transaksi</div> -->
                                    <span style="float: left;" class="borr">Total Transaksi </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="total" name="total"  disabled> </div>                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Total Transaksi</div> -->
                                    <span style="float: left;" class="borr">Total Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="totaldibayar" name="totaldibayar"  disabled> </div>                                    
                                </div>
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <!-- <div  id="ex1">Total Transaksi</div> -->
                                    <span style="float: left;" class="borr2" >Total Belum Dibayar </span><div style="float:left;">
                                    <input type="text" class="textboxid" id="totalbelum" name="totalbelum"  disabled> </div>                                    
                                </div>        
                            </div>
                        </div>    
                    </div>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>INVOICE NUMBER</th>
                                <th>REF NO</th>
                                <th>KODE BOOKING</th>
                                <th>NO TIKET</th>
                                <th>MITRA ID</th>
                                <th>WAKTU TRANSAKSI</th>
                                <th>WAKTU KEBERANGKATAN</th>
                                <th>WAKTU SETTLEMENT</th>
                                <th>ASAL</th>
                                <th>TUJUAN</th>
                                <th>LAYANAN</th>
                                <th>JENIS PENGGUNA JASA</th>
                                <th>GOLONGAN</th>
                                <th>KODE TOKO</th>
                                <th>NAMA TOKO</th>
                                <th>STATUS</th>
                                <th>TARIF PER JENIS</th>
                                <th>ADMIN FEE</th>
                                <th>DISKON</th>
                                <th>TRANSFER ASDP</th>
                                <th>KODE PROMO</th>
                                <th>UPDATE SETTLEMENT</th>                            
                            </tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>                    
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

        function totalrow(){
            service = document.getElementById('service').value;
            dateFrom = document.getElementById('dateFrom').value;
            dateTo = document.getElementById('dateTo').value;
            dateFrom2 = document.getElementById('dateFrom2').value;
            dateTo2 = document.getElementById('dateTo2').value;
            dateFrom3 = document.getElementById('dateFrom3').value;
            dateTo3 = document.getElementById('dateTo3').value;
            merchant = document.getElementById('merchant').value;
            status_type = document.getElementById('status_type').value;
            search = $('.dataTables_filter input').val();
            $.ajax({
                url: "<?php echo site_url('laporan/menu_rekonsiliasi/get_total') ?>",
                type: "POST",
                dataType : "JSON",
                data: {
                    service: service,
                    dateFrom: dateFrom,
                    dateTo: dateTo,
                    dateFrom2: dateFrom2,
                    dateTo2: dateTo2,
                    dateFrom3: dateFrom3,
                    dateTo3: dateTo3,
                    merchant: merchant,
                    status_type: status_type,
                    search: search
                    
                },
                success: function (json) {
                    data = json.data
                    // console.log(data[0][0])
                    $("input[name='jumlah']").val(data[0][0].jumlah_transaksi);
                    $("input[name='total']").val("Rp. "+data[0][0].total_transaksi);
                    $("input[name='jumlahdibayar']").val(data[0][1].jumlah_dibayar);
                    $("input[name='totaldibayar']").val("Rp. "+data[0][1].total_dibayar);
                    $("input[name='jumlahbelum']").val(data[0][2].jumlah_belum);
                    $("input[name='totalbelum']").val("Rp. "+data[0][2].total_belum);
                    // console.log($("#jumlah").val())
                }
            });    
        }



var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporanv2/sensor_report') ?>",
                "type": "POST",
                "data": function(d) {
                    d.service = document.getElementById('service').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom2 = document.getElementById('dateFrom2').value;
                    d.dateTo2 = document.getElementById('dateTo2').value;
                    d.dateFrom3 = document.getElementById('dateFrom3').value;
                    d.dateTo3 = document.getElementById('dateTo3').value;
                    d.merchant = document.getElementById('merchant').value;
                    d.status_type = document.getElementById('status_type').value;
                    
                }                
            },
        
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "id_trans", "orderable": true, "className": "text-left"},
                    {"data": "payment_code", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "merchant_id", "orderable": true, "className": "text-center"},
                    {"data": "waktu_trans", "orderable": true, "className": "text-left"},
                    {"data": "depart_date", "orderable": true, "className": "text-right"},
                    {"data": "waktu_settle", "orderable": true, "className": "text-left"},
                    {"data": "asal", "orderable": true, "className": "text-left"},
                    {"data": "tujuan", "orderable": true, "className": "text-left"},
                    {"data": "ship_class", "orderable": true, "className": "text-left"},
                    {"data": "service", "orderable": true, "className": "text-left"},
                    {"data": "golongan", "orderable": true, "className": "text-left"},
                    {"data": "shop_code", "orderable": true, "className": "text-left"},
                    {"data": "shop_name", "orderable": true, "className": "text-left"},
                    {"data": "reconn_status", "orderable": true, "className": "text-center"},
                    {"data": "tarif_per_jenis", "orderable": true, "className": "text-right"},
                    {"data": "admin_fee", "orderable": true, "className": "text-right"},
                    {"data": "diskon", "orderable": true, "className": "text-left"},
                    {"data": "transfer_asdp", "orderable": true, "className": "text-left"},
                    {"data": "code_promo", "orderable": true, "className": "text-left"},
                    {"data": "updated_settlement", "orderable": true, "className": "text-left"},
                    // {"data": "status_invoice", "orderable": true, "className": "text-left"},
                    
            ],
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                  "processing": "Proses.....",
                  "emptyTable": "Tidak ada data",
                  "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                  "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                  "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                  "lengthMenu": "Menampilkan _MENU_",
                  "search": "Pencarian :",
                  "zeroRecords": "Tidak ditemukan data yang sesuai",
                  "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir",
                    "first": "Pertama"
                }
            },
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#dataTables').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
                        totalrow();
                    }
                });

            },

            fnDrawCallback: function(allRow)
            {   
                if(allRow.json.recordsTotal)
                {
                    $('.download').prop('disabled',false);
                }
                else
                {
                    $('.download').prop('disabled',true);
                }
            }
        });
        
        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#dataTables').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#dataTables').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }
        this.loadData();
    }
};

    
    $(document).ready(function () {
        table.init();

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var dateFrom2=$("#dateFrom2").val();
            var dateTo2=$("#dateTo2").val();
            var dateFrom3=$("#dateFrom3").val();
            var dateTo3=$("#dateTo3").val();
            var service=$("#service").val();
            var merchant=$("#merchant").val();
            var status_type=$("#status_type").val();
            var jumlah=$("#jumlah").val();
            var total=$("#total").val();
            var jumlahdibayar=$("#jumlahdibayar").val();
            var totaldibayar=$("#totaldibayar").val();
            var jumlahbelum=$("#jumlahbelum").val();
            var totalbelum=$("#totalbelum").val();
            var search= $('.dataTables_filter input').val();
            // console.log(jumlah)
            // console.log(search)
            window.location.href="<?php echo site_url('laporan/menu_rekonsiliasi/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&dateFrom2="+dateFrom2+"&dateTo2="+dateTo2+"&dateFrom3="+dateFrom3+"&dateTo3="+dateTo3+"&service="+service+"&merchant="+merchant+"&jumlah="+jumlah+"&jumlahdibayar="+jumlahdibayar+"&jumlahbelum="+jumlahbelum+"&status_type="+status_type+"&total="+total+"&totaldibayar="+totaldibayar+"&totalbelum="+totalbelum+"&search="+search;
        });

 
        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateTo').datepicker('setStartDate', e.date)
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            startDate: $('#datefrom').val(),
            endDate: new Date(),
        }).on('changeDate',function(e) {
            $('#dateFrom').datepicker('setEndDate', e.date)
        });
        
        
        

        $(function () {
            totalrow()
            
        })
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });
        
        $("#service").on("change",function(){
            table.reload();
            totalrow();
        });
        
        $("#dateFrom").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#dateTo").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#dateFrom2").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#dateTo2").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#dateFrom3").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#dateTo3").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#port_origin").on("change",function(){
            table.reload();
        });

        $("#port_destination").on("change",function(){
            table.reload();
        });

        $("#merchant").on("change",function(){
            table.reload();
            totalrow();
        });

        $("#status_type").on("change",function(){
            table.reload();
            totalrow();
        });
        
        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        // $(".menu-toggler").click(function() {
        //     $('.select2').css('width', '100%');
        // });
    });
</script>