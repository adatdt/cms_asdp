<style>
    .table .btn {
        margin: 2.5px;
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
                    <script type="text/javascript">
                        window.onload = date_time('datetime');
                    </script>
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
                            <div class="col-sm-12 form-inline">
                                <div class="input-group pad-top">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id='btnData'>Tipe
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Tipe','type')">Tipe</a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Judul Info','titleText')">Judul Popup</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Info','info')">Info Peringatan</a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="myData.changeSearch('Instruksi','instruction')">Instruksi</a>
                                            </li>

                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Cari Data" data-name="type" name="searchData" id="searchData">
                                </div>
                                <div class="input-group pad-top">
                                    <button type="button" class="btn btn-danger mt-ladda-btn ladda-button" data-style="zoom-in" id="cari">
                                        <span class="ladda-label">Cari</span>
                                        <span class="ladda-spinner"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p></p>
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dataTables">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>TIPE</th>
                                <th>JUDUL POP-UP</th>
                                <th>INSTRUKSI</th>
                                <th>INFO PERINGATAN</th>
                                <th>TIPE GRUP</th>
                                <th>STATUS</th>
                                <th width="120px">AKSI</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "fileJs.php" ?>

<script type="text/javascript">    
    var csfrData = {};
    csfrData[`<?php echo $this->security->get_csrf_token_name(); ?>`] =`<?php echo $this->security->get_csrf_hash(); ?>`;
    $.ajaxSetup({
        data: csfrData
    });
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function replaceStyle(text)
    {
        return btoa(text)
    }    
    function replaceStyle_24052022(text)
    {
        return text.replaceAll('style', 'monkey-1777')
    }

    let myData = new MyData();

    // function showModal2(url) {
    //     if (!mfp.isOpen) {
    //         mfp.open({
    //             items: {
    //                 src: url
    //             },
    //             modal: true,
    //             type: 'ajax',
    //             tLoading: '<i class="fa fa-refresh fa-spin"></i> Mohon tunggu...',
    //             showCloseBtn: false
    //         });
    //     }
    // }

function showModal2(url) {
    if (!mfp.isOpen) {
        mfp.open({
            items: {
                src: url
            },
            modal: true,
            type: 'ajax',
            tLoading: '<i class="fa fa-refresh fa-spin"></i> Mohon tunggu...',
            showCloseBtn: false,
            fixedContentPos: false,
            callbacks: {
                open: function () {
                    $('.mfp-wrap').css("overflow", "initial")
                    $('.mfp-wrap').removeAttr('tabindex')
                },
            },
        });
    }
}    

    var table = {
        loadData: function() {
            $('#dataTables').DataTable({
                "ajax": {
                    "url": "<?php echo site_url('master_data/assessmentParam') ?>",
                    "type": "POST",
                    "data": function(d) {
                        d.searchData = document.getElementById('searchData').value;
                        d.searchName = $("#searchData").attr('data-name');
                    },
                },

                "serverSide": true,
                "filter": false,
                "processing": true,
                "columns": [{
                        "data": "no",
                        "orderable": false,
                        "className": "text-center",
                        "width": 5
                    },
                    {
                        "data": "type",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "title_text",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "instructions_text",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "info_text",
                        "orderable": true,
                        "className": "text-left"
                    },
                    {
                        "data": "group_type",
                        "orderable": true,
                        "className": "text-left"
                    },                    
                    {
                        "data": "status",
                        "orderable": true,
                        "className": "text-center"
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "className": "text-center"
                    },
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
                "order": [
                    [0, "desc"]
                ],
                "initComplete": function() {
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#dataTables').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function(e) {
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

    jQuery(document).ready(function() {
        table.init();

        $("#cari").on("click", function() {
            $(this).button('loading');
            table.reload();
            $('#dataTables').on('draw.dt', function() {
                $("#cari").button('reset');
            });
        })



    });
</script>