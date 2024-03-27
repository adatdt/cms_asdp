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

        <?php $now=date("Y-m-d"); $last_week=date('Y-m-d',strtotime("-365 days"))?>
        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                               
                            <div class="portlet-body">
                                <div class="table-toolbar">
                                    <div class="row">
                                        <div class="col-sm-12 form-inline">

                                            <div class="input-group select2-bootstrap-prepend">
                                                <div class="input-group-addon">Tanggal Shift</div>
                                                <input type="text" class="form-control date input-small" id="dateFrom" value="<?php echo $now; ?>" placeholder="YYYY-MM-DD">
                                                <div class="input-group-addon">s/d</div>
                                                <input type="text" class="form-control date input-small" id="dateTo" value="<?php echo $now; ?>" placeholder="YYYY-MM-DD">

                                            </div> 
																						<div class="input-group select2-bootstrap-prepend pad-top">
                                    						<div class="input-group-addon">Pelabuhan</div>
                                    						<select id="port" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        						<option value="">Pilih</option>
                                        						<?php foreach($port as $key=>$value) {?>
                                        						    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        						<?php }  ?>
                                    						</select>                                    
                                						</div>
																					<div class="input-group select2-bootstrap-prepend pad-top">
                                    				<div class="input-group-addon">Kelas Layanan</div>
                                    					<select id="ship_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        				<option value="">Pilih</option>
                                        					<?php foreach($ship_class as $key=>$value) {?>
                                        				    <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name); ?></option>
                                        					<?php }  ?>
                                    					</select>                                    
																					</div>

                                        </div>

																		</div>
																		<br/>
																		<div class="row">
																			<div class="col-sm-12 form-inline">
																					<div class="input-group select2-bootstrap-prepend pad-top">
                                    				<div class="input-group-addon">Tipe Laporan</div>
                                    					<select id="type" class="form-control js-data-example-ajax select2 input-small" dir="" name="merchant">
                                        				<option value="">Pilih</option>                                        				
                                        				<option value=<?php echo $this->enc->encode(1); ?>>TERJUAL NORMAL</option>
                                                        <option value=<?php echo $this->enc->encode(3); ?>>TERJUAL MANUAL</option>
                                        				<option value=<?php echo $this->enc->encode(2); ?>>TERTAGIH NORMAL</option>
                                        				<option value=<?php echo $this->enc->encode(4); ?>>TERTAGIH MANUAL</option>
                                    					</select>                                    
                                					</div>
																			</div>
																		</div>
                                </div>


                                <table class="table table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>NAMA FILE</th>
                                            <th>PENDAPATAN (Rp.)</th>
                                            <th>TANGGAL SHIFT</th>
                                            <th>SHIFT</th>
                                            <th>PELABUHAN</th>
																						<th>KELAS KAPAL</th>
																						<th>TIPE LAPORAN</th>
                                            <th>CREATED ON</th>
                                            <th>STATUS</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tfoot></tfoot>
                                </table>
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

<script type="text/javascript">

var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('monitoring/sap_summary') ?>",
                "type": "POST",
                "data": function(d) {
                    d.dateTo = document.getElementById('dateTo').value;
                    d.dateFrom = document.getElementById('dateFrom').value;
										d.port = document.getElementById('port').value;
										d.ship_class = document.getElementById('ship_class').value;
										d.type = document.getElementById('type').value;

                },
            },


         
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "text", "orderable": true, "className": "text-left"},
                    {"data": "pendapatan", "orderable": true, "className": "text-right"},
                    {"data": "shift_date", "orderable": true, "className": "text-left"},
                    {"data": "shift_name", "orderable": true, "className": "text-left"},
                    {"data": "port", "orderable": true, "className": "text-left"},
                    {"data": "ship_class", "orderable": true, "className": "text-left"},
										{"data": "type", "orderable": true, "className": "text-left"},
                    {"data": "created_on", "orderable": true, "className": "text-left"},
                    {"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "actions", "orderable": true, "className": "text-left"},
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
                    }
                });
            },

            fnDrawCallback: function(allRow)
            {
                //console.log(allRow);
                if(allRow.json.recordsTotal)
                {
                    $('.download').prop('disabled',false);
                }
                else
                {
                    $('.download').prop('disabled',true);
                }

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
            },
            "columnDefs" : [
                {
                    targets : [3],
                    visible:<?php echo $gs ?>

                }
           ],


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

    
    jQuery(document).ready(function () {
        table.init();

        // $("#download_excel").click(function(event){
        //     var dateFrom=$("#dateFrom").val();
        //     var dateTo=$("#dateTo").val();
        //     var search= $('.dataTables_filter input').val();

        //     window.location.href="<?php echo site_url('log/log_transaction/download_excel?') ?>dateFrom="+dateFrom+"&dateTo="+dateTo+"&search="+search;
        // });


        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: new Date(),
        });

        $("#dateTo").change(function(){
            table.reload();
        });

        $("#dateFrom").change(function(){
            table.reload();
        });

        $("#port").change(function(){
            table.reload();
        });

				$("#ship_class").change(function(){
            table.reload();
        });

				$("#type").change(function(){
            table.reload();
        });
        
    });
</script>
