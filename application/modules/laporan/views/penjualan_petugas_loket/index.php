<style>
    .input-group .select2-container--bootstrap {
        width: 100% !important;
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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-7 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">

                    <div class="caption"><?php echo $title ?></div>
                    <!-- <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div> -->
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">                                       
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Pelabuhan</div>
                                                    <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="port">
                                                        <option value="">All</option>
                                                        <?php foreach($port as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
    												<div class="input-group-addon">User Petugas</div>
    												<select id="username" class="form-control js-data-example-ajax select2 input-small" dir="" name="username">
    													<option value="">All</option>
    													<?php foreach($petugas as $key=>$value) {?>
    													<option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo $value->full_name ?></option>
    													<?php }?>
    												</select>
    											</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Tanggal Shift</div>
                                                    <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $now; ?>">
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Shift</div>
                                                    <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="shift">
                                                        <option value="">All</option>
                                                        <?php foreach($shift as $key=>$value) {?>
                                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">Regu</div>
                                                        <select id="regu" class="form-control js-data-example-ajax select2 input-small" dir="" name="regu">
                                                            <option value="">All</option>
                                                            <?php foreach($regu as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->team_code); ?>"><?php echo strtoupper($value->team_name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                 </div>
                                            </div>
                                      
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                    <div class="input-group-addon">Loket</div>
                                                        <select id="loket" class="form-control js-data-example-ajax select2 input-small" dir="" name="loket">
                                                            <option value="">All</option>
                                                            <?php foreach($loket as $key=>$value) {?>
                                                            <option value="<?php echo $this->enc->encode($value->terminal_code); ?>"><?php echo strtoupper($value->terminal_name); ?></option>
                                                            <?php }?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-danger" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">Cari</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                
                                            </div>
                                        </div>
                                    </div>
								</div>

								<div class="kt-portlet__body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item active">
                                            <a id="tabPenumpang" class="label label-primary " data-toggle="tab" data-target="#penumpang" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Pejalan Kaki</a>
                                        </li>

                                        <li class="nav-item">
											<a id="tabKendaraan" class="label label-primary " data-toggle="tab" data-target="#kendaraan" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Loading...">Data Kendaraan</a>
                                        </li>
									</ul>
								</div>

								<div class="tab-content" >
        
                                    <!-- tab data penumpang -->
                                    <div class="tab-pane active" id="penumpang" role="tabpanel" style="padding: 10px">
                                        <div class="form-group pull-right">
                                            <?php echo $btnPdf; ?>
                                            <?php echo $btnExcel; ?>
                                        </div>
										<table class="table table-bordered table-striped   table-hover" id="table_penumpang" hidden>
											<thead>
												<tr>
													<th>NO</th>
													<th>NAMA PETUGAS</th>
													<th>USER PETUGAS</th>
													<th>LOKET</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>NOMOR TIKET</th>
													<th>GOLONGAN</th>
													<th>TARIF</th>
													<th>METODE PEMBAYARAN</th>
													<th>KELAS</th>
													<th>TANGGAL SHIFT</th>
													<th>SHIFT</th>
													<th>REGU</th>
													<th>NAMA PENGGUNA JASA</th>
													<th>NO IDENTITAS</th>
													<th>NAMA KAPAL</th>
													<th>TANGGAL KLAIM</th>
													<!-- <th>AKSI</th> -->
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                        <h4 class="text-right"><b id="total_amount">Total 0</b></h4>
                                    </div>

                                    <!-- Data Kendaraan -->
                                    <div class="tab-pane " id="kendaraan" role="tabpanel" style="padding: 10px" >
                                        <div class="form-group pull-right">
                                            <?php echo $btnPdfVehicle; ?>
                                            <?php echo $btnExcelVehicle; ?>
                                        </div>
										<table class="table table-bordered table-striped   table-hover" id="table_kendaraan" hidden>
											<thead>
												<tr>
													<th>NO</th>
													<th>NAMA PETUGAS</th>
													<th>USER PETUGAS</th>
													<th>LOKET</th>
                                                    <th>KODE BOOKING</th>
                                                    <th>NOMOR TIKET</th>
													<th>GOLONGAN</th>
													<th>TARIF</th>
													<th>METODE PEMBAYARAN</th>
													<th>KELAS</th>
													<th>TANGGAL SHIFT</th>
													<th>SHIFT</th>
													<th>REGU</th>
													<th>NO POLISI</th>
													<th>NAMA PENGGUNA JASA</th>
													<th>NO IDENTITAS</th>
													<th>NAMA KAPAL</th>
													<th>TANGGAL KLAIM</th>
													<!-- <th>AKSI</th> -->
												</tr>
											</thead>
											<tfoot></tfoot>
										</table>
                                        <h4 class="text-right"><b id="total_amount_kendaraan">Total 0</b></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- </div>     -->
            </div>
        </div>
    </div>
</div>
<form id="formDownload" target="_blank" method="POST"></form>
<script type="text/javascript">
var initPnp = false;
var initKnd = false;
var target = '#penumpang';
var send_dateTo     = '', 
    send_dateFrom   = '', 
    send_port       = '',
    send_username   = '',
    send_shift      = '',
    send_regu       = '',
    send_loket      = '',
    send_port_name  = 'All';

$("#dateTo").change(function(){
    send_dateTo    = $('#dateTo').val()
});

$("#dateFrom").change(function(){
    send_dateFrom  = $('#dateFrom').val()
});

$("#port").change(function(){
    send_port  = $('#port').val();
    send_port_name  = $("#port option:selected").text();
});

$("#username").change(function(){
    send_username  = $('#username').val()
});

$("#shift").change(function(){
    send_shift  = $('#shift').val()
});

$("#regu").change(function(){
    send_regu  = $('#regu').val()
});

$("#loket").change(function(){
    send_loket  = $('#loket').val()
});

var table_penumpang= {
    loadData: function() {
        $('#table_penumpang').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/penjualan_petugas_loket/penumpang') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.username = document.getElementById('username').value;
                    d.shift = document.getElementById('shift').value;
                    d.loket = document.getElementById('loket').value;
                    d.regu = document.getElementById('regu').value;
				},
				complete: function(){
					$("#cari").button('reset');
                    $("#tabPenumpang").button('reset');
					$("#table_penumpang").show();
				}
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "first_name", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "loket", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "golongan", "orderable": true, "className": "text-left"},
                    {"data": "tarif", "orderable": true, "className": "text-right"},
                    {"data": "payment_type", "orderable": true, "className": "text-left"},
                    {"data": "kelas", "orderable": true, "className": "text-left"},
                    {"data": "trans_date", "orderable": true, "className": "text-left"},
                    {"data": "shift", "orderable": true, "className": "text-left"},
                    {"data": "regu", "orderable": true, "className": "text-left"},
                    {"data": "customer_name", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "ship", "orderable": true, "className": "text-left"},
                    {"data": "naik_kapal", "orderable": true, "className": "text-left"},
                    // {"data": "actions", "orderable": false, "className": "text-center"},
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
            fnDrawCallback: function(data) {  
                params = data.oAjaxData;

                if(data.json.recordsTotal){
                    $('#penumpang .btn-download').css('display','inline-block');
                }else{
                    $('#penumpang .btn-download').css('display','none');
                }

                $('#total_amount').html('Total '+data.json.total);
            },
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var searchInput = $('#table_penumpang_filter input');
                var data_tables = $('#table_penumpang').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {

                        data_tables.search(this.value).draw();
                    }
                });
            },
        });

        // $('#export_tools > li > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
    },

    reload: function() {
        $('#table_penumpang').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

var table_kendaraan= {
    loadData: function() {
        $('#table_kendaraan').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/penjualan_petugas_loket/kendaraan') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.username = document.getElementById('username').value;
                    d.shift = document.getElementById('shift').value;
                    d.loket = document.getElementById('loket').value;
                    d.regu = document.getElementById('regu').value;
				},
				complete: function(){
					$("#cari").button('reset');
                    $("#tabKendaraan").button('reset');
					$("#table_kendaraan").show();
				}
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "number", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "first_name", "orderable": true, "className": "text-left"},
                    {"data": "username", "orderable": true, "className": "text-left"},
                    {"data": "loket", "orderable": true, "className": "text-left"},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "golongan", "orderable": true, "className": "text-left"},
                    {"data": "tarif", "orderable": true, "className": "text-right"},
                    {"data": "payment_type", "orderable": true, "className": "text-left"},
                    {"data": "kelas", "orderable": true, "className": "text-left"},
                    {"data": "trans_date", "orderable": true, "className": "text-left"},
                    {"data": "shift", "orderable": true, "className": "text-left"},
                    {"data": "regu", "orderable": true, "className": "text-left"},
                    {"data": "plat", "orderable": true, "className": "text-left"},
                    {"data": "customer_name", "orderable": true, "className": "text-left"},
                    {"data": "id_number", "orderable": true, "className": "text-left"},
                    {"data": "ship", "orderable": true, "className": "text-left"},
                    {"data": "naik_kapal", "orderable": true, "className": "text-left"},
                    // {"data": "actions", "orderable": false, "className": "text-center"},
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
            fnDrawCallback: function(data) {  
                params = data.oAjaxData;

                if(data.json.recordsTotal){
                    $('#kendaraan .btn-download').css('display','inline-block');
                }else{
                    $('#kendaraan .btn-download').css('display','none');
                }

                $('#total_amount_kendaraan').html('Total '+data.json.total);
            },
            "pageLength": 10,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var searchInput = $('#table_penumpang_filter input');
                var data_tables = $('#table_kendaraan').DataTable();
                searchInput.unbind();
                searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        console.log("tes")
                        $('#table_kendaraan').DataTable().search(this.value).draw();
                    }
                });
            },
        });

        // $('#export_tools > li > a.tool-action').on('click', function() {
        //     var data_tables = $('#dataTables').DataTable();
        //     var action = $(this).attr('data-action');

        //     data_tables.button(action).trigger();
        // });
    },

    reload: function() {
        $('#table_kendaraan').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

function getData()
{
    $.ajax({
        data:"port="+$("#port").val(),
        type:"post",
        url:"<?php echo site_url()?>laporan/penjualan_petugas_loket/get_data",
        dataType:"json",
        success:function(x)
        {
            var dataPetugas=x.petugas;
            var dataLoket=x.loket;
            var dataRegu=x.regu;

            var petugasHtml="<option value=''>All</option>";
            var loketHtml="<option value=''>All</option>";
            var reguHtml="<option value=''>All</option>";

            for(var i=0; i<dataPetugas.length; i++)
            {
                petugasHtml += "<option value='"+dataPetugas[i].id+"'>"+dataPetugas[i].full_name+"</option>"
            }

            for(var i=0; i<dataLoket.length; i++)
            {
                loketHtml += "<option value='"+dataLoket[i].id+"'>"+dataLoket[i].terminal_name+"</option>"
            }

            for(var i=0; i<dataRegu.length; i++)
            {
                reguHtml += "<option value='"+dataRegu[i].id+"'>"+dataRegu[i].team_name+"</option>"
            }

            $("#username").html(petugasHtml);
            $("#loket").html(loketHtml);
            $("#regu").html(reguHtml);
        }
    });
}
    jQuery(document).ready(function () {
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        setTimeout(function() {
            $('.menu-toggler').trigger('click');
		}, 1);

        $("#port").on("change",function(){
            getData()
        });

		$("#cari").on("click",function(e){
			$(this).button('loading');
            $("#tabPenumpang").button('loading');
            $("#tabKendaraan").button('loading');
			e.preventDefault();

			// console.log(target);
			// if(target == '#kendaraan'){
				if(initKnd == false){
					table_kendaraan.init();
					initKnd = true;
				}else{
					table_kendaraan.reload();
				}
			// }else{
				if(initPnp == false){
					table_penumpang.init();
					initPnp = true;
				}else{
					table_penumpang.reload();
				}
			// }
		});
	});

	$(document).on('click', '[data-toggle="tab"]', function(){
		target = $(this).data('target');
        $.ajax({
            url     : '<?php echo site_url() ?>laporan/penjualan_petugas_loket/loket_type',
            data    : {type : target},
            type    : 'POST',
            dataType: 'json',

            success: function(json) {
                $('#loket').html('<option value="">All</option>')
                if(json.code == 1){
                    for (const i in json.data) {
                        $('#loket').append(
                            '<option value="'+json.data[i].terminal_code+'">'+json.data[i].terminal_name.toUpperCase()+'</option>'
                        );
                    }
                }
            },

            error: function() {
                console.log('Silahkan Hubungi Administrator')
            },
        });
	});

    $('#penumpang .btn-download-excel').click(function(){
    
        ahref = $('#penumpang .btn-download-excel').data('href');

        addForm = '<input type="text" name="dateFrom" value="'+send_dateFrom+'">\
        <input type="text" name="dateTo" value="'+send_dateTo+'">\
        <input type="text" name="port" value="'+send_port+'">\
        <input type="text" name="username" value="'+send_username+'">\
        <input type="text" name="shift" value="'+send_shift+'">\
        <input type="text" name="regu" value="'+send_regu+'">\
        <input type="text" name="loket" value="'+send_loket+'">\
        <input type="text" name="port_name" value="'+send_port_name+'">';

        $('#formDownload').attr('action',ahref);
        $('#formDownload').html(addForm);
        $('#formDownload').submit();
        $('#formDownload input').remove();
        // console.log(ahref)
    });        

    $('#kendaraan .btn-download-excel').click(function(){
    
        ahref = $('#kendaraan .btn-download-excel').data('href');

        addForm = '<input type="text" name="dateFrom" value="'+send_dateFrom+'">\
        <input type="text" name="dateTo" value="'+send_dateTo+'">\
        <input type="text" name="port" value="'+send_port+'">\
        <input type="text" name="username" value="'+send_username+'">\
        <input type="text" name="shift" value="'+send_shift+'">\
        <input type="text" name="regu" value="'+send_regu+'">\
        <input type="text" name="loket" value="'+send_loket+'">\
        <input type="text" name="port_name" value="'+send_port_name+'">';

        $('#formDownload').attr('action',ahref);
        $('#formDownload').html(addForm);
        $('#formDownload').submit();
        $('#formDownload input').remove();
        // console.log(ahref)
    });    

    $('#penumpang .btn-download-pdf').click(function(){
        ahref = $('#penumpang .btn-download-pdf').data('href');

        addForm = '<input type="text" name="dateFrom" value="'+send_dateFrom+'">\
        <input type="text" name="dateTo" value="'+send_dateTo+'">\
        <input type="text" name="port" value="'+send_port+'">\
        <input type="text" name="username" value="'+send_username+'">\
        <input type="text" name="shift" value="'+send_shift+'">\
        <input type="text" name="regu" value="'+send_regu+'">\
        <input type="text" name="loket" value="'+send_loket+'">\
        <input type="text" name="port_name" value="'+send_port_name+'">';

        $('#formDownload').attr('action',ahref);
        $('#formDownload').html(addForm);
        $('#formDownload').submit();
        $('#formDownload input').remove();
        // console.log(ahref)
    });

    $('#kendaraan .btn-download-pdf').click(function(){
        ahref = $('#kendaraan .btn-download-pdf').data('href');

        addForm = '<input type="text" name="dateFrom" value="'+send_dateFrom+'">\
        <input type="text" name="dateTo" value="'+send_dateTo+'">\
        <input type="text" name="port" value="'+send_port+'">\
        <input type="text" name="username" value="'+send_username+'">\
        <input type="text" name="shift" value="'+send_shift+'">\
        <input type="text" name="regu" value="'+send_regu+'">\
        <input type="text" name="loket" value="'+send_loket+'">\
        <input type="text" name="port_name" value="'+send_port_name+'">';

        $('#formDownload').attr('action',ahref);
        $('#formDownload').html(addForm);
        $('#formDownload').submit();
        $('#formDownload input').remove();
        // console.log(ahref)
    });
</script>
