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
                    <div class="pull-right btn-add-padding">
                        <!-- <?php echo $btn_add; ?> -->
                        <?= $btn_excel." ".$btn_add; ?>
                    </div>
                </div>

                <div class="portlet-body">
<!--                     <div class="table-toolbar">
                        <div class="row">
                            <div class="col-sm-12 form-inline">

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan Keberangakatan</div>
                                    <select id="port_origin" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_origin">
                                        <?php if(!empty($this->session->userdata("port_id"))) {} else { ?>
                                        <option value="">Pilih</option>
                                        <?php } foreach($port as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Pelabuhan Tujuan</div>
                                    <select id="port_destination" class="form-control js-data-example-ajax select2 input-small" dir="" name="port_destination">
                                        <option value="">Pilih</option>
                                        <?php foreach($destination as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="input-group select2-bootstrap-prepend">
                                    <div class="input-group-addon">Tipe Kapal</div>
                                    <select id="ship_class" class="form-control js-data-example-ajax select2 input-small" dir="" name="ship_class">
                                        <option value="">Pilih</option>
                                        <?php foreach($ship_class as $key=>$value ) {?>
                                        <option value="<?php echo $this->enc->encode($value->id); ?>"><?php echo strtoupper($value->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>        

                            </div>

                        </div>
                    </div> -->

                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>

                                <th>NO</th>
                                <th>KODE SCHEMA</th>
                                <th>NAMA SCHEMA</th>
                                <th>KODE DISKON</th>
                                <th>PELABUHAN</th>
                                <th>NAMA PROMO</th>
                                <th>KONFIGURASI BERLAKU</th>
                                <th>AWAL BERLAKU</th>
                                <th>AKHIR BERLAKU</th>
                                <th>POS <br>PENUMPANG</th>
                                <th>POS <br>KENDARAAN</th>
                                <th>VM</th>
                                <th>MOBILE</th>
                                <th>WEB</th>
                                <th>B2B</th>
                                <th>IFCS</th>
                                <th>STATUS</th>
                                <th>

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                AKSI
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </th>
                            </tr>
                        </thead>
                    </table>
                </div>   
            </div>
        </div>
    </div>
    <input type="hidden" id="tokenHash" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
                "url": "<?php echo site_url('fare/discount') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port_destination = document.getElementById('port_destination').value;
                    // d.port_origin = document.getElementById('port_origin').value;
                    // d.ship_class = document.getElementById('ship_class').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "schema_code", "orderable": true, "className": "text-left"},
                    {"data": "schema_name2", "orderable": true, "className": "text-left"},
                    {"data": "discount_code", "orderable": true, "className": "text-left"},
                    {"data": "port_name", "orderable": true, "className": "text-left"},
                    {"data": "description", "orderable": true, "className": "text-left"},
                    {"data": "active_reservation_date", "orderable": true, "className": "text-left"},
                    {"data": "start_date", "orderable": true, "className": "text-left"},
                    {"data": "end_date", "orderable": true, "className": "text-left"},
                    {"data": "pos_passanger", "orderable": true, "className": "text-center"},
                    {"data": "pos_vehicle", "orderable": true, "className": "text-center"},
                    {"data": "vm", "orderable": true, "className": "text-center"},
                    {"data": "mobile", "orderable": true, "className": "text-center"},
                    {"data": "web", "orderable": true, "className": "text-center"},
                    {"data": "b2b", "orderable": true, "className": "text-center"},
                    {"data": "ifcs", "orderable": true, "className": "text-center"},
                    {"data": "status", "orderable": true, "className": "text-center"},
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

        $("#port_origin").on("change",function(){
            table.reload();
        });

        $("#port_destination").on("change",function(){
            table.reload();
        });

        $("#ship_class").on("change",function(){
            table.reload();
        });

        $("#download_excel").click(function(event) {                 
            window.location.href = "<?php echo site_url('fare/discount/download_excel?') ?>";
        });


        
    });

</script>
