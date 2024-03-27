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

        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                    	<div class="row">
                    		<div class="col-sm-12">
                    			<div class="col col-md-5">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">LICENSE NUMBER</div>
                                        <input type="text" name="license_number" id="license_number" class="form-control" placeholder="Filter LICENSE NUMBER">
                    				</div> 
                    			</div>
                                <div class="col col-md-3">
                    				<div class="input-group select2-bootstrap-prepend">
                    					<div class="input-group-addon">IMEI</div>
                                        <input type="text" name="imei" id="imei" class="form-control" placeholder="Filter IMEI">
                    				</div> 
                    			</div>
                    		</div>
                    	</div>
                    </div>

                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NOMOR LISENSI</th>
                                <th>IMEI</th>
                                <th>DIBUAT OLEH</th>
                                <th>TANGGAL DIBUAT</th>
                                <th>STATUS</th>
                                <th>AKSI</th> 
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('device_management/master_license') ?>",
                "type": "POST",
                "data": function(d) {
                    d.license_number = document.getElementById('license_number').value;
                    d.imei = document.getElementById('imei').value;
                    console.log(d)
                },
            },
            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "license_number", "orderable": false, "className": "text-center"},
                    {"data": "imei", "orderable": false, "className": "text-center"},
                    {"data": "created_by", "orderable": false, "className": "text-center"},
                    {"data": "created_on", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": false, "className": "text-center"},
                    {"data": "actions", "orderable": false, "className": "text-center"},
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

    $("#license_number").on("change",function(){
        table.reload();
    });

    $("#imei").on("change",function(){
        table.reload();
    });
});
</script>
