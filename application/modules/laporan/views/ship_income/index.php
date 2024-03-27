<div class="page-content-wrapper">
    <div class="page-content">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $title; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline">
                    <div class="input-group">
                        <div class="input-group-addon">Tanggal Keberangkatan</div>
                        <input class="form-control input-small date" id="datefrom" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">Sampai Tanggal</div>
                        <input class="form-control input-small date" id="dateto" placeholder="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                    <button type="button" class="btn btn-info" id="cari" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Mencari...">cari</button>
                    <b id="total"></b>
                </div>                
                <table class="table table-bordered table-hover table-striped" id="tblshipincome">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kapal</th>
                            <th>Waktu <br>Berangkat</th>
                            <th>Total <br> penumpang </th>
                            <th>Pendapatan<br> Penumpang (Rp.)</th>
                            <th>Total <br>Kendaraan</th>
                            <th>Pendapatan <br>Kendaraan (Rp.)</th>
                            <th>Total (Rp.)</th>
                            <th>Action</th>
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
var shipincome= {
    loadData: function() {
        var numericColumn = [3, 4, 5, 6, 7];
        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, column, row, node) {
                        return numericColumn.indexOf(column) >= 0 ? parseInt(data.toString().replace(/\./g, '')) : data;
                    }
                }
            }
        };

        $('#tblshipincome').DataTable({
            "ajax": {
                "url": "<?php echo site_url('laporan/ship_income') ?>",
                "type": "POST",
                "data": function(d) {
                    d.datefrom= document.getElementById('datefrom').value;
                    d.dateto = document.getElementById('dateto').value;
                },
            },

            "serverSide": true,
            "processing": true,
            "columns": [
                {"data": "number", "orderable": false, "searchable": false, "className": "text-center"},
                {"data": "ship_name", "orderable": true},
                {"data": "schedule_date", "orderable": true},
                {"data": "countpassanger","orderable": true,"className": "text-center"},
                {"data": "sum_passanger","orderable":true,"className": "text-right"},
                {"data": "countvehicle","orderable":true,"className": "text-center"},
                {"data": "sum_vehicle","orderable":true,"className": "text-right"},
                {"data": "total","orderable":true,"className": "text-right"},
                {"data": "actions","orderable":false,"className": "text-center"},
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

            "bStateSave": true,
            "searching":false,
            "pageLength": -1,
            "pagingType": "bootstrap_full_number",
            "paging": false,
            "order": [[2, "desc" ]],

            "fnDrawCallback": function(data) {
                $('#total').html('<h4>Total Pendapatan Rp.'+data.json.total+'</h4>');
            },

            "initComplete": function (data) {
                var $searchInput = $('div.dataTables_filter input');
                var data_tables = $('#tblshipincome').DataTable();
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
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    }),
                <?php } ?>

                <?php if($download_pdf){ ?>
                    $.extend(true, {}, {}, {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        exportOptions: {
                            columns: ':not(:last-child)',
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = [20, 100, 120, 100, 90, 100, 100, 75];
                            var iColumns = $('#tblshipincome thead th').length;
                            var rowCount = document.getElementById("tblshipincome").rows.length;

                            for (i = 0; i < rowCount; i++) {
                                doc.content[1].table.body[i][0].alignment = 'center';
                                doc.content[1].table.body[i][3].alignment = 'center';
                                doc.content[1].table.body[i][4].alignment = 'right';
                                doc.content[1].table.body[i][5].alignment = 'center';
                                doc.content[1].table.body[i][6].alignment = 'right';
                                doc.content[1].table.body[i][7].alignment = 'right';

                            }
                        }
                    }),
                <?php } ?>
            ],
        });

        $('#export_tools  > a.tool-action').on('click', function() {
            var data_tables = $('#tblshipincome').DataTable();
            var action = $(this).attr('data-action');

            data_tables.button(action).trigger();
        });
    },

    reload: function() {
        $('#tblshipincome').DataTable().ajax.reload();
    },

    init: function() {
        if (!jQuery().DataTable) {
            return;
        }

        this.loadData();
    }
};

jQuery(document).ready(function () {
    shipincome.init();

    $('#datefrom').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#dateto').datepicker('setStartDate', e.date)
    });

    $('#dateto').datepicker({
        format: 'yyyy-mm-dd',
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        startDate: $('#datefrom').val(),
        endDate: new Date(),
    }).on('changeDate',function(e) {
        $('#datefrom').datepicker('setEndDate', e.date)
    });
    
    $("#cari").on("click",function(){
        $(this).button('loading');
        shipincome.reload();
        $('#tblshipincome').on('draw.dt', function() {
            $("#cari").button('reset');
        });
    });

    setTimeout(function() {
        $('.menu-toggler').trigger('click');
    }, 1);
});
</script>