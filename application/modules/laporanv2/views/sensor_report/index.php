<link href="<?php echo base_url()?>assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .pad-top{padding-top: 5px;}
    .checkbox label:after, 
        .radio label:after {
            content: '';
            display: table;
            clear: both;
        }

        .checkbox .cr,
        .radio .cr {
            position: relative;
            display: inline-block;
            border: 1px solid #a9a9a9;
            border-radius: .25em;
            width: 1.3em;
            height: 1.3em;
            float: left;
            margin-right: .5em;
        }

        .radio .cr {
            border-radius: 50%;
        }

        .checkbox .cr .cr-icon,
        .radio .cr .cr-icon {
            position: absolute;
            font-size: .8em;
            line-height: 0;
            top: 50%;
            left: 20%;
        }

        .radio .cr .cr-icon {
            margin-left: 0.04em;
        }

        .checkbox label input[type="checkbox"],
        .radio label input[type="radio"] {
            display: none;
        }

        .checkbox label input[type="checkbox"] + .cr > .cr-icon,
        .radio label input[type="radio"] + .cr > .cr-icon {
            transform: scale(3) rotateZ(-20deg);
            opacity: 0;
            transition: all .3s ease-in;
        }

        .checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
        .radio label input[type="radio"]:checked + .cr > .cr-icon {
            transform: scale(1) rotateZ(0deg);
            opacity: 1;
        }

        .checkbox label input[type="checkbox"]:disabled + .cr,
        .radio label input[type="radio"]:disabled + .cr {
            opacity: .5;
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
                    <div class="btn-group heading-btn pull-right">
                        
                        <!-- <div class="dropdown pull-right"> -->
                            <button type="button" class="btn btn-icon dropdown-toggle btn-warning btn-sm download" data-toggle="dropdown" aria-expanded="true">Download <i class="icon-download"></i> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:;" class="download" style="font-weight:500;" id="download_pdf">PDF</a></li>
                                <li><a href="javascript:;" class="download" style="font-weight:500;" id="download_excel">Excel</a></li>
                            </ul>
                        <!-- </div> -->
                        <button style="margin-left:5px;" type="button" class="btn btn-warning mt-ladda-btn ladda-button btn-sm" data-style="zoom-in" id="btn-approve" disabled>
                            <span class="ladda-label">Approved</span>
                            <span class="ladda-spinner"></span>
                        </button>
                    </div>
                        <!-- <?php if ($download_excel) {?>
                        <button  class="pull-right btn btn-sm btn-warning download" style="padding-left: 5px" id="download_excel">Excel</button>
                        <?php } ?>
						<button id="download_pdf" class=" pull-right btn btn-sm btn-warning download" style="margin-right: 5px">PDF</button> -->
                    <!-- <div class="pull-right btn-add-padding" style="padding-left: 5px"><?php echo $import_excel; ?></div> -->
                </div>
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        <option value="">Semua</option>
                                        <?php foreach($port as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php }  ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Tanggal</div>
                                    <input type="text" name="dateFrom" id="dateFrom" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly></input>
                                    <div class="input-group-addon">s/d</div>
                                    <input type="text" name="dateTo" id="dateTo" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly ></input>
                                </div>
                                
                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Kelas Layanan</div>
                                    <select id="shipclass" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
                                        <option value="">Semua</option>
                                        <?php foreach($class as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        <?php }?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Status</div>
                                    <select id="status" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        <option value="">Semua</option>
                                        <option value="<?php echo $this->enc->encode(4); ?>"><?php echo strtoupper('gate in'); ?></option>
                                        <option value="<?php echo $this->enc->encode(5); ?>"><?php echo strtoupper('boarding'); ?></option>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Shift</div>
                                    <select id="shift" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Semua</option>
                                        <?php foreach($shift as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->shift_name); ?></option>
                                        <?php }?>
                                    </select>                                    
                                </div>
                
								<div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Regu</div>
                                    <select id="regu" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Semua (Pilih Pelabuhan terlebih dahulu)</option>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Petugas</div>
                                    <select id="petugas" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Semua</option>
                                        <?php foreach($petugas as $key=>$value) {?>
                                            <option value="<?php echo $this->enc->encode($value->id); ?>"><?= $value->first_name . " " . $value->last_name ?></option>
                                        <?php }?>
                                    </select>                                    
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Nama Loket</div>
                                    <select id="loket" class="form-control js-data-example-ajax select2 input-small" dir="" name="status_type">
                                        <option value="">Semua (Pilih Pelabuhan terlebih dahulu)</option>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend pad-top">
                                    <div class="input-group-addon">Keterangan</div>
                                    <select id="keter" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        <option value="">Semua</option>
                                        <option value="<?php echo $this->enc->encode(1); ?>"><?php echo strtoupper('Over Paid'); ?></option>
                                        <option value="<?php echo $this->enc->encode(2); ?>"><?php echo strtoupper('Under Paid'); ?></option>
                                    </select>
                                </div>

								<div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Kode Booking
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Kode Booking','bookingCode')">Kode Booking</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Nomor Tiket','ticketNumber')">'Nomor Tiket</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="table.changeSearch('Nomor Polisi','nopol')">'Nomor Polisi</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="bookingCode" name="searchData" id="searchData"> 
                                </div>   
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-inline">

								

                            </div>
                        </div>  
                    </div>
                    <table class="table table-bordered table-hover" id="dataTables">
                        <thead>
                            <tr>
                                <th rowspan="3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="checkAll" id="checkAll" >
                                            <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                        </label>
                                    </div>

                                </th>
                                <th rowspan="3">NO</th>
																<th rowspan="3">KODE BOOKING</th>
																<th rowspan="3">NOMOR TIKET</th>
																<th rowspan="3">LAYANAN</th>
																<th colspan="4" rowspan="2">NOMOR POLISI</th>
																<th colspan="19">HASIL PENGUKURAN</th>
																<th colspan="3" rowspan="2">IDENTITAS PETUGAS</th>
																<th colspan="5" rowspan="2">INFORMASI TIKET</th>
																<th colspan="4" rowspan="2">STATUS APPROVAL NAIK/TURUN GOLONGAN</th>
														</tr>
														<tr>
																<th colspan="4">PANJANG</th>
																<th colspan="4">LEBAR</th>
																<th colspan="4">TINGGI</th>
																<th colspan="3">BERAT</th>
																<th colspan="4">GOLONGAN</th>
														</tr>
														<tr>
																<th>RESERVASI</th>
																<th>SENSOR</th>
																<th>MANUAL</th>
																<th>KOMPARASI MANUAL <br> DAN SENSOR</th>

																<th>RESERVASI</th>
																<th>SENSOR</th>
																<th>MANUAL</th>
																<th>MANUAL - SENSOR</th>

																<th>RESERVASI</th>
																<th>SENSOR</th>
																<th>MANUAL</th>
																<th>MANUAL - SENSOR</th>

																<th>RESERVASI</th>
																<th>SENSOR</th>
																<th>MANUAL</th>
																<th>MANUAL - SENSOR</th>

																<th>BATASAN</th>
																<th>HASIL TIMBANG</th>
																<th>STATUS</th>

																<th>RESERVASI</th>
																<th>SENSOR</th>
																<th>MANUAL</th>
																<th>KOMPARASI MANUAL <br> DAN SENSOR</th>

																<th>USER PETUGAS <br> LOKET</th>
																<th>NAMA LOKET</th>
																<th>USER SUPERVISI</th>

																<th>STATUS</th>
																<th>DERMAGA</th>
																<th>KAPAL</th>
																<th>WAKTU</th>
																<th>KETERANGAN</th>

																<th>STATUS</th>
																<th>USER</th>
																<th>TANGGAL</th>
																<th>AKSI</th>
														</tr>
                        </thead>
                        <tfoot></tfoot>
                    </table>                    
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>
<script type="text/javascript">

class myData {
    loadData() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporanv2/sensor_report') ?>",
                "type": "POST",
                "data": function(d) {
                    d.port = document.getElementById('port').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
                    d.dateTo = document.getElementById('dateTo').value;
                    d.class = document.getElementById('shipclass').value;
                    d.status = document.getElementById('status').value;
                    d.shift = document.getElementById('shift').value;
                    d.regu = document.getElementById('regu').value;
                    d.petugas = document.getElementById('petugas').value;
                    d.loket = document.getElementById('loket').value;
                    d.keter = document.getElementById('keter').value;
                    d.searchName=$("#searchData").attr('data-name');
                    d.searchData=document.getElementById('searchData').value;
                    
                }                
            },
        
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "checkBox", "orderable": false, "className": "text-center"},
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "booking_code", "orderable": true, "className": "text-left"},
                    {"data": "ticket_number", "orderable": true, "className": "text-left"},
                    {"data": "ship_class", "orderable": true, "className": "text-left"},
                    {"data": "nopol_bok", "orderable": true, "className": "text-center"},
                    {"data": "nopol_cek", "orderable": true, "className": "text-center"},
                    {"data": "nopol_man", "orderable": true, "className": "text-center"},
                    {"data": "nopol_comp", "orderable": true, "className": "text-center"},
                    {"data": "panjang_bok", "orderable": true, "className": "text-right"},
                    {"data": "panjang_cek", "orderable": true, "className": "text-right"},
                    {"data": "panjang_man", "orderable": true, "className": "text-right"},
                    {"data": "panjang", "orderable": true, "className": "text-right"},
                    {"data": "lebar_bok", "orderable": true, "className": "text-right"},
                    {"data": "lebar_cek", "orderable": true, "className": "text-right"},
                    {"data": "lebar_man", "orderable": true, "className": "text-right"},
                    {"data": "lebar", "orderable": true, "className": "text-right"},
                    {"data": "tinggi_bok", "orderable": true, "className": "text-right"},
                    {"data": "tinggi_cek", "orderable": true, "className": "text-right"},
                    {"data": "tinggi_man", "orderable": true, "className": "text-right"},
                    {"data": "tinggi", "orderable": true, "className": "text-right"},
                    {"data": "batasan", "orderable": true, "className": "text-right"},
                    {"data": "hasil_timbang", "orderable": true, "className": "text-right"},
                    {"data": "berat_status", "orderable": true, "className": "text-left"},
                    {"data": "gol_bok", "orderable": true, "className": "text-center"},
                    {"data": "gol_cek", "orderable": true, "className": "text-center"},
                    {"data": "gol_man", "orderable": true, "className": "text-center"},
                    {"data": "gol_comp", "orderable": true, "className": "text-center"},
                    {"data": "user_petugas_loket", "orderable": true, "className": "text-center"},
                    {"data": "nama_loket", "orderable": true, "className": "text-center"},
                    {"data": "nama_spv", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "dermaga", "orderable": true, "className": "text-center"},
                    {"data": "nama_kapal", "orderable": true, "className": "text-center"},
                    {"data": "waktu", "orderable": true, "className": "text-center"},
                    {"data": "keterangan", "orderable": true, "className": "text-center"},
                    {"data": "appr_status", "orderable": true, "className": "text-center"},
                    {"data": "appr_user", "orderable": true, "className": "text-center"},
                    {"data": "appr_tanggal", "orderable": true, "className": "text-center"},
                    {"data": "appr_aksi", "orderable": true, "className": "text-center"},
                    
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
            "searching": false,
            "pagingType": "bootstrap_full_number",
            "order": [[ 0, "desc" ]],
            "initComplete": function () {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#dataTables').DataTable();
                $searchInput.unbind();
                $searchInput.bind('keyup', function (e) {
                    if (e.keyCode == 13 || e.whiche == 13) {
                        data_tables.search(this.value).draw();
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
    }

    reload() {
        $('#dataTables').DataTable().ajax.reload();
    }

    init() {
        if (!jQuery().DataTable) {
            return;
        }
        this.loadData();
    }

    changeSearch(x,name)
    {
        $("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
        $("#searchData").attr('data-name', name);

    }

    formatDate=(date)=> {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;

        return [year, month, day].join('-');
    }

    approveData=()=>
		{
		    var idApprove=[];
		    $('input.myCheck:checkbox:checked').each(function () {
		        idApprove.push($(this).val());
		    });

		    var l = Ladda.create(document.querySelector('.ladda-button'));

		    alertify.confirm("Apakah anda yakin ingin approve data ini", function (e) {
		        if(e)
		        {
                    console.log(idApprove);
		            // $.ajax({
		            //     dataType : "JSON",
		            //     type : "post",
		            //     url : "<?php echo site_url()?>refund/refund/actionApprove",
		            //     data :{idApprove:idApprove},
		            //     beforeSend: ()=>{
		            //         l.start();
		            //         unBlockUiId("dataTables");
		            //     },
		            //     success : (x)=>{
		                    
		            //         if(x.code==1)
		            //         {
		            //             toastr.success(x.message, 'Sukses');
		            //             $('#dataTables').DataTable().ajax.reload( null, false );
		            //         }
		            //         else
		            //         {
		            //             toastr.error(x.message, 'Gagal');
		            //         }
		            //     },
		            //     error: ()=> {
		            //         toastr.error('Silahkan Hubungi Administrator', 'Gagal');
		            //     },
		            //     complete: function(){
		            //          l.stop();
		            //          $('#dataTables').unblock(); 
		            //     }                
		            // })
		        }
		    });
		}
};

    var table = new myData();
    $(document).ready(function () {

        table.init();

        $("#port").change(function() {
            $.ajax({
                    method: "GET",
                    url: "sensor_report/get_regu/" + $("#port").val(),
                    type: "html"
            })
            .done(function(msg) {
                $("#regu").html(msg);
            });
            $.ajax({
                method: "GET",
                url: "sensor_report/get_loket/" + $("#port").val(),
                type: "html"
            })
            .done(function(msg) {
                $("#loket").html(msg);
            });
        });

        $("#btn-approve").click(function(){

            table.approveData();
        })

        $("#download_excel").click(function(event){
            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var shipclass=$("#shipclass").val();
            var status=$("#status").val();
            var shift=$("#shift").val();
            var regu=$("#regu").val();
            var petugas=$("#petugas").val();
            var loket=$("#loket").val();
            var keter=$("#keter").val();
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;
            // console.log(jumlah)
            // console.log(search)
            window.location.href="<?php echo site_url('laporanv2/sensor_report/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&shipclass="+shipclass+"&status="+status+"&shift="+shift+"&regu="+regu+"&petugas="+petugas+"&loket="+loket+"&keter="+keter+"&searchName="+searchName+"&searchData="+searchData;
        });

		$("#download_pdf").click(function() {


            const channelName1 = $("#channel option:selected").text(),
            portName1 = $("#port option:selected").text()

            var dateFrom=$("#dateFrom").val();
            var dateTo=$("#dateTo").val();
            var port=$("#port").val();
            var shipclass=$("#shipclass").val();
            var status=$("#status").val();
            var shift=$("#shift").val();
            var regu=$("#regu").val();
            var petugas=$("#petugas").val();
            var loket=$("#loket").val();
            var keter=$("#keter").val();
            var searchName=$("#searchData").attr('data-name');
            var searchData=document.getElementById('searchData').value;

            var url_download = "dateFrom="+dateFrom+"&dateTo="+dateTo+"&port="+port+"&shipclass="+shipclass+"&status="+status+"&shift="+shift+"&regu="+regu+"&petugas="+petugas+"&loket="+loket+"&keter="+keter+"&searchName="+searchName+"&searchData="+searchData;

            window.open("<?php echo site_url('laporanv2/sensor_report/download_pdf?') ?>" + url_download);
        });

        $("#checkAll").change(function(){

            if($(this).is(":checked"))
            {
                $(".myCheck").prop('checked',true)
            }
            else
            {
                $(".myCheck").removeAttr('checked')
            }
        })

 
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,           
        });

        $('#dateFrom').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
        });

        $('#dateTo').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true,
            // endDate: "+1m",
            startDate: new Date()
        });        


        $("#dateFrom").change(function() {
            
            var startDate = $(this).val();
            var someDate = new Date(startDate);

            someDate.getDate();
            someDate.setMonth(someDate.getMonth()+1);
            someDate.getFullYear();
            let endDate=table.formatDate(someDate);
            // destroy ini firts setting
            $('#dateTo').datepicker('remove');
            
              // Re-int with new options
            $('#dateTo').datepicker({
                format: 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                endDate: endDate,
                startDate: startDate
            });

            $('#dateTo').val(startDate).datepicker("update")
            // myData.reload();
        });


        setTimeout(function() {
            $('.menu-toggler').trigger('click');
        }, 1);

        $("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });
    });
</script>