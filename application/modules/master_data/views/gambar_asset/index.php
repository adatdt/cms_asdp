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
                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
																<th>MODULE</th>
                                <th>NAMA</th>
                                <th>PATH</th>
																<th>DESC</th>
                                <th>STATUS</th>
                                <!-- <th>ORDER</th> -->
                                <th>URL TARGET</th>
																<th>GAMBAR</th>
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
<!-- <div class="modal my-modal" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="modal-info-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <center><img id="image-info" src="<?php echo base_url('assets/img/loader.gif') ?>" alt="info layanan dan tarif" style="max-width: 100%;max-height:100%"></center>
            </div>
            <div class="modal-footer">
                <div class="modal-tools">
                    <button class="btn my-btn-primary my-semi-bold" data-dismiss="modal" aria-hidden="true">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div id="tallModal" class="modal modal-wide fade">
    <div class="modal-dialog" style="width:100%">
         <!-- <h4 id='namaaplikasi'>Foto Barang</h4> </br> -->
             <div id="photoss"></div>
            
    </div><!-- /.modal -->
    <div class="modal-footer">
                <div class="modal-tools">
                    <button class="btn btn-sm btn-primary" data-dismiss="modal" aria-hidden="true">Tutup</button>
                </div>
            </div>
</div>

<script>
    $(document).on(" click", "#detailgambar", function() {
        var image = $(this).data('image');
        var url_image = `<?php echo base_url() ?>${image}`;
        // $(".modal-body #image-info").attr("src", url_image);
        $('#photoss').html('<img src="'+url_image+'" class="img-responsive">');
        $("#tallModal").modal("show");
    });

    $("#modal-info").on('hidden.bs.modal', function(e) {
        $(".modal-body #image-info").attr("src", `<?php echo base_url() ?>assets/img/loader.gif`);
    });

    // $('#show_data').on("click",".tt",function(){

    // var ss  =    $(this).attr("photoss");
    // var base_url = "http://localhost/barang/upload/"+ss;
    // console.log(base_url)

    // $('#photoss').html('<img src="'+base_url+'" class="img-responsive">');


    // $("#tallModal").modal("show");

    // });
</script>
<script type="text/javascript">

var table= {
    loadData: function() {
        $('#dataTables').DataTable({
            "ajax": {
                "url": "<?php echo site_url('master_data/gambar_asset') ?>",
                "type": "POST",
                "data": function(d) {
                    // d.port = document.getElementById('port').value;
                    // d.team = document.getElementById('team').value;
                    // console.log(d)
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                    {"data": "no", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "module", "orderable": true, "className": "text-left"},
                    {"data": "name", "orderable": true, "className": "text-left"},
                    {"data": "path", "orderable": true, "className": "text-left"},
										{"data": "desc", "orderable": true, "className": "text-left"},
                    {"data": "active", "orderable": true, "className": "text-center"},
                    // {"data": "order", "orderable": false, "className": "text-center" , "width": 5},
                    {"data": "url_target", "orderable": true, "className": "text-center", "width": 5},
										{"data": "detail", "orderable": true, "className": "text-center"},
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

        $("#port").on("change",function(){
            table.reload();
        });

        $("#team").on("change",function(){
            table.reload();
        });


        
    });

</script>
