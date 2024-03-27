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
                <div class="portlet-body">
							<div class="table-toolbar">
								<div class="row">
										<div class="col-sm-12 form-inline">
												<div class="input-group select2-bootstrap-prepend pad-top">
														<div class="input-group-addon">Tanggal Transaksi</div>
														<input type="text" name="dateFrom" id="dateFrom" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $last_week; ?>" readonly></input>
														<div class="input-group-addon">s/d</div>
														<input type="text" name="dateTo" id="dateTo" class="form-control date" autocomplete="off" placeholder="YYYY-MM-DD" value="<?php echo $now; ?>" readonly ></input>
												</div>
												
												<div class="input-group select2-bootstrap-prepend pad-top">
														<div class="input-group-addon">Tipe FTP</div>
														<select id="tipe" class="form-control js-data-example-ajax select2 input-small" dir="" name="service">
																<option value="">Pilih</option>
																<option value="<?php echo $this->enc->encode('download'); ?>">Download</option>
																<option value="<?php echo $this->enc->encode('upload'); ?>">Upload</option>
														</select>
												</div>

												<div class="input-group pad-top">
														<div class="input-group-btn">
																<button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData' >Nama File
																		<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu">
																		<li>
																				<a href="javascript:;" onclick="table.changeSearch('Nama File','file_name')">Nama File</a>
																		</li>
																</ul>
														</div>
														<!-- /btn-group -->
														<input type="text" class="form-control" placeholder="Cari Data" data-name="file_name" name="searchData" id="searchData"> 
												</div>

												<div class="input-group pad-top">
														<button type="submit" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
																<span class="ladda-label">Cari</span>
																<span class="ladda-spinner"></span>
														</button>
												</div>
										</div>

								</div>
							</div>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA FILE</th>
                                <th>TANGGAL TRANSAKSI</th>
                                <th>STATUS</th>
                                <th>TIPE FTP</th>
                                <th>CREATED ON</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

class myData {
    loadData=()=> {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('monitoring/ftp_brilink') ?>",
                "type": "POST",
                "data": function(d) {
									d.dateFrom = document.getElementById('dateFrom').value;
									d.dateTo = document.getElementById('dateTo').value;
									d.tipe = document.getElementById('tipe').value;
									d.searchName=$("#searchData").attr('data-name');
									d.searchData=document.getElementById('searchData').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "file_name", "orderable": true, "className": "text-left"},
                    {"data": "transaction_date", "orderable": true, "className": "text-center"},
                    {"data": "description", "orderable": true, "className": "text-center"},
                    {"data": "ftp_type", "orderable": true, "className": "text-center"},
                    {"data": "created_on", "orderable": true, "className": "text-center"},
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
						"searching"	: false,
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
                    }
                });
            },
            "fnDrawCallback": function(allRow) 
                {
                    let getTokenName = `<?php echo $this->security->get_csrf_token_name(); ?>`;
                    let getToken = allRow.json[getTokenName];
                    csfrData[getTokenName] = getToken;

                    if( allRow.json[getTokenName] == undefined )
					{
						csfrData[allRow.json['csrfName']] = allRow.json['tokenHash'];
					}
                    
                    $.ajaxSetup({
                        data: csfrData
                    });
                }
        });

        $('#export_tools > li > a.tool-action').on('click', function() {
            var data_tables = $('#dataTables').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    }

    reload=()=> {
        $('#dataTables').DataTable().ajax.reload();
    }

    init=()=> {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }

		formatDate(date) {
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

	    changeSearch(x,name)
	    {
	    	$("#btnData").html(`${x} <i class="fa fa-angle-down"></i>`);
	    	$("#searchData").attr('data-name', name);

	    }
};

var table = new myData();
    
    jQuery(document).ready(function () {
        table.init();

        $("#category").on("change",function(){
            table.reload();
        });

        $("#team").on("change",function(){
            table.reload();
        });

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

				$("#cari").on("click",function(){
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        });

        
    });

</script>
