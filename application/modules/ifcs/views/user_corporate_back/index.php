<style type="text/css">
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        /*width: 800px;*/
        margin: 0 auto;
    }

    div.DTFC_LeftBodyWrapper table {
        /*border-top: none;*/
        margin-bottom: 0 !important;
        
        }
    .DTFC_LeftBodyWrapper
    {
        margin-top : -10px !important;
    }        
#dataTables_processing
{
    z-index: 1;
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


        <div class="my-div-body">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    
                    <div class="caption"><?php echo $title ?></div>
                    <div class="pull-right btn-add-padding"><?php echo $btn_add; ?></div>
                </div>
                <div class="portlet-body">
<!--                     <div class="form-inline">
                        <div class="row">
                            <div class="col-md-12"> 
                                
                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan</div>
                                    <select id="port" class="form-control js-data-example-ajax select2" dir="" name="port">
                                        <option value="">Pilih</option>
                                        <?php foreach($port as $key=>$value ) { ?>
                                            <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Regu</div>
                                    <select id="team" class="form-control js-data-example-ajax select2" dir="" name="team">
                                        <option value="">Pilih</option>
                                        <?php foreach($team as $key=>$value ) { ?>
                                        <option value="<?php echo $value->team_name; ?>"><?php echo $value->team_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>    
                         
                            </div>
                            <div class="col-md-12"></div>
                        </div>

                    </div> -->


                    <p></p>


                    <!-- <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables"> -->
                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>

                            <tr>
                                <th>NO</th>
                                <th>NAMA CORPORATE</th>
                                <th>KODE CORPORATE</th>
                                <th>TIPE USER</th>
                                <th>NAMA</th>
                                <th>NIK</th>
                                <th>NIP</th>
                                <th>NO TELPON</th>
                                <th>EMAIL</th>
                                <th>CABANG</th>
                                <th>JABATAN</th>
                                <th>BOOKING</th>
                                <th>REEDEM</th>
                                <th>TOPUP DEPOSIT</th>
                                <th>PURCHASE DEPOSIT</th>
                                <th>CASH OUT DEPOSIT</th>
                                <th>AKTIVASI</th>
                                <th>STATUS</th>
                                <th>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    AKSI
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </th> 
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
                "url": "<?php echo site_url('ifcs/user_corporate') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "corporate_name", "orderable": true, "className": "text-left"},
                    {"data": "corporate_code", "orderable": true, "className": "text-left"},
                    {"data": "member_type_name", "orderable": true, "className": "text-left"},
                    {"data": "name", "orderable": true, "className": "text-left"},
                    {"data": "nik", "orderable": true, "className": "text-left"},
                    {"data": "nip", "orderable": true, "className": "text-left"},
                    {"data": "phone", "orderable": true, "className": "text-left"},
                    {"data": "email", "orderable": true, "className": "text-left"},
                    {"data": "branch_name", "orderable": true, "className": "text-left"},
                    {"data": "position", "orderable": true, "className": "text-left"},
                    {"data": "booking", "orderable": true, "className": "text-center"},
                    {"data": "redeem", "orderable": true, "className": "text-center"},
                    {"data": "topup_deposit", "orderable": true, "className": "text-center"},
                    {"data": "deposit", "orderable": true, "className": "text-center"},
                    {"data": "cash_out_deposit", "orderable": true, "className": "text-center"},
                    {"data": "is_activation", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
                    {"data": "actions", "orderable": false, "className": "text-center"},
            ],
            scrollX:        true,
            paging:         true,
            fixedColumns:   {
                leftColumns: 5
            },            
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

        $("#port").on("change",function(){
            table.reload();
        });

        $("#team").on("change",function(){
            table.reload();
        });


        
    });

</script>
