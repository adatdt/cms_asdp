<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue-madison">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>
                </div>
                <div class="pull-right btn-add-padding">
                    <a href="<?php echo site_url('laporan/ship_income') ?>" class="btn btn-sm btn-warning">Kembali</a>
                </div>
            </div>
            <div class="portlet-body" style="padding-top: 0px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light" style="padding: 1px">
                            <div class="portlet-body">
                                <div class="row number-stats margin-bottom-30">
                                    <div class="col-md-4">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Nama Kapal </div>
                                                <div class="my-number"> <?php echo $header->name ?> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Tanggal Keberangkatan </div>
                                                <div class="my-number"> <?php echo format_dateTime($header->depart_date) ?> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-right">
                                            <div class="stat-number">
                                                <div class="title"> Total Pendapatan </div>
                                                <div class="my-number" id="total"> </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-striped" id="detail">
                    <thead>
                        <tr>
                            <th> No </th>
                            <th> Tanggal Keberangkatan </th>
                            <th> Nama Kapal </th>
                            <th> Golongan </th>
                            <th> Produksi </th>
                            <th> Pendapatan (Rp) </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var detail = {
        loadData: function() {
            var numericColumn = [4,5];
            var buttonCommon = {
                exportOptions: {
                    format: {
                        body: function(data, column, row, node) {
                            return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
                        }
                    }
                }
            };

            $('#detail').DataTable({
                "ajax": {
                    "url": "<?php echo $url ?>",
                    "type": "POST",
                },

                "serverSide": true,
                "processing": true,
                "columns": [
                    {"data": "number", "orderable": false, "className": "text-center", "width": 30},
                    {"data": "date", "orderable": false},
                    {"data": "ship", "orderable": false},
                    {"data": "name", "orderable": true},
                    {"data": "production", "orderable": true,"className": "text-center"},
                    {"data": "income", "orderable": true,"className": "text-right"},
                ],

                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending",
                    },
                    "processing": "Proses.....",
                    "emptyTable": "Tidak ada data",
                    "info": "Total _TOTAL_ data",
                    "infoEmpty": "Total 0 data",
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

                "searching":false,
                "pageLength": -1,
                "pagingType": "bootstrap_full_number",
                "order": [[3, "asc" ]],
                "paging": false,

                "initComplete": function (data) {
                    $('#total').html('Rp.'+data.json.total);
                    var $searchInput = $('div.dataTables_filter input');
                    var data_tables = $('#detail').DataTable();
                    $searchInput.unbind();
                    $searchInput.bind('keyup', function (e) {
                        if (e.keyCode == 13 || e.whiche == 13) {
                            data_tables.search(this.value).draw();
                        }
                    });
                }, 

                dom: 'B<"table-scrollable"t>ri',
                "buttons": [
                    <?php if($download_excel){ ?>
                        $.extend(true, {}, buttonCommon, {
                            extend: 'excelHtml5',
                        }),
                    <?php } ?>

                    <?php if($download_pdf){ ?>
                        $.extend(true, {}, {}, {
                            extend: 'pdfHtml5',
                            customize: function(doc) {
                                doc.content[1].table.widths = [20, 100, 110, 75, 75, 100];
                                var iColumns = $('#detail thead th').length;
                                var rowCount = document.getElementById("detail").rows.length;

                                for (i = 0; i < rowCount; i++) {
                                    doc.content[1].table.body[i][0].alignment = 'center';
                                    doc.content[1].table.body[i][4].alignment = 'center';
                                    doc.content[1].table.body[i][5].alignment = 'right';

                                }
                            }
                        }),
                    <?php } ?>
                ],
            });

            $('#export_tools  > a.tool-action').on('click', function() {
                var data_tables = $('#detail').DataTable();
                var action = $(this).attr('data-action');

                data_tables.button(action).trigger();
            });
        },

        reload: function() {
            $('#detail').DataTable().ajax.reload();
        },

        init: function() {
            if (!jQuery().DataTable) {
                return;
            }
            this.loadData();
        }
    };

    jQuery(document).ready(function () {
        detail.init();
    });
</script>